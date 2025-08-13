<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestElectionId extends BaseCommand
{
    protected $group       = 'Blockchain';
    protected $name        = 'test:electionid';
    protected $description = 'Test election ID generation to avoid blockchain conflicts';
    
    public function run(array $params)
    {
        $blockchain = new \App\Libraries\Blockchain();
        
        CLI::write('===== Testing Blockchain Election ID Generation =====', 'green');
        CLI::write('This test demonstrates how the system prevents "Already voted" errors after database resets', 'yellow');
        CLI::newLine();
        
        // Simulate multiple database resets with the same election ID
        $elections = [
            ['database_id' => 1, 'title' => 'Election after initial setup'],
            ['database_id' => 1, 'title' => 'Election after first database reset'],
            ['database_id' => 1, 'title' => 'Election after second database reset']
        ];
        
        // Test with different voters
        $voters = [
            ['id' => 42, 'name' => 'John'],
            ['id' => 57, 'name' => 'Jane'],
            ['id' => 99, 'name' => 'Sam']
        ];
        
        // Generate and display unique election IDs
        CLI::write('CURRENT MONTH SALT - Election ID mapping (database_id → blockchain_id):', 'yellow');
        CLI::newLine();
        
        CLI::write(str_pad('Database ID', 15) . str_pad('Voter ID', 10) . str_pad('Voter', 10) . str_pad('Blockchain ID', 20) . 'Salt/Election');
        CLI::write(str_repeat('-', 100));
        
        $currentSalt = "bcvt_salt_" . date('Ym'); // Current month salt
        
        // First with current month salt (default)
        foreach ($voters as $voter) {
            // Generate unique election ID
            $blockchainElectionId = $blockchain->generateUserElectionId(
                1, // Database ID 1
                $voter['id']
            );
            
            // Display the mapping
            CLI::write(
                str_pad(1, 15) .
                str_pad($voter['id'], 10) .
                str_pad($voter['name'], 10) .
                str_pad($blockchainElectionId, 20) .
                $currentSalt . " (current month)"
            );
        }
        CLI::newLine();
        
        // Simulate different months/database resets
        $salts = [
            "bcvt_salt_202401" => "January 2024 (old database)",
            "bcvt_salt_202406" => "June 2024 (previous reset)",
            "bcvt_salt_202502" => "February 2025 (future reset)"
        ];
        
        CLI::write('DIFFERENT SALT VALUES - Same election ID generates different blockchain IDs:', 'yellow');
        CLI::newLine();
        
        CLI::write(str_pad('Database ID', 15) . str_pad('Voter ID', 10) . str_pad('Blockchain ID', 20) . 'Salt/Time Period');
        CLI::write(str_repeat('-', 100));
        
        // Show how same election/voter with different salts gives different IDs
        foreach ($salts as $salt => $description) {
            // Override the salt temporarily
            putenv("blockchain.election_id_salt=$salt");
            
            // Generate unique election ID
            $blockchainElectionId = $blockchain->generateUserElectionId(
                1, // Always database ID 1
                42 // Always voter 42
            );
            
            // Display the mapping
            CLI::write(
                str_pad(1, 15) .
                str_pad(42, 10) .
                str_pad($blockchainElectionId, 20) .
                "$salt - $description"
            );
        }
        
        // Reset the environment
        putenv("blockchain.election_id_salt=");
        
        CLI::newLine();
        CLI::write('Notice how each combination produces a unique blockchain election ID', 'green');
        CLI::write('This ensures that votes for "Election ID 1" after database reset', 'green');
        CLI::write('will not conflict with previous votes for "Election ID 1" before the reset', 'green');
        CLI::newLine();
        
        CLI::write('===== Testing Complete =====', 'green');
    }
}
