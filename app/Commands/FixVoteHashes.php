<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\VoteModel;
use App\Libraries\Encryptor;
use App\Models\CandidateModel;
use App\Models\BlockchainTransactionModel;

class FixVoteHashes extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'votes:fix';
    protected $description = 'Fix existing votes by updating candidate_id and adding candidate_hash';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $encryptor = new Encryptor();
        $voteModel = new VoteModel();
        $candidateModel = new CandidateModel();
        $blockchainModel = new BlockchainTransactionModel();

        // Check if candidate_hash column exists
        if (!$db->fieldExists('candidate_hash', 'votes')) {
            CLI::error('The candidate_hash column does not exist in the votes table. Please run migrations first.');
            return;
        }

        // Step 1: First, we need to check if we have some votes with hashed candidate_ids
        CLI::write('Checking for votes with hashed candidate_ids...', 'yellow');
        
        // Get all votes
        $votes = $voteModel->findAll();
        $needsFixing = 0;
        $hashToId = [];
        
        // Check each vote to see if candidate_id appears to be a hash
        foreach ($votes as $vote) {
            // If candidate_id is a long hexadecimal string, it's probably a hash
            if (preg_match('/^[0-9a-f]{64}$/i', $vote['candidate_id'])) {
                $needsFixing++;
            }
        }
        
        if ($needsFixing === 0) {
            CLI::write('No votes need fixing. All candidate_ids appear to be numerical IDs.', 'green');
            return;
        }
        
        CLI::write("Found $needsFixing votes that need fixing.", 'yellow');
        
        // Step 2: Try to recover the original candidate_ids from blockchain records
        CLI::write('Recovering original candidate_ids from blockchain records...', 'yellow');
        
        $recovered = 0;
        $failed = 0;
        
        // Get all blockchain transactions
        $transactions = $blockchainModel->findAll();
        
        // Build a lookup table of vote_id to candidate_id from blockchain data
        $voteIdToCandidateId = [];
        foreach ($transactions as $tx) {
            if (!empty($tx['vote_id']) && !empty($tx['data'])) {
                $data = json_decode($tx['data'], true);
                if (isset($data['candidate_id'])) {
                    $voteIdToCandidateId[$tx['vote_id']] = $data['candidate_id'];
                }
            }
        }
        
        // Update votes with recovered candidate_ids
        foreach ($votes as $vote) {
            // If candidate_id is a hash (64 hex chars)
            if (preg_match('/^[0-9a-f]{64}$/i', $vote['candidate_id'])) {
                // Store the hash in candidate_hash
                $hash = $vote['candidate_id'];
                
                // Try to find the original candidate_id from blockchain records
                if (isset($voteIdToCandidateId[$vote['id']])) {
                    $originalCandidateId = $voteIdToCandidateId[$vote['id']];
                    
                    // Update the vote
                    $voteModel->update($vote['id'], [
                        'candidate_id' => $originalCandidateId,
                        'candidate_hash' => $hash
                    ]);
                    
                    CLI::write("Updated vote {$vote['id']}: Set candidate_id={$originalCandidateId}, candidate_hash={$hash}", 'green');
                    $recovered++;
                } else {
                    // If we can't find the original candidate_id, check all candidate IDs
                    // Try to find by matching hash with candidates
                    $allCandidates = $candidateModel->findAll();
                    $found = false;
                    
                    foreach ($allCandidates as $candidate) {
                        $candidateHash = $encryptor->hash($candidate['id']);
                        if ($candidateHash === $hash) {
                            // Update the vote
                            $voteModel->update($vote['id'], [
                                'candidate_id' => $candidate['id'],
                                'candidate_hash' => $hash
                            ]);
                            
                            CLI::write("Updated vote {$vote['id']}: Set candidate_id={$candidate['id']}, candidate_hash={$hash}", 'green');
                            $recovered++;
                            $found = true;
                            break;
                        }
                    }
                    
                    if (!$found) {
                        CLI::error("Failed to recover original candidate_id for vote {$vote['id']} with hash {$hash}");
                        $failed++;
                    }
                }
            }
        }
        
        CLI::write("Fix completed: $recovered votes updated, $failed votes could not be fixed.", 'yellow');
    }
}
