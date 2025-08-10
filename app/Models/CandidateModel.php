<?php

namespace App\Models;

use CodeIgniter\Model;

class CandidateModel extends Model
{
    protected $table = 'candidates';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'candidate_id', 'vice_candidate_id', 'election_id', 'photo', 'vision', 'mission', 'programs'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'candidate_id' => 'required|is_natural_no_zero',
        'vice_candidate_id' => 'required|is_natural_no_zero',
        'election_id' => 'required|is_natural_no_zero'
    ];

    public function getCandidatesWithUser($electionId)
    {
        return $this->select('candidates.*,
                            candidate.name as candidate_name, candidate.nim as candidate_nim,
                            candidate_department.name as candidate_department_name,
                            vice_candidate.name as vice_candidate_name,
                            vice_candidate.nim as vice_candidate_nim,
                            vice_candidate_department.name as vice_candidate_department_name')
                   ->join('users as candidate', 'candidate.id = candidates.candidate_id')
                   ->join('departments as candidate_department', 'candidate_department.id = candidate.department_id', 'left')
                   ->join('users as vice_candidate', 'vice_candidate.id = candidates.vice_candidate_id', 'left')
                   ->join('departments as vice_candidate_department', 'vice_candidate_department.id = vice_candidate.department_id', 'left')
                   ->where('candidates.election_id', $electionId)
                   ->findAll();
    }

    public function getCandidateWithDetails($candidateRowId)
    {
        return $this->select('candidates.*,
                            candidate.name as candidate_name, candidate.email as candidate_email, candidate.nim as candidate_nim,
                            candidate_department.name as candidate_department_name, candidate_faculty.name as candidate_faculty_name,
                            vice_candidate.name as vice_candidate_name,
                            vice_candidate.email as vice_candidate_email,
                            vice_candidate.nim as vice_candidate_nim,
                            vice_candidate_department.name as vice_candidate_department_name,
                            vice_candidate_faculty.name as vice_candidate_faculty_name')
                   ->join('users as candidate', 'candidate.id = candidates.candidate_id')
                   ->join('departments as candidate_department', 'candidate_department.id = candidate.department_id', 'left')
                   ->join('faculties as candidate_faculty', 'candidate_faculty.id = candidate_department.faculty_id', 'left')
                   ->join('users as vice_candidate', 'vice_candidate.id = candidates.vice_candidate_id', 'left')
                   ->join('departments as vice_candidate_department', 'vice_candidate_department.id = vice_candidate.department_id', 'left')
                   ->join('faculties as vice_candidate_faculty', 'vice_candidate_faculty.id = vice_candidate_department.faculty_id', 'left')
                   ->where('candidates.id', $candidateRowId)
                   ->first();
    }
    
    /**
     * Get candidate pair with full details for both primary candidate and vice candidate
     *
     * @param int $candidateRowId
     * @return array|null
     */
    public function getCandidatePairWithDetails($candidateRowId)
    {
        return $this->select('candidates.*,
                            candidate.name as candidate_name, candidate.email as candidate_email, candidate.nim as candidate_nim,
                            candidate_department.name as candidate_department_name, candidate_faculty.name as candidate_faculty_name,
                            vice_candidate.name as vice_candidate_name, vice_candidate.email as vice_candidate_email, vice_candidate.nim as vice_candidate_nim,
                            vice_candidate_department.name as vice_candidate_department_name, vice_candidate_faculty.name as vice_candidate_faculty_name')
                   ->join('users as candidate', 'candidate.id = candidates.candidate_id')
                   ->join('departments as candidate_department', 'candidate_department.id = candidate.department_id', 'left')
                   ->join('faculties as candidate_faculty', 'candidate_faculty.id = candidate_department.faculty_id', 'left')
                   ->join('users as vice_candidate', 'vice_candidate.id = candidates.vice_candidate_id', 'left')
                   ->join('departments as vice_candidate_department', 'vice_candidate_department.id = vice_candidate.department_id', 'left')
                   ->join('faculties as vice_candidate_faculty', 'vice_candidate_faculty.id = vice_candidate_department.faculty_id', 'left')
                   ->where('candidates.id', $candidateRowId)
                   ->first();
    }
    
    /**
     * Check if a user is already part of an active candidate pair in an election of the same level
     *
     * @param int $userId
     * @param string $electionLevel
     * @return bool
     */
    public function isUserInActiveElection($userId, $electionLevel)
    {
        $result = $this->select('candidates.id')
                      ->join('elections', 'elections.id = candidates.election_id')
                      ->where('elections.level', $electionLevel)
                      ->where('elections.status', 'active')
                      ->groupStart()
                          ->where('candidates.candidate_id', $userId)
                          ->orWhere('candidates.vice_candidate_id', $userId)
                      ->groupEnd()
                      ->first();
        
        return $result !== null;
    }
}