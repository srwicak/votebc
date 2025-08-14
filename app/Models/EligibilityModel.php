<?php

namespace App\Models;

use CodeIgniter\Model;

class EligibilityModel extends Model
{
    protected $table = 'eligibility';
    protected $primaryKey = 'id';
    protected $allowedFields = ['election_id', 'faculty_id', 'department_id'];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    
    /**
     * Get eligibility settings for an election
     * 
     * @param int $electionId The election ID
     * @return array The eligibility settings
     */
    public function getEligibilityForElection($electionId)
    {
        return $this->where('election_id', $electionId)->findAll();
    }
    
    /**
     * Check if a user is eligible for an election
     * 
     * @param int $electionId The election ID
     * @param int $userId The user ID
     * @return bool True if eligible, false otherwise
     */
    public function isUserEligible($electionId, $userId)
    {
        // Get user details
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        
        if (!$user) {
            return false;
        }
        
        // Get eligibility settings
        $eligibility = $this->getEligibilityForElection($electionId);
        
        // If no eligibility settings, all users are eligible
        if (empty($eligibility)) {
            return true;
        }
        
        // Check if user matches any eligibility criteria
        foreach ($eligibility as $item) {
            // Check faculty
            if ($item['faculty_id'] == $user['faculty_id']) {
                // If department is specified, check department
                if (!empty($item['department_id'])) {
                    if ($item['department_id'] == $user['department_id']) {
                        return true;
                    }
                } else {
                    // If no department specified, any user from this faculty is eligible
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Get eligible users for an election
     * 
     * @param int $electionId The election ID
     * @return array The eligible users
     */
    public function getEligibleUsers($electionId)
    {
        // Get eligibility settings
        $eligibility = $this->getEligibilityForElection($electionId);
        
        // If no eligibility settings, all users are eligible
        if (empty($eligibility)) {
            $userModel = new UserModel();
            return $userModel->where('role', 'mahasiswa')->findAll();
        }
        
        // Build query to get eligible users
        $userModel = new UserModel();
        $userModel->where('role', 'mahasiswa');
        
        $userModel->groupStart();
        
        foreach ($eligibility as $index => $item) {
            if ($index > 0) {
                $userModel->orGroupStart();
            } else {
                $userModel->groupStart();
            }
            
            $userModel->where('faculty_id', $item['faculty_id']);
            
            if (!empty($item['department_id'])) {
                $userModel->where('department_id', $item['department_id']);
            }
            
            $userModel->groupEnd();
        }
        
        $userModel->groupEnd();
        
        return $userModel->findAll();
    }
}