<?php

namespace App\Models;

use CodeIgniter\Model;

class ElectionModel extends Model
{
    protected $table = 'elections';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title', 'description', 'level', 'target_id',
        'start_time', 'end_time', 'status', 'created_by',
        'use_blockchain'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    // Hilangkan validasi greater_than karena tidak bekerja untuk datetime
    protected $validationRules = [
        'title' => 'required',
        'description' => 'required',
        'level' => 'required|in_list[jurusan,fakultas,universitas]',
        'start_time' => 'required|valid_date',
        'end_time' => 'required|valid_date',
        'created_by' => 'required|is_natural_no_zero'
    ];

    public function getActiveElections()
    {
        return $this->where('status', 'active')
                   ->where('start_time <=', date('Y-m-d H:i:s'))
                   ->where('end_time >=', date('Y-m-d H:i:s'))
                   ->findAll();
    }

    public function getElectionWithDetails($electionId)
    {
        $election = $this->select('elections.*, users.name as creator_name')
                        ->join('users', 'users.id = elections.created_by')
                        ->find($electionId);
        
        if (!$election) {
            return null;
        }
        
        // Get candidates count
        $candidateModel = new CandidateModel();
        $election['candidates_count'] = $candidateModel->where('election_id', $electionId)->countAllResults();
        
        // Get votes count
        $voteModel = new \App\Models\VoteModel();
        $election['votes_count'] = $voteModel->where('election_id', $electionId)->countAllResults();
        
        // Get eligibility
        $eligibilityModel = new \App\Models\EligibilityModel();
        $election['eligibility'] = $eligibilityModel->getEligibilityForElection($electionId);
        
        // Get eligible voters count
        $election['eligible_voters_count'] = count($eligibilityModel->getEligibleUsers($electionId));
        
        return $election;
    }

    public function getEligibleVoters($electionId)
    {
        $eligibilityModel = new \App\Models\EligibilityModel();
        return $eligibilityModel->getEligibleUsers($electionId);
    }

    public function isElectionActive($electionId)
    {
        $election = $this->find($electionId);
        if (!$election) return false;

        $now = date('Y-m-d H:i:s');
        return $election['status'] === 'active' &&
               $election['start_time'] <= $now &&
               $election['end_time'] >= $now;
    }
    
    public function updateElectionStatusIfNeeded($electionId)
    {
        $election = $this->find($electionId);
        if (!$election) return false;
        
        $now = date('Y-m-d H:i:s');
        $updated = false;
        
        // If election has ended and is still active, mark as completed
        if ($election['status'] === 'active' && $election['end_time'] < $now) {
            $this->update($electionId, ['status' => 'completed']);
            $updated = true;
        }
        
        return $updated;
    }
}