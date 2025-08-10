<?php

namespace App\Controllers;

use App\Models\ElectionModel;
use App\Models\VoteModel;
use App\Models\CandidateModel;
use App\Models\BlockchainVoteModel;

class Vote extends BaseController
{
    public function castVote()
    {
        try {
            $currentUser = $this->requireAuth();
            
            // Get JSON input
            $json = $this->request->getJSON(true);
            if (!$json) {
                return $this->sendError('Invalid JSON input', 400);
            }
            
            // Validate required fields
            $electionId = $json['election_id'] ?? null;
            $candidateId = $json['candidate_id'] ?? null;
            
            if (!$electionId || !is_numeric($electionId)) {
                return $this->sendError('Election ID harus diisi dengan nilai numerik', 400);
            }
            
            if (!$candidateId || !is_numeric($candidateId)) {
                return $this->sendError('Candidate ID harus diisi dengan nilai numerik', 400);
            }
            
            // Convert to integers
            $electionId = (int) $electionId;
            $candidateId = (int) $candidateId;

            // Initialize models
            $electionModel = new ElectionModel();
            $voteModel = new VoteModel();
            $candidateModel = new CandidateModel();
            $blockchainModel = new BlockchainVoteModel();
            
            // Get election details
            $election = $electionModel->find($electionId);
            if (!$election) {
                return $this->sendError('Pemilihan tidak ditemukan', 404);
            }

            // Cek apakah pemilihan aktif
            if (!$electionModel->isElectionActive($electionId)) {
                return $this->sendError('Pemilihan tidak aktif atau di luar periode voting', 400);
            }

            // Cek apakah user sudah voting
            if ($voteModel->hasVoted($electionId, $currentUser['id'])) {
                return $this->sendError('Anda sudah melakukan voting pada pemilihan ini', 400);
            }

            // Cek apakah kandidat valid
            $candidate = $candidateModel->find($candidateId);
            if (!$candidate) {
                return $this->sendError('Kandidat tidak ditemukan', 404);
            }
            
            if ($candidate['election_id'] != $electionId) {
                return $this->sendError('Kandidat tidak terdaftar dalam pemilihan ini', 400);
            }

            // Cek apakah user eligible untuk voting
            $eligibleVoters = $electionModel->getEligibleVoters($electionId);
            $isEligible = false;
            foreach ($eligibleVoters as $voter) {
                if ($voter['id'] == $currentUser['id']) {
                    $isEligible = true;
                    break;
                }
            }

            if (!$isEligible) {
                return $this->sendError('Anda tidak eligible untuk voting pada pemilihan ini', 403);
            }
            
            // Log audit trail before voting
            $auditor = new \App\Libraries\Auditor();
            $auditor->log('vote_attempt', [
                'user_id' => $currentUser['id'],
                'election_id' => $electionId,
                'candidate_id' => $candidateId,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            // Simpan vote ke database lokal dengan enkripsi
            $voteData = [
                'election_id' => $electionId,
                'voter_id' => $currentUser['id'],
                'candidate_id' => $candidateId,
                'voted_at' => date('Y-m-d H:i:s')
            ];

            // Use encrypted save method
            if (!$voteModel->saveEncrypted($voteData)) {
                $auditor->log('vote_failed', [
                    'user_id' => $currentUser['id'],
                    'election_id' => $electionId,
                    'reason' => 'Database error',
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
                return $this->sendError('Gagal menyimpan vote: ' . implode(', ', $voteModel->errors()), 500);
            }

            $voteId = $voteModel->getInsertID();

            // Prepare metadata for blockchain
            $metadata = [
                'user_faculty' => $currentUser['faculty_id'] ?? null,
                'user_department' => $currentUser['department_id'] ?? null,
                'election_title' => $election['title'],
                'candidate_name' => $candidateModel->getCandidateWithDetails($candidate['id'])['user_name'] ?? 'Unknown',
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString()
            ];
            
            // Kirim ke blockchain dengan metadata tambahan
            $blockchain = new \App\Libraries\Blockchain();
            
            try {
                $blockchainResult = $blockchain->castVote($electionId, $candidateId, $currentUser['id'], $metadata);
                
                if ($blockchainResult['status'] === 'success' || $blockchainResult['status'] === 'pending') {
                    // Simpan ke blockchain_votes dengan vote hash
                    $blockchainVoteData = [
                        'vote_id' => $voteId,
                        'transaction_hash' => $blockchainResult['transaction_hash'],
                        'vote_hash' => $blockchainResult['vote_hash'] ?? null,
                        'status' => $blockchainResult['status']
                    ];
                    
                    if (!$blockchainModel->save($blockchainVoteData)) {
                        throw new \Exception('Gagal menyimpan data blockchain vote: ' . implode(', ', $blockchainModel->errors()));
                    }
                
                // Log successful vote with more details
                $auditor->log('vote_success', [
                    'user_id' => $currentUser['id'],
                    'election_id' => $electionId,
                    'vote_id' => $voteId,
                    'transaction_hash' => $blockchainResult['transaction_hash'],
                    'vote_hash' => $blockchainResult['vote_hash'] ?? null,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'simulation' => $blockchainResult['simulation'] ?? false
                ]);

                // Create blockchain transaction record with enhanced data
                $txModel = new \App\Models\BlockchainTransactionModel();
                $txModel->save([
                    'election_id' => $electionId,
                    'vote_id' => $voteId,
                    'tx_hash' => $blockchainResult['transaction_hash'],
                    'tx_type' => 'vote',
                    'status' => $blockchainResult['status'],
                    'data' => json_encode([
                        'voter_id' => $currentUser['id'],
                        'candidate_id' => $candidateId,
                        'election_id' => $electionId,
                        'vote_hash' => $blockchainResult['vote_hash'] ?? null,
                        'timestamp' => $blockchainResult['timestamp'] ?? time(),
                        'metadata' => $metadata
                    ]),
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                return $this->sendResponse([
                    'message' => 'Vote berhasil dicatat',
                    'vote_id' => $voteId,
                    'transaction_hash' => $blockchainResult['transaction_hash'],
                    'vote_hash' => $blockchainResult['vote_hash'] ?? null,
                    'status' => $blockchainResult['status'],
                    'simulation' => $blockchainResult['simulation'] ?? false,
                    'election' => [
                        'id' => $election['id'],
                        'title' => $election['title']
                    ],
                    'candidate' => [
                        'id' => $candidate['id'],
                        'name' => $candidateModel->getCandidateWithDetails($candidate['id'])['user_name'] ?? 'Unknown'
                    ],
                    'timestamp' => $blockchainResult['timestamp'] ?? time()
                ]);
                } else {
                    // Rollback vote lokal jika blockchain gagal
                    $voteModel->delete($voteId);
                    
                    // Log blockchain failure
                    $auditor->log('vote_blockchain_failed', [
                        'user_id' => $currentUser['id'],
                        'election_id' => $electionId,
                        'error' => $blockchainResult['error'] ?? 'Unknown error',
                        'timestamp' => date('Y-m-d H:i:s')
                    ]);
                    
                    return $this->sendError('Gagal mencatat vote ke blockchain: ' . ($blockchainResult['error'] ?? 'Unknown error'), 500);
                }
            } catch (\Exception $e) {
                // Rollback vote lokal jika blockchain gagal
                $voteModel->delete($voteId);
                
                // Log blockchain exception
                $auditor->log('vote_blockchain_exception', [
                    'user_id' => $currentUser['id'],
                    'election_id' => $electionId,
                    'error' => $e->getMessage(),
                    'timestamp' => date('Y-m-d H:i:s')
                ]);
                
                return $this->sendError('Terjadi kesalahan saat mencatat vote ke blockchain: ' . $e->getMessage(), 500);
            }

        } catch (\Exception $e) {
            // Log exception
            log_message('error', 'Vote exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            
            return $this->sendError('Terjadi kesalahan saat memproses vote: ' . $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function hasVoted($electionId)
    {
        try {
            $currentUser = $this->requireAuth();

            $voteModel = new VoteModel();
            $hasVoted = $voteModel->hasVoted($electionId, $currentUser['id']);

            return $this->sendResponse(['has_voted' => $hasVoted]);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    /**
     * Verify a vote on the blockchain
     *
     * @param int $id Vote ID to verify
     * @return \CodeIgniter\HTTP\Response
     */
    public function verifyVote($id = null)
    {
        try {
            // Authentication is optional for verification
            $currentUser = $this->getAuthUser();
            
            if (!$id || !is_numeric($id)) {
                return $this->sendError('Vote ID harus diisi dengan nilai numerik', 400);
            }
            
            // Initialize models
            $voteModel = new VoteModel();
            $blockchainModel = new BlockchainVoteModel();
            
            // Get vote details
            $vote = $voteModel->find($id);
            if (!$vote) {
                return $this->sendError('Vote tidak ditemukan', 404);
            }
            
            // Get blockchain vote details
            $blockchainVote = $blockchainModel->where('vote_id', $id)->first();
            if (!$blockchainVote) {
                return $this->sendError('Blockchain vote tidak ditemukan', 404);
            }
            
            // Get transaction receipt
            $blockchain = new \App\Libraries\Blockchain();
            $receipt = $blockchain->getTransactionReceipt($blockchainVote['transaction_hash']);
            
            try {
                // Verify the vote
                $verificationResult = $blockchain->verifyVote(
                    $blockchainVote['vote_hash'] ?? '',
                    $vote['election_id'],
                    $vote['candidate_id'],
                    $vote['voter_id'],
                    strtotime($vote['voted_at']),
                    hash('sha256', $vote['voter_id'])
                );
                
                // Get vote details from blockchain
                $voteDetails = $blockchain->getVoteDetails($blockchainVote['vote_hash'] ?? '');
            } catch (\Exception $e) {
                log_message('error', 'Blockchain verification error: ' . $e->getMessage());
                
                // Create fallback verification result
                $verificationResult = [
                    'valid' => false,
                    'hash_valid' => false,
                    'on_blockchain' => false,
                    'error' => $e->getMessage(),
                    'verification_time' => time()
                ];
                
                $voteDetails = null;
            }
            
            // Log verification attempt
            $auditor = new \App\Libraries\Auditor();
            $auditor->log('vote_verification', [
                'vote_id' => $id,
                'user_id' => $currentUser ? $currentUser['id'] : null,
                'result' => $verificationResult['valid'] ? 'valid' : 'invalid',
                'hash_valid' => $verificationResult['hash_valid'] ?? false,
                'on_blockchain' => $verificationResult['on_blockchain'] ?? false,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            return $this->sendResponse([
                'vote_id' => $id,
                'transaction_hash' => $blockchainVote['transaction_hash'],
                'vote_hash' => $blockchainVote['vote_hash'] ?? null,
                'verification' => $verificationResult,
                'receipt' => $receipt,
                'vote_details' => $voteDetails,
                'local_vote' => [
                    'election_id' => $vote['election_id'],
                    'candidate_id' => $vote['candidate_id'],
                    'voted_at' => $vote['voted_at']
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Vote verification exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return $this->sendError('Terjadi kesalahan saat memverifikasi vote: ' . $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    /**
     * Get all votes for an election with blockchain verification
     *
     * @param int $electionId Election ID
     * @return \CodeIgniter\HTTP\Response
     */
    public function getElectionVotes($electionId = null)
    {
        try {
            // Require admin authentication
            $currentUser = $this->requireAuth(true);
            
            if (!$electionId || !is_numeric($electionId)) {
                return $this->sendError('Election ID harus diisi dengan nilai numerik', 400);
            }
            
            // Initialize models
            $electionModel = new ElectionModel();
            $voteModel = new VoteModel();
            $blockchainModel = new BlockchainVoteModel();
            
            // Get election details
            $election = $electionModel->find($electionId);
            if (!$election) {
                return $this->sendError('Pemilihan tidak ditemukan', 404);
            }
            
            // Get votes for the election
            $votes = $voteModel->where('election_id', $electionId)->findAll();
            
            // Get blockchain votes
            $blockchain = new \App\Libraries\Blockchain();
            $blockchainVotes = $blockchain->getElectionVotes($electionId);
            
            // Combine local and blockchain votes
            $combinedVotes = [];
            foreach ($votes as $vote) {
                $blockchainVote = $blockchainModel->where('vote_id', $vote['id'])->first();
                
                $combinedVotes[] = [
                    'vote_id' => $vote['id'],
                    'election_id' => $vote['election_id'],
                    'candidate_id' => $vote['candidate_id'],
                    'voter_id' => $vote['voter_id'],
                    'voted_at' => $vote['voted_at'],
                    'blockchain' => $blockchainVote ? [
                        'transaction_hash' => $blockchainVote['transaction_hash'],
                        'vote_hash' => $blockchainVote['vote_hash'] ?? null,
                        'status' => $blockchainVote['status']
                    ] : null
                ];
            }
            
            return $this->sendResponse([
                'election' => $election,
                'votes' => $combinedVotes,
                'blockchain_votes' => $blockchainVotes,
                'total_votes' => count($votes),
                'blockchain_total' => count($blockchainVotes)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get election votes exception: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return $this->sendError('Terjadi kesalahan saat mengambil data votes: ' . $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}