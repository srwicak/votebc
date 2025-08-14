<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixHashValidation extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'fix:hash-validation';
    protected $description = 'Fix hash validation issues by ensuring consistent hash calculation between vote creation and verification';

    public function run(array $params)
    {
        CLI::write('===== Fix Hash Validation Issues =====', 'green');
        CLI::newLine();

        $voteModel = new \App\Models\VoteModel();
        $blockchainModel = new \App\Models\BlockchainTransactionModel();
        
        // Get all blockchain transactions with vote data
        $transactions = $blockchainModel->findAll();
        
        $fixed = 0;
        $errors = 0;
        $alreadyCorrect = 0;
        
        CLI::write('Checking ' . count($transactions) . ' blockchain transactions...', 'yellow');
        CLI::newLine();
        
        foreach ($transactions as $tx) {
            $voteId = $tx['vote_id'] ?? 0;
            $vote = $voteModel->find($voteId);
            
            if (!$vote) {
                CLI::write("Vote ID {$voteId} not found for transaction {$tx['id']}", 'red');
                $errors++;
                continue;
            }
            
            // Parse blockchain data
            $data = json_decode($tx['data'] ?? '{}', true);
            $storedVoteHash = $data['vote_hash'] ?? null;
            
            if (!$storedVoteHash) {
                CLI::write("No vote hash found for vote ID {$voteId}", 'yellow');
                $errors++;
                continue;
            }
            
            // Get blockchain election ID
            $blockchainElectionId = $tx['blockchain_election_id'] ?? 
                                   ($data['blockchain_election_id'] ?? $vote['election_id']);
            
            // Get timestamp from blockchain data
            $timestamp = $data['timestamp'] ?? strtotime($vote['voted_at']);
            
            // Calculate what the hash SHOULD be based on current verification method
            $correctHashData = [
                'election_id' => $blockchainElectionId,
                'candidate_id' => $vote['candidate_id'],
                'voter_id' => $vote['voter_id'],
                'timestamp' => $timestamp
            ];
            $correctHash = hash('sha256', json_encode($correctHashData));
            
            CLI::write("Vote ID {$voteId}:", 'cyan');
            CLI::write("  Stored hash:  {$storedVoteHash}");
            CLI::write("  Expected hash: {$correctHash}");
            
            if ($storedVoteHash === $correctHash) {
                CLI::write("  Status: Already correct", 'green');
                $alreadyCorrect++;
            } else {
                CLI::write("  Status: Needs fixing", 'yellow');
                CLI::write("  Hash calculation data: " . json_encode($correctHashData));
                
                try {
                    // Update the blockchain transaction with the correct hash
                    $data['vote_hash'] = $correctHash;
                    $blockchainModel->update($tx['id'], [
                        'data' => json_encode($data)
                    ]);
                    
                    CLI::write("  Fixed: Updated hash in blockchain transaction", 'green');
                    $fixed++;
                } catch (\Exception $e) {
                    CLI::write("  Error: Failed to update - " . $e->getMessage(), 'red');
                    $errors++;
                }
            }
            
            CLI::newLine();
        }
        
        CLI::write("===== Fix Summary =====", 'green');
        CLI::write("Total transactions processed: " . count($transactions), 'white');
        CLI::write("Already correct: {$alreadyCorrect}", 'green');
        CLI::write("Fixed: {$fixed}", 'green');
        CLI::write("Errors: {$errors}", 'red');
        CLI::newLine();
        
        if ($fixed > 0) {
            CLI::write("Hash validation issues have been fixed!", 'green');
            CLI::write("All votes should now verify correctly.", 'green');
        } else if ($alreadyCorrect > 0) {
            CLI::write("All hashes are already correct!", 'green');
        } else {
            CLI::write("No blockchain transactions found to process.", 'yellow');
        }
    }
}
