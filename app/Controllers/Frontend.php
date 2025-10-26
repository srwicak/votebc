<?php

namespace App\Controllers;

use App\Libraries\JWT;

class Frontend extends BaseController
{
    // Mengubah access level dari private menjadi protected
    protected $userData = null;
    protected $isLoggedIn = false;

    public function __construct()
    {
        log_message('debug', 'Session token: ' . (session()->get('auth_token') ? 'exists' : 'not exists'));

        $this->checkAuth();
    }

    private function checkAuth()
    {
        $authToken = session()->get('auth_token');
        if ($authToken) {
            try {
                $jwt = new JWT();
                $decoded = $jwt->decode($authToken);
                if ($decoded) {
                    $userModel = new \App\Models\UserModel();
                    $this->userData = $userModel->getUserWithAcademic($decoded['user_id']);
                    $this->isLoggedIn = true;
                }
            } catch (\Exception $e) {
                session()->remove('auth_token');
            }
        }
    }

    private function render($view, $data = [])
    {
        $defaultData = [
            'user' => $this->userData,
            'isLoggedIn' => $this->isLoggedIn,
            'title' => isset($data['title']) ? $data['title'] : 'E-Voting BEM'
        ];
        
        $data = array_merge($defaultData, $data);
        
        return view('frontend/layout/header', $data)
             . view('frontend/layout/navbar', $data)
             . view($view, $data)
             . view('frontend/layout/footer', $data);
    }

    public function index()
    {
        $data = [
            'title' => 'Beranda - E-Voting BEM',
            'page' => 'home'
        ];
        return $this->render('frontend/pages/home', $data);
    }

    public function login()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->isLoggedIn) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login - E-Voting BEM',
            'page' => 'login'
        ];
        return $this->render('frontend/pages/login', $data);
    }

    public function register()
    {
        // Jika sudah login, redirect ke dashboard
        if ($this->isLoggedIn) {
            return redirect()->to('/dashboard');
        }

        // Load data untuk form
        $facultyModel = new \App\Models\FacultyModel();
        $faculties = $facultyModel->findAll();

        $data = [
            'title' => 'Register - E-Voting BEM',
            'page' => 'register',
            'faculties' => $faculties
        ];
        return $this->render('frontend/pages/register', $data);
    }

    public function logout()
    {
        session()->remove('auth_token');
        return redirect()->to('/login')->with('message', 'Logout berhasil');
    }

    public function dashboard()
    {
        if (!$this->isLoggedIn) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Load data untuk dashboard
        $electionModel = new \App\Models\ElectionModel();
        $voteModel = new \App\Models\VoteModel();
        
        // Check if userData is null or role is admin
        if ($this->userData && $this->userData['role'] === 'admin') {
            // Admin dashboard
            $elections = $electionModel->orderBy('created_at', 'DESC')->findAll(5);
            $totalUsers = (new \App\Models\UserModel())->countAll();
            $totalElections = $electionModel->countAll();
            $totalVotes = $voteModel->countAll();
            
            $data = [
                'title' => 'Admin Dashboard - E-Voting BEM',
                'page' => 'admin-dashboard',
                'elections' => $elections,
                'totalUsers' => $totalUsers,
                'totalElections' => $totalElections,
                'totalVotes' => $totalVotes
            ];
            return $this->render('frontend/pages/admin/dashboard', $data);
        } else {
            // User dashboard
            $activeElections = $electionModel->getActiveElections();
            $userVotes = [];
            
            foreach ($activeElections as $election) {
                $hasVoted = $voteModel->hasVoted($election['id'], $this->userData['id']);
                $userVotes[$election['id']] = $hasVoted;
            }
            
            $data = [
                'title' => 'Dashboard - E-Voting BEM',
                'page' => 'user-dashboard',
                'activeElections' => $activeElections,
                'userVotes' => $userVotes
            ];
            return $this->render('frontend/pages/dashboard', $data);
        }
    }

    public function elections()
    {
        if (!$this->isLoggedIn) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $electionModel = new \App\Models\ElectionModel();
        
        // Check and update status for all elections
        $allElections = $electionModel->findAll();
        foreach ($allElections as $election) {
            $electionModel->updateElectionStatusIfNeeded($election['id']);
        }
        
        // For admin users, show all elections including drafts
        // For regular users, only show active and completed elections
        if ($this->userData && $this->userData['role'] === 'admin') {
            $elections = $electionModel->select('elections.*, users.name as creator_name')
                                      ->join('users', 'users.id = elections.created_by')
                                      ->orderBy('elections.created_at', 'DESC')
                                      ->findAll();
        } else {
            $elections = $electionModel->select('elections.*, users.name as creator_name')
                                      ->join('users', 'users.id = elections.created_by')
                                      ->where('elections.status !=', 'draft')
                                      ->orderBy('elections.created_at', 'DESC')
                                      ->findAll();
        }

        $data = [
            'title' => 'Pemilihan - E-Voting BEM',
            'page' => 'elections',
            'elections' => $elections
        ];
        return $this->render('frontend/pages/elections', $data);
    }

    public function electionDetail($electionId)
    {
        if (!$this->isLoggedIn) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $electionModel = new \App\Models\ElectionModel();
        $candidateModel = new \App\Models\CandidateModel();
        $voteModel = new \App\Models\VoteModel();

        // Check and update election status if needed
        $electionModel->updateElectionStatusIfNeeded($electionId);
        
        $election = $electionModel->getElectionWithDetails($electionId);
        if (!$election) {
            return redirect()->to('/elections')->with('error', 'Pemilihan tidak ditemukan');
        }

        // For non-admin users, prevent access to draft elections
        if ($this->userData && $this->userData['role'] !== 'admin' && $election['status'] === 'draft') {
            return redirect()->to('/elections')->with('error', 'Pemilihan tidak ditemukan');
        }

        $candidates = $candidateModel->getCandidatesWithUser($electionId);
        $hasVoted = $voteModel->hasVoted($electionId, $this->userData['id']);
        
        // Get user's vote details if they have voted
        $userVote = null;
        $userBlockchainVote = null;
        if ($hasVoted) {
            $userVote = $voteModel->where('election_id', $electionId)
                                  ->where('voter_id', $this->userData['id'])
                                  ->first();
            if ($userVote) {
                $blockchainModel = new \App\Models\BlockchainTransactionModel();
                $userBlockchainVote = $blockchainModel->where('vote_id', $userVote['id'])->first();
                
                // Parse vote_hash from JSON data if available
                if ($userBlockchainVote && !empty($userBlockchainVote['data'])) {
                    $blockchainData = json_decode($userBlockchainVote['data'], true);
                    if (isset($blockchainData['vote_hash'])) {
                        $userBlockchainVote['vote_hash'] = $blockchainData['vote_hash'];
                    }
                }
            }
        }

        $data = [
            'title' => $election['title'] . ' - E-Voting BEM',
            'page' => 'election-detail',
            'election' => $election,
            'candidates' => $candidates,
            'hasVoted' => $hasVoted,
            'userVote' => $userVote,
            'userBlockchainVote' => $userBlockchainVote
        ];
        return $this->render('frontend/pages/election_detail', $data);
    }

    public function profile()
    {
        if (!$this->isLoggedIn) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        $data = [
            'title' => 'Profil - E-Voting BEM',
            'page' => 'profile'
        ];
        return $this->render('frontend/pages/profile', $data);
    }

    // Admin Pages
    public function adminElections()
    {
        if (!$this->isLoggedIn || $this->userData['role'] !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        $electionModel = new \App\Models\ElectionModel();
        $elections = $electionModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Manajemen Pemilihan - Admin',
            'page' => 'admin-elections',
            'elections' => $elections
        ];
        return $this->render('frontend/pages/admin/elections', $data);
    }

    public function adminUsers()
    {
        if (!$this->isLoggedIn || $this->userData['role'] !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        $userModel = new \App\Models\UserModel();
        
        // Get users with faculty and department names
        $users = $userModel->select('users.*, faculties.name as faculty_name, departments.name as department_name')
                          ->join('faculties', 'faculties.id = users.faculty_id', 'left')
                          ->join('departments', 'departments.id = users.department_id', 'left')
                          ->orderBy('users.created_at', 'DESC')
                          ->findAll();

        $data = [
            'title' => 'Manajemen User - Admin',
            'page' => 'admin-users',
            'users' => $users
        ];
        return $this->render('frontend/pages/admin/users', $data);
    }
    
    public function adminAcademic()
    {
        if (!$this->isLoggedIn || ($this->userData['role'] !== 'admin' && $this->userData['role'] !== 'operator')) {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        $facultyModel = new \App\Models\FacultyModel();
        $departmentModel = new \App\Models\DepartmentModel();
        
        $faculties = $facultyModel->findAll();
        
        // Get departments with faculty names
        $departments = $departmentModel->select('departments.*, faculties.name as faculty_name')
                                      ->join('faculties', 'faculties.id = departments.faculty_id')
                                      ->findAll();
        
        // Count departments per faculty
        foreach ($faculties as &$faculty) {
            $faculty['departments'] = array_filter($departments, function($dept) use ($faculty) {
                return $dept['faculty_id'] == $faculty['id'];
            });
        }
        
        $data = [
            'title' => 'Manajemen Akademik - Admin',
            'page' => 'admin-academic',
            'faculties' => $faculties,
            'departments' => $departments
        ];
        return $this->render('frontend/pages/admin/academic', $data);
    }
    
    public function adminResetPassword()
    {
        if (!$this->isLoggedIn || $this->userData['role'] !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        $userModel = new \App\Models\UserModel();
        $users = $userModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Reset Password - Admin',
            'page' => 'admin-reset-password',
            'users' => $users
        ];
        return $this->render('admin/reset_password', $data);
    }

    /**
     * Admin view candidates by election page
     *
     * @param int $electionId Election ID
     * @return string
     */
    public function adminViewCandidates($electionId = null)
    {
        if (!$this->isLoggedIn || $this->userData['role'] !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }

        if (!$electionId) {
            return redirect()->to('/admin/elections')->with('error', 'ID pemilihan tidak valid');
        }

        $electionModel = new \App\Models\ElectionModel();
        $candidateModel = new \App\Models\CandidateModel();
        
        $election = $electionModel->find($electionId);
        
        if (!$election) {
            return redirect()->to('/admin/elections')->with('error', 'Pemilihan tidak ditemukan');
        }

        // Get all candidates for this election
        $candidates = $candidateModel->getCandidatesWithDetails($electionId);

        $data = [
            'title' => 'Daftar Kandidat - ' . $election['title'],
            'page' => 'admin-view-candidates',
            'election' => $election,
            'candidates' => $candidates
        ];

        return $this->render('frontend/pages/admin/view_candidates', $data);
    }

    public function getDepartments($facultyId)
    {
        // Log the request for debugging
        log_message('debug', "Fetching departments for faculty ID: {$facultyId}");
        
        $departmentModel = new \App\Models\DepartmentModel();
        $departments = $departmentModel->where('faculty_id', $facultyId)->findAll();
        
        // Log the result for debugging
        log_message('debug', "Found " . count($departments) . " departments for faculty ID: {$facultyId}");
        log_message('debug', "Departments data: " . json_encode($departments));
        
        // Set proper headers to prevent caching issues
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Cache-Control', 'no-store, max-age=0, no-cache')
            ->setJSON($departments);
    }
    
    /**
     * Verification page for blockchain transactions
     *
     * @return string
     */
    public function verifyVote($voteId = null)
    {
        $data = [
            'title' => 'Verifikasi Vote - E-Voting BEM',
            'page' => 'verify-vote',
            'voteId' => $voteId
        ];
        
        // If vote ID is provided, get vote details
        if ($voteId) {
            $voteModel = new \App\Models\VoteModel();
            $blockchainModel = new \App\Models\BlockchainTransactionModel();
            
            $vote = $voteModel->find($voteId);
            $blockchainVote = $blockchainModel->where('vote_id', $voteId)->first();
            
            if ($vote && $blockchainVote) {
                $data['vote'] = $vote;
                $data['blockchainVote'] = $blockchainVote;

                // Get election and candidate details
                $electionModel = new \App\Models\ElectionModel();
                
                $data['election'] = $electionModel->find($vote['election_id']);
            }
        }
        
        return $this->render('frontend/pages/verify_vote', $data);
    }
    
    /**
     * Create election page
     *
     * @return string
     */
    public function createElection()
    {
        if (!$this->isLoggedIn || $this->userData['role'] !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }
        
        // Get faculties and departments for eligibility selection
        $facultyModel = new \App\Models\FacultyModel();
        $faculties = $facultyModel->findAll();
        
        $data = [
            'title' => 'Buat Pemilihan Baru - Admin',
            'page' => 'admin-create-election',
            'faculties' => $faculties
        ];
        
        return $this->render('frontend/pages/admin/create_election', $data);
    }
    
    /**
     * Edit election page
     *
     * @param int $id Election ID
     * @return string
     */
    public function editElection($id = null)
    {
        if (!$this->isLoggedIn || $this->userData['role'] !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }
        
        if (!$id) {
            return redirect()->to('/admin/elections')->with('error', 'ID pemilihan tidak valid');
        }
        
        $electionModel = new \App\Models\ElectionModel();
        $election = $electionModel->find($id);
        
        if (!$election) {
            return redirect()->to('/admin/elections')->with('error', 'Pemilihan tidak ditemukan');
        }
        
        // Get faculties and departments for eligibility selection
        $facultyModel = new \App\Models\FacultyModel();
        $faculties = $facultyModel->findAll();
        
        // Get current eligibility settings
        $eligibilityModel = new \App\Models\EligibilityModel();
        $eligibility = $eligibilityModel->where('election_id', $id)->findAll();
        
        $data = [
            'title' => 'Edit Pemilihan - Admin',
            'page' => 'admin-edit-election',
            'election' => $election,
            'faculties' => $faculties,
            'eligibility' => $eligibility
        ];
        
        return $this->render('frontend/pages/admin/edit_election', $data);
    }
    
    /**
     * Create candidate page
     *
     * @return string
     */
    public function createCandidate()
    {
        if (!$this->isLoggedIn || $this->userData['role'] !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }
        
        // Get elections for selection
        $electionModel = new \App\Models\ElectionModel();
        $elections = $electionModel->findAll();
        
        // Get users for selection (all users for university level)
        $userModel = new \App\Models\UserModel();
        $users = $userModel->findAll();
        
        $data = [
            'title' => 'Tambah Kandidat Baru - Admin',
            'page' => 'admin-create-candidate',
            'elections' => $elections,
            'users' => $users
        ];
        
        return $this->render('frontend/pages/admin/create_candidate', $data);
    }
    
    /**
     * Edit candidate page
     *
     * @param int $id Candidate ID
     * @return string
     */
    public function editCandidate($id = null)
    {
        if (!$this->isLoggedIn || $this->userData['role'] !== 'admin') {
            return redirect()->to('/login')->with('error', 'Akses ditolak');
        }
        
        if (!$id) {
            return redirect()->to('/admin/elections')->with('error', 'ID kandidat tidak valid');
        }
        
        $candidateModel = new \App\Models\CandidateModel();
        $candidate = $candidateModel->find($id);
        
        if (!$candidate) {
            return redirect()->to('/admin/elections')->with('error', 'Kandidat tidak ditemukan');
        }
        
        // Get elections for selection
        $electionModel = new \App\Models\ElectionModel();
        $elections = $electionModel->findAll();
        
        // Get users for selection
        $userModel = new \App\Models\UserModel();
        $users = $userModel->select('users.*, faculties.name as faculty_name, departments.name as department_name')
                          ->join('faculties', 'faculties.id = users.faculty_id', 'left')
                          ->join('departments', 'departments.id = users.department_id', 'left')
                          ->findAll();
        
        // Get candidate details
        $candidateDetails = $candidateModel->getCandidateWithDetails($id);
        
        $data = [
            'title' => 'Edit Kandidat - Admin',
            'page' => 'admin-edit-candidate',
            'candidate' => $candidate,
            'candidateDetails' => $candidateDetails,
            'elections' => $elections,
            'users' => $users
        ];
        
        return $this->render('frontend/pages/admin/edit_candidate', $data);
    }
}