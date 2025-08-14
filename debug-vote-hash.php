<?php

// Simple debug script to check vote hash for vote ID 8
try {
    // Connect to database directly
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bcvt;charset=utf8', 'root', 'toortoor');
    
    $stmt = $pdo->prepare('SELECT v.*, bt.blockchain_election_id, bt.data, bt.tx_hash FROM votes v LEFT JOIN blockchain_transactions bt ON v.id = bt.vote_id WHERE v.id = 8');
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        echo "Vote ID 8 not found!\n";
        exit;
    }
    
    echo "=== VOTE DATA ===\n";
    echo "Vote ID: " . $result['id'] . "\n";
    echo "Election ID: " . $result['election_id'] . "\n";
    echo "Candidate ID: " . $result['candidate_id'] . "\n";
    echo "Voter ID: " . $result['voter_id'] . "\n";
    echo "Voted At: " . $result['voted_at'] . "\n";
    echo "Blockchain Election ID: " . $result['blockchain_election_id'] . "\n";
    echo "\n";
    
    if ($result['data']) {
        $blockchainData = json_decode($result['data'], true);
        echo "=== BLOCKCHAIN DATA ===\n";
        print_r($blockchainData);
        echo "\n";
        
        // Extract relevant data
        $voteHash = $blockchainData['vote_hash'] ?? 'NOT_SET';
        $timestamp = $blockchainData['timestamp'] ?? strtotime($result['voted_at']);
        $blockchainElectionId = $result['blockchain_election_id'] ?? $blockchainData['blockchain_election_id'] ?? $result['election_id'];
        
        echo "=== HASH VERIFICATION ===\n";
        echo "Stored vote hash: " . $voteHash . "\n";
        echo "Timestamp used: " . $timestamp . " (" . date('Y-m-d H:i:s', $timestamp) . ")\n";
        echo "Blockchain election ID: " . $blockchainElectionId . "\n";
        echo "\n";
        
        // Try different hash calculations
        echo "=== HASH CALCULATIONS ===\n";
        
        // 1. Using blockchain election ID and timestamp from data
        $hashData1 = [
            'election_id' => $blockchainElectionId,
            'candidate_id' => $result['candidate_id'],
            'voter_id' => $result['voter_id'],
            'timestamp' => $timestamp
        ];
        $hash1 = hash('sha256', json_encode($hashData1));
        echo "Hash 1 (blockchain election ID + blockchain timestamp): " . $hash1 . "\n";
        echo "Data: " . json_encode($hashData1) . "\n";
        echo "Matches stored: " . ($hash1 === $voteHash ? 'YES' : 'NO') . "\n\n";
        
        // 2. Using database election ID and timestamp from data
        $hashData2 = [
            'election_id' => $result['election_id'],
            'candidate_id' => $result['candidate_id'],
            'voter_id' => $result['voter_id'],
            'timestamp' => $timestamp
        ];
        $hash2 = hash('sha256', json_encode($hashData2));
        echo "Hash 2 (database election ID + blockchain timestamp): " . $hash2 . "\n";
        echo "Data: " . json_encode($hashData2) . "\n";
        echo "Matches stored: " . ($hash2 === $voteHash ? 'YES' : 'NO') . "\n\n";
        
        // 3. Using blockchain election ID and voted_at timestamp
        $hashData3 = [
            'election_id' => $blockchainElectionId,
            'candidate_id' => $result['candidate_id'],
            'voter_id' => $result['voter_id'],
            'timestamp' => strtotime($result['voted_at'])
        ];
        $hash3 = hash('sha256', json_encode($hashData3));
        echo "Hash 3 (blockchain election ID + voted_at timestamp): " . $hash3 . "\n";
        echo "Data: " . json_encode($hashData3) . "\n";
        echo "Matches stored: " . ($hash3 === $voteHash ? 'YES' : 'NO') . "\n\n";
        
        // 4. Using database election ID and voted_at timestamp
        $hashData4 = [
            'election_id' => $result['election_id'],
            'candidate_id' => $result['candidate_id'],
            'voter_id' => $result['voter_id'],
            'timestamp' => strtotime($result['voted_at'])
        ];
        $hash4 = hash('sha256', json_encode($hashData4));
        echo "Hash 4 (database election ID + voted_at timestamp): " . $hash4 . "\n";
        echo "Data: " . json_encode($hashData4) . "\n";
        echo "Matches stored: " . ($hash4 === $voteHash ? 'YES' : 'NO') . "\n\n";
        
    } else {
        echo "No blockchain data found for this vote.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
