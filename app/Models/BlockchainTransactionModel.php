<?php

namespace App\Models;

use CodeIgniter\Model;

class BlockchainTransactionModel extends Model
{
    protected $table            = 'blockchain_transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'election_id', 'vote_id', 'tx_hash', 'tx_type', 'status', 
        'block_number', 'gas_used', 'gas_price', 'data', 'created_at', 'updated_at',
        'blockchain_election_id'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'election_id' => 'required|numeric',
        'tx_hash'     => 'required|min_length[10]',
        'tx_type'     => 'required',
        'status'      => 'required'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Create a new blockchain transaction record
     *
     * @param int $electionId Election ID
     * @param int|null $voteId Vote ID (if applicable)
     * @param string $txHash Transaction hash
     * @param string $txType Transaction type
     * @param array $data Additional data
     * @param int|null $blockchainElectionId Unique blockchain election ID
     * @return int|false ID of the inserted record or false on failure
     */
    public function createTransaction($electionId, $voteId, $txHash, $txType, $data = null, $blockchainElectionId = null)
    {
        $transaction = [
            'election_id' => $electionId,
            'vote_id'     => $voteId,
            'tx_hash'     => $txHash,
            'tx_type'     => $txType,
            'status'      => 'pending',
            'data'        => $data ? json_encode($data) : null,
            'created_at'  => date('Y-m-d H:i:s')
        ];
        
        // Add blockchain_election_id if provided
        if ($blockchainElectionId !== null) {
            $transaction['blockchain_election_id'] = $blockchainElectionId;
        }
        
        $this->insert($transaction);
        return $this->getInsertID();
    }
    
    /**
     * Update transaction status
     *
     * @param string $txHash Transaction hash
     * @param string $status New status
     * @param array $data Additional data to update
     * @return bool
     */
    public function updateTransactionStatus($txHash, $status, $data = [])
    {
        $updateData = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Add additional data if provided
        foreach ($data as $key => $value) {
            $updateData[$key] = $value;
        }
        
        return $this->where('tx_hash', $txHash)->update(null, $updateData);
    }
    
    /**
     * Get transaction by hash
     *
     * @param string $txHash Transaction hash
     * @return array|null
     */
    public function getTransactionByHash($txHash)
    {
        return $this->where('tx_hash', $txHash)->first();
    }
    
    /**
     * Get transactions by election ID
     *
     * @param int $electionId Election ID
     * @return array
     */
    public function getTransactionsByElection($electionId)
    {
        return $this->where('election_id', $electionId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get transactions by vote ID
     *
     * @param int $voteId Vote ID
     * @return array
     */
    public function getTransactionsByVote($voteId)
    {
        return $this->where('vote_id', $voteId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get recent transactions
     *
     * @param int $limit Number of transactions to return
     * @return array
     */
    public function getRecentTransactions($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get pending transactions
     *
     * @return array
     */
    public function getPendingTransactions()
    {
        return $this->where('status', 'pending')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }
    
    /**
     * Count transactions by status
     *
     * @param string $status Status to count
     * @return int
     */
    public function countTransactionsByStatus($status)
    {
        return $this->where('status', $status)->countAllResults();
    }
    
    /**
     * Count transactions by type
     *
     * @param string $type Type to count
     * @return int
     */
    public function countTransactionsByType($type)
    {
        return $this->where('tx_type', $type)->countAllResults();
    }
}