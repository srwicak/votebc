<?php

namespace App\Models;

use CodeIgniter\Model;

class VoteModel extends Model
{
    protected $table = 'votes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['election_id', 'voter_id', 'candidate_id', 'candidate_hash', 'voted_at', 'vote_hash'];
    protected $useTimestamps = false;

    protected $validationRules = [
        'election_id' => 'required|is_natural_no_zero',
        'voter_id' => 'required|is_natural_no_zero',
        'candidate_id' => 'required'
    ];

    public function hasVoted($electionId, $voterId)
    {
        return $this->where(['election_id' => $electionId, 'voter_id' => $voterId])
                   ->first() !== null;
    }

    public function getVoteDetails($voteId)
    {
        return $this->select('votes.*, elections.title as election_title, candidates.user_id as candidate_user_id, voters.name as voter_name')
                   ->join('elections', 'elections.id = votes.election_id')
                   ->join('candidates', 'candidates.id = votes.candidate_id')
                   ->join('users as voters', 'voters.id = votes.voter_id')
                   ->where('votes.id', $voteId)
                   ->first();
    }

    public function getElectionResults($electionId)
    {
        $builder = $this->db->table('votes')
                           ->select('candidate_id, COUNT(*) as vote_count')
                           ->where('election_id', $electionId)
                           ->groupBy('candidate_id')
                           ->orderBy('vote_count', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function getTotalVotes($electionId)
    {
        return $this->where('election_id', $electionId)->countAllResults();
    }
    
    /**
     * Mendapatkan jumlah suara per hari
     * 
     * @param int $electionId ID pemilihan
     * @return array
     */
    public function getVotesByTime($electionId)
    {
        $builder = $this->db->table('votes')
                           ->select('DATE(voted_at) as date, COUNT(*) as vote_count')
                           ->where('election_id', $electionId)
                           ->groupBy('DATE(voted_at)')
                           ->orderBy('date', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Mendapatkan jumlah suara per fakultas
     * 
     * @param int $electionId ID pemilihan
     * @return array
     */
    public function getVotesByFaculty($electionId)
    {
        $builder = $this->db->table('votes')
                           ->select('faculties.name as faculty_name, COUNT(*) as vote_count')
                           ->join('users', 'users.id = votes.voter_id')
                           ->join('faculties', 'faculties.id = users.faculty_id')
                           ->where('votes.election_id', $electionId)
                           ->groupBy('faculties.id')
                           ->orderBy('vote_count', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Mendapatkan jumlah suara per jurusan
     * 
     * @param int $electionId ID pemilihan
     * @return array
     */
    public function getVotesByDepartment($electionId)
    {
        $builder = $this->db->table('votes')
                           ->select('departments.name as department_name, faculties.name as faculty_name, COUNT(*) as vote_count')
                           ->join('users', 'users.id = votes.voter_id')
                           ->join('departments', 'departments.id = users.department_id')
                           ->join('faculties', 'faculties.id = departments.faculty_id')
                           ->where('votes.election_id', $electionId)
                           ->groupBy('departments.id')
                           ->orderBy('vote_count', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Mendapatkan demografi pemilih
     * 
     * @param int $electionId ID pemilihan
     * @return array
     */
    public function getVoterDemographics($electionId)
    {
        // Jenis kelamin (jika ada)
        $genderStats = $this->db->table('votes')
                              ->select('users.gender, COUNT(*) as count')
                              ->join('users', 'users.id = votes.voter_id')
                              ->where('votes.election_id', $electionId)
                              ->groupBy('users.gender')
                              ->get()
                              ->getResultArray();
        
        // Tahun angkatan (dari NIM)
        $yearStats = $this->db->table('votes')
                            ->select('SUBSTRING(users.nim, 1, 4) as year, COUNT(*) as count')
                            ->join('users', 'users.id = votes.voter_id')
                            ->where('votes.election_id', $electionId)
                            ->groupBy('year')
                            ->orderBy('year', 'ASC')
                            ->get()
                            ->getResultArray();
        
        return [
            'gender' => $genderStats,
            'year' => $yearStats
        ];
    }
    
    /**
     * Mendapatkan distribusi suara per jam
     * 
     * @param int $electionId ID pemilihan
     * @return array
     */
    public function getHourlyVoteDistribution($electionId)
    {
        $builder = $this->db->table('votes')
                           ->select('HOUR(voted_at) as hour, COUNT(*) as vote_count')
                           ->where('election_id', $electionId)
                           ->groupBy('hour')
                           ->orderBy('hour', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Mendapatkan perbandingan dengan pemilihan sebelumnya
     * 
     * @param int $electionId ID pemilihan
     * @return array
     */
    public function getVoteComparisonWithPreviousElections($electionId)
    {
        // Dapatkan level dan target_id dari pemilihan saat ini
        $election = $this->db->table('elections')
                           ->select('level, target_id')
                           ->where('id', $electionId)
                           ->get()
                           ->getRowArray();
        
        if (!$election) {
            return [];
        }
        
        // Dapatkan pemilihan sebelumnya dengan level dan target yang sama
        $previousElections = $this->db->table('elections')
                                    ->select('id, title, start_time')
                                    ->where('level', $election['level'])
                                    ->where('target_id', $election['target_id'])
                                    ->where('id !=', $electionId)
                                    ->where('end_time <', 'NOW()', false)
                                    ->orderBy('start_time', 'DESC')
                                    ->limit(5)
                                    ->get()
                                    ->getResultArray();
        
        $result = [];
        foreach ($previousElections as $prev) {
            $voteCount = $this->where('election_id', $prev['id'])->countAllResults();
            $result[] = [
                'election_id' => $prev['id'],
                'title' => $prev['title'],
                'date' => date('Y-m-d', strtotime($prev['start_time'])),
                'vote_count' => $voteCount
            ];
        }
        
        // Tambahkan pemilihan saat ini
        $currentElection = $this->db->table('elections')
                                  ->select('title, start_time')
                                  ->where('id', $electionId)
                                  ->get()
                                  ->getRowArray();
        
        $currentVoteCount = $this->where('election_id', $electionId)->countAllResults();
        $result[] = [
            'election_id' => $electionId,
            'title' => $currentElection['title'],
            'date' => date('Y-m-d', strtotime($currentElection['start_time'])),
            'vote_count' => $currentVoteCount,
            'is_current' => true
        ];
        
        // Urutkan berdasarkan tanggal
        usort($result, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        
        return $result;
    }
    
    /**
     * Menyimpan vote terenkripsi
     * 
     * @param array $data Data vote
     * @return bool
     */
    public function saveEncrypted($data)
    {
        // Enkripsi data kandidat
        $encryptor = new \App\Libraries\Encryptor();
        
        $encryptedData = [
            'election_id' => $data['election_id'],
            'voter_id' => $data['voter_id'],
            'candidate_id' => $data['candidate_id'], // Store the actual candidate ID for vote counting
            'candidate_hash' => $encryptor->hash($data['candidate_id']), // Store hash for verification purposes
            'voted_at' => $data['voted_at'],
            'vote_hash' => $encryptor->hash([
                'election_id' => $data['election_id'],
                'voter_id' => $data['voter_id'],
                'candidate_id' => $data['candidate_id'],
                'timestamp' => $data['voted_at']
            ])
        ];
        
        return $this->save($encryptedData);
    }
}