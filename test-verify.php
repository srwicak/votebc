<?php

// Simple database check script
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Bootstrap the application
require_once FCPATH . 'vendor/autoload.php';

// Load the environment file  
$dotenv = \Dotenv\Dotenv::createImmutable(FCPATH);
if (file_exists(FCPATH . '.env')) {
    $dotenv->load();
}

// Initialize the database manually
$config = new \Config\Database();
$db = \CodeIgniter\Database\Config::connect($config->default);

try {
    echo "Checking for vote with ID 79...\n";
    
    // Check if vote exists
    $vote = $db->table('votes')->where('id', 79)->get()->getRowArray();
    if (!$vote) {
        echo "Vote with ID 79 not found!\n";
        
        // Get the latest vote IDs
        $latestVotes = $db->table('votes')->orderBy('id', 'DESC')->limit(10)->get()->getResultArray();
        echo "Latest vote IDs: ";
        foreach ($latestVotes as $v) {
            echo $v['id'] . " ";
        }
        echo "\n";
        exit;
    }
    
    echo "Vote found: " . json_encode($vote) . "\n";
    
    // Check blockchain transaction
    $blockchainVote = $db->table('blockchain_transactions')->where('vote_id', 79)->get()->getRowArray();
    if (!$blockchainVote) {
        echo "Blockchain transaction for vote 79 not found!\n";
        exit;
    }
    
    echo "Blockchain transaction found: " . json_encode($blockchainVote) . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
