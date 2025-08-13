<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixVerification extends BaseCommand
{
    protected $group       = 'Blockchain';
    protected $name        = 'fix:verification';
    protected $description = 'Fix vote verification issues by updating blockchain_election_id in existing votes';
    
    public function run(array $params)
    {
        $blockchain = new \App\Libraries\Blockchain();
        $blockchainModel = new \App\Models\BlockchainTransactionModel();
        $voteModel = new \App\Models\VoteModel();
        
        CLI::write('===== Fixing Vote Verification Issues =====', 'green');
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
            
            // Existing blockchain election ID
            $existingBlockchainId = $tx['blockchain_election_id'] ?? null;
            
            // Get the blockchain election ID from data or generate a new one
            $blockchainElectionId = $data['blockchain_election_id'] ?? null;
            if (!$blockchainElectionId) {
                // Generate a blockchain election ID
                $blockchainElectionId = $blockchain->generateUserElectionId($vote['election_id'], $vote['voter_id']);
                CLI::write("Generated new blockchain election ID {$blockchainElectionId} for vote ID {$vote['id']}", 'yellow');
            }
            
            // If there's no existing blockchain ID, update it
            if (empty($existingBlockchainId) && $blockchainElectionId) {
                CLI::write("Updating transaction ID {$tx['id']} with blockchain election ID {$blockchainElectionId}", 'green');
                
                // Update the blockchain transaction record
                $blockchainModel->update($tx['id'], [
                    'blockchain_election_id' => $blockchainElectionId
                ]);
                
                // Update the data field to include the blockchain election ID
                $data['blockchain_election_id'] = $blockchainElectionId;
                $blockchainModel->update($tx['id'], [
                    'data' => json_encode($data)
                ]);
                
                $fixed++;
            } else {
                CLI::write("Transaction ID {$tx['id']} already has blockchain election ID {$existingBlockchainId}", 'blue');
            }
        }
        
        CLI::newLine();
        CLI::write("===== Fix Summary =====", 'green');
        CLI::write("Total transactions checked: " . count($transactions), 'white');
        CLI::write("Transactions fixed: {$fixed}", 'green');
        CLI::write("Errors: {$errors}", 'red');
        CLI::newLine();
        
        // Add a quick validation check for stored vote hashes
        CLI::write("===== Hash Validation =====", 'green');
        CLI::newLine();
        
        $validHashes = 0;
        $invalidHashes = 0;
        
        foreach ($transactions as $tx) {
            // Get the vote
            $vote = $voteModel->find($tx['vote_id'] ?? 0);
            if (!$vote) {
                continue;
            }
            
            // Parse the data
            $data = json_decode($tx['data'] ?? '{}', true);
            
            // Get the vote hash and other data
            $storedVoteHash = $data['vote_hash'] ?? null;
            if (!$storedVoteHash) {
                continue;
            }
            
            // Get the blockchain election ID
            $blockchainElectionId = $tx['blockchain_election_id'] ?? 
                                   ($data['blockchain_election_id'] ?? $vote['election_id']);
            
            // Get the timestamp
            $timestamp = $data['timestamp'] ?? strtotime($vote['voted_at']);
            
            // Generate the expected hash
            $expectedHash = hash('sha256', json_encode([
                'election_id' => $blockchainElectionId,
                'candidate_id' => $vote['candidate_id'],
                'voter_id' => $vote['voter_id'],
                'timestamp' => $timestamp
            ]));
            
            if ($storedVoteHash === $expectedHash) {
                CLI::write("Valid hash for vote ID {$vote['id']}", 'green');
                $validHashes++;
            } else {
                CLI::write("Invalid hash for vote ID {$vote['id']} - stored: {$storedVoteHash}, expected: {$expectedHash}", 'red');
                $invalidHashes++;
            }
        }
        
        CLI::newLine();
        CLI::write("Valid hashes: {$validHashes}", 'green');
        CLI::write("Invalid hashes: {$invalidHashes}", 'red');
        CLI::newLine();
        
        if ($invalidHashes > 0) {
            CLI::write("Some hashes are still invalid. You may need to run 'fix:hashes' to update them.", 'yellow');
        } else {
            CLI::write("All hashes appear to be valid!", 'green');
        }
    }
}
