<?php

namespace App\Controllers;

class VerificationDebug extends BaseController
{
    /**
     * Debug endpoint for checking vote hash generation consistency
     *
     * @return \CodeIgniter\HTTP\Response
     */
    public function testHash()
    {
        try {
            // Get JSON input or query params
            $json = $this->request->getJSON(true) ?: [];
            
            $electionId = $json['election_id'] ?? $this->request->getGet('election_id') ?? 1;
            $candidateId = $json['candidate_id'] ?? $this->request->getGet('candidate_id') ?? 1;
            $voterId = $json['voter_id'] ?? $this->request->getGet('voter_id') ?? 42;
            $timestamp = $json['timestamp'] ?? $this->request->getGet('timestamp') ?? time();
            
            // Generate vote hash the same way as in the Blockchain library
            $voteHashData = [
                'election_id' => $electionId,
                'candidate_id' => $candidateId,
                'voter_id' => $voterId,
                'timestamp' => $timestamp
            ];
            
            $voteHash = hash('sha256', json_encode($voteHashData));
            
            // Generate with different orderings to check if JSON serialization is consistent
            $voteHashData2 = [
                'candidate_id' => $candidateId,
                'election_id' => $electionId,
                'timestamp' => $timestamp,
                'voter_id' => $voterId
            ];
            
            $voteHash2 = hash('sha256', json_encode($voteHashData2));
            
            // Use blockchain library to generate a hash
            $blockchain = new \App\Libraries\Blockchain();
            $verifyResult = $blockchain->verifyVote(
                $voteHash,
                $electionId,
                $candidateId,
                $voterId,
                $timestamp,
                hash('sha256', $voterId)
            );
            
            // Check if a blockchain unique election ID would be generated
            $uniqueElectionId = $blockchain->generateUserElectionId($electionId, $voterId);
            
            // Generate hash with unique election ID
            $voteHashData3 = [
                'election_id' => $uniqueElectionId,
                'candidate_id' => $candidateId,
                'voter_id' => $voterId,
                'timestamp' => $timestamp
            ];
            
            $voteHash3 = hash('sha256', json_encode($voteHashData3));
            
            // Return debug information
            return $this->sendResponse([
                'inputs' => $voteHashData,
                'hash_result' => $voteHash,
                'reordered_inputs' => $voteHashData2,
                'reordered_hash' => $voteHash2,
                'hash_match' => $voteHash === $voteHash2,
                'verification_result' => $verifyResult,
                'unique_election_id' => $uniqueElectionId,
                'unique_id_hash' => $voteHash3,
                'serialized_json' => json_encode($voteHashData),
                'raw_test_data' => [
                    'json_input' => $json,
                    'get_params' => $this->request->getGet(),
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError('Error generating test hash: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Verify a specific vote transaction and show detailed debugging info
     * 
     * @param int $id Vote ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function debugVerifyVote($id = null)
    {
        try {
            if (!$id || !is_numeric($id)) {
                return $this->sendError('Vote ID harus diisi dengan nilai numerik', 400);
            }
            
            // Initialize models
            $voteModel = new \App\Models\VoteModel();
            $blockchainModel = new \App\Models\BlockchainTransactionModel();
            
            // Get vote details
            $vote = $voteModel->find($id);
            if (!$vote) {
                return $this->sendError('Vote tidak ditemukan', 404);
            }
            
            // Get blockchain vote details
            $blockchainVote = $blockchainModel->where('vote_id', $id)->first();
            if (!$blockchainVote) {
                return $this->sendError('Blockchain transaction tidak ditemukan', 404);
            }
            
            // Parse the blockchain data
            $blockchainData = json_decode($blockchainVote['data'] ?? '{}', true);
            
            // Get various values that might be used for hash calculation
            $electionId = $vote['election_id'];
            $blockchainElectionId = $blockchainVote['blockchain_election_id'] ?? 
                                   ($blockchainData['blockchain_election_id'] ?? $electionId);
            $timestamp = $blockchainData['timestamp'] ?? strtotime($vote['voted_at']);
            $candidateId = $vote['candidate_id'];
            $voterId = $vote['voter_id'];
            
            // Original vote hash from database
            $voteHash = $blockchainData['vote_hash'] ?? $blockchainVote['vote_hash'] ?? null;
            
            // Test different hash combinations
            $hashes = [];
            
            // 1. Using database election ID
            $hashData1 = [
                'election_id' => $electionId,
                'candidate_id' => $candidateId,
                'voter_id' => $voterId,
                'timestamp' => $timestamp
            ];
            $hashes['database_id'] = [
                'data' => $hashData1,
                'hash' => hash('sha256', json_encode($hashData1)),
                'matches_stored' => false
            ];
            
            // 2. Using blockchain election ID
            $hashData2 = [
                'election_id' => $blockchainElectionId,
                'candidate_id' => $candidateId,
                'voter_id' => $voterId,
                'timestamp' => $timestamp
            ];
            $hashes['blockchain_id'] = [
                'data' => $hashData2,
                'hash' => hash('sha256', json_encode($hashData2)),
                'matches_stored' => false
            ];
            
            // 3. Using vote_at timestamp instead
            $hashData3 = [
                'election_id' => $blockchainElectionId,
                'candidate_id' => $candidateId,
                'voter_id' => $voterId,
                'timestamp' => strtotime($vote['voted_at'])
            ];
            $hashes['voted_at_timestamp'] = [
                'data' => $hashData3,
                'hash' => hash('sha256', json_encode($hashData3)),
                'matches_stored' => false
            ];
            
            // Check which hash matches the stored hash
            if ($voteHash) {
                foreach ($hashes as $key => $hashInfo) {
                    $hashes[$key]['matches_stored'] = ($hashInfo['hash'] === $voteHash);
                }
            }
            
            // Get blockchain verification using our best guess
            $blockchain = new \App\Libraries\Blockchain();
            $verificationResult = $blockchain->verifyVote(
                $voteHash,
                $blockchainElectionId,
                $candidateId,
                $voterId,
                $timestamp,
                hash('sha256', $voterId)
            );
            
            return $this->sendResponse([
                'vote_id' => $id,
                'stored_hash' => $voteHash,
                'hash_tests' => $hashes,
                'vote_data' => [
                    'election_id' => $electionId,
                    'blockchain_election_id' => $blockchainElectionId,
                    'candidate_id' => $candidateId,
                    'voter_id' => $voterId,
                    'voted_at' => $vote['voted_at'],
                    'timestamp' => $timestamp
                ],
                'blockchain_data' => $blockchainData,
                'verification_result' => $verificationResult
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError('Error debugging vote verification: ' . $e->getMessage(), 500);
        }
    }
}
