<?php

namespace App\Models;

use CodeIgniter\Model;

class BlockchainVoteModel extends Model
{
    protected $table = 'blockchain_votes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vote_id', 'transaction_hash', 'vote_hash', 'block_number', 'status'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    public function getBlockchainVote($voteId)
    {
        return $this->where('vote_id', $voteId)->first();
    }

    public function updateStatus($transactionHash, $status, $blockNumber = null)
    {
        $data = ['status' => $status];
        if ($blockNumber) {
            $data['block_number'] = $blockNumber;
        }
        
        return $this->where('transaction_hash', $transactionHash)
                   ->set($data)
                   ->update();
    }
}