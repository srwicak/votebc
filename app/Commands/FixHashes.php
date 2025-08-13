<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixHashes extends BaseCommand
{
    protected $group       = 'Blockchain';
    protected $name        = 'fix:hashes';
    protected $description = 'Fix vote hashes in existing blockchain transactions to ensure verification works';
    
    public function run(array $params)
    {
        $blockchain = new \App\Libraries\Blockchain();
        $blockchainModel = new \App\Models\BlockchainTransactionModel();
        $voteModel = new \App\Models\VoteModel();
        
        CLI::write('===== Fixing Vote Hashes =====', 'green');
        CLI::newLine();
        
        // Get all blockchain transactions for votes
        $transactions = $blockchainModel->where('tx_type', 'vote')->findAll();
        
        CLI::write('Found ' . count($transactions) . ' vote transactions to check', 'yellow');
        CLI::newLine();
        
        $fixed = 0;
        $errors = 0;
        
        foreach ($transactions as $tx) {
            // Get the vote
            $vote = $voteModel->find($tx['vote_id'] ?? 0);
            if (!$vote) {
                CLI::write("No vote found for transaction ID {$tx['id']}", 'red');
                $errors++;
                continue;
            }
            
            // Parse the data
            $data = json_decode($tx['data'] ?? '{}', true);
            
            // Get the blockchain election ID
            $blockchainElectionId = $tx['blockchain_election_id'] ?? 
                                   ($data['blockchain_election_id'] ?? $vote['election_id']);
            
            // Get the timestamp
            $timestamp = $data['timestamp'] ?? strtotime($vote['voted_at']);
            
            // Generate the correct hash
            $hashData = [
                'election_id' => $blockchainElectionId,
                'candidate_id' => $vote['candidate_id'],
                'voter_id' => $vote['voter_id'],
                'timestamp' => $timestamp
            ];
            $correctVoteHash = hash('sha256', json_encode($hashData));
            
            // Get the existing hash
            $existingVoteHash = $data['vote_hash'] ?? null;
            
            if ($existingVoteHash !== $correctVoteHash) {
                CLI::write("Fixing hash for vote ID {$vote['id']}", 'yellow');
                CLI::write("  Old hash: {$existingVoteHash}", 'red');
                CLI::write("  New hash: {$correctVoteHash}", 'green');
                
                // Update the data with the correct hash
                $data['vote_hash'] = $correctVoteHash;
                $blockchainModel->update($tx['id'], [
                    'data' => json_encode($data)
                ]);
                
                $fixed++;
            } else {
                CLI::write("Hash for vote ID {$vote['id']} is already correct", 'green');
            }
        }
        
        CLI::newLine();
        CLI::write("===== Fix Summary =====", 'green');
        CLI::write("Total transactions checked: " . count($transactions), 'white');
        CLI::write("Hashes fixed: {$fixed}", 'green');
        CLI::write("Errors: {$errors}", 'red');
        CLI::newLine();
        
        if ($fixed > 0) {
            CLI::write("Vote hashes have been updated. Verification should now work correctly.", 'green');
        } else {
            CLI::write("No hash fixes needed!", 'green');
        }
    }
}
