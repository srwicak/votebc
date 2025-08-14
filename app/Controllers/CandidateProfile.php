<?php

namespace App\Controllers;

use App\Models\CandidateModel;
use App\Models\ElectionModel;
use App\Models\UserModel;

class CandidateProfile extends BaseController
{
    /**
     * Constructor yang akan mengecek apakah user sudah login
     */
    public function __construct()
    {
        // Panggil parent constructor terlebih dahulu
        parent::initController(service('request'), service('response'), service('logger'));
        
        // Inisialisasi data user dari session
        $this->checkUserSession();
    }
    
    /**
     * Memeriksa session user untuk autentikasi
     */
    private function checkUserSession()
    {
        log_message('debug', 'CandidateProfile: Checking user session');
        
        // Coba cara 1: Menggunakan token dari session
        $authToken = session()->get('auth_token');
        log_message('debug', 'Auth token in session: ' . ($authToken ? 'exists' : 'not exists'));
        
        if ($authToken) {
            try {
                $jwt = new \App\Libraries\JWT();
                $decoded = $jwt->decode($authToken);
                if ($decoded) {
                    $userModel = new \App\Models\UserModel();
                    $this->userData = $userModel->getUserWithAcademic($decoded['user_id']);
                    $this->isLoggedIn = (bool) $this->userData;
                    log_message('debug', 'User logged in via token: ' . ($this->isLoggedIn ? 'yes' : 'no'));
                    return; // Berhasil login, keluar dari fungsi
                }
            } catch (\Exception $e) {
                log_message('error', 'Error decoding token: ' . $e->getMessage());
                session()->remove('auth_token');
            }
        }
        
        // Cara 2: Cek apakah ada user_id langsung di session (untuk kompatibilitas)
        if (session()->has('user_id')) {
            log_message('debug', 'Found user_id directly in session: ' . session()->get('user_id'));
            $userModel = new \App\Models\UserModel();
            $this->userData = $userModel->getUserWithAcademic(session()->get('user_id'));
            $this->isLoggedIn = (bool) $this->userData;
            log_message('debug', 'User logged in via user_id: ' . ($this->isLoggedIn ? 'yes' : 'no'));
        }
    }
    
    /**
     * Halaman profil kandidat
     *
     * @return string
     */
    public function index()
    {
        try {
            // Cek apakah user sudah login
            if (!$this->isLoggedIn || !$this->userData) {
                log_message('debug', 'CandidateProfile: User not logged in, redirecting to login');
                return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
            }
            
            log_message('debug', 'CandidateProfile: User logged in as ' . $this->userData['name']);
            $user = $this->userData;
            
            // Dapatkan semua pemilihan di mana user adalah kandidat (baik ketua atau wakil)
            $candidateModel = new CandidateModel();
            log_message('debug', 'CandidateProfile: Looking for candidacies for user ID ' . $user['id']);
            
            // Gunakan metode Model langsung untuk query, tanpa getCompiledSelect()
            $candidates = $candidateModel->select('candidates.*')
                ->groupStart()
                    ->where('candidate_id', $user['id'])
                    ->orWhere('vice_candidate_id', $user['id'])
                ->groupEnd()
                ->findAll();
                
            log_message('debug', 'CandidateProfile: Found ' . count($candidates) . ' candidacies');
                
            log_message('debug', 'CandidateProfile: Found ' . count($candidates) . ' candidacies');
            
            // Jika user bukan kandidat, redirect ke dashboard
            if (empty($candidates)) {
                log_message('debug', 'CandidateProfile: User is not a candidate in any election, redirecting to dashboard');
                return redirect()->to('/dashboard')->with('error', 'Anda bukan kandidat dalam pemilihan apapun');
            }
            
            // Cek apakah ada kandidat yang dipilih dari session
            $selectedCandidateId = session()->get('selected_candidate_id');
            $activeCandidate = null;
            $electionModel = new ElectionModel();
            
            if ($selectedCandidateId) {
                log_message('debug', 'CandidateProfile: Using selected candidate ID from session: ' . $selectedCandidateId);
                // Temukan kandidat yang dipilih dari array kandidat
                foreach ($candidates as $candidate) {
                    if ($candidate['id'] == $selectedCandidateId) {
                        $activeCandidate = $candidate;
                        break;
                    }
                }
                
                // Jika kandidat yang dipilih tidak ditemukan dalam kandidat user, reset session
                if (!$activeCandidate) {
                    session()->remove('selected_candidate_id');
                    log_message('debug', 'CandidateProfile: Selected candidate not found in user candidates, resetting session');
                }
            }
            
            // Jika tidak ada kandidat yang dipilih dari session atau kandidat tidak ditemukan,
            // coba cari kandidat yang aktif
            if (!$activeCandidate) {
                log_message('debug', 'CandidateProfile: Looking for active candidate');
                foreach ($candidates as $candidate) {
                    $election = $electionModel->find($candidate['election_id']);
                    if ($election && $election['status'] === 'active') {
                        $activeCandidate = $candidate;
                        log_message('debug', 'CandidateProfile: Found active candidate ID: ' . $activeCandidate['id']);
                        break;
                    }
                }
            }
            
            // Jika tidak ada kandidat aktif, gunakan yang pertama
            if (!$activeCandidate) {
                log_message('debug', 'CandidateProfile: No active candidate found, using first one');
                $activeCandidate = $candidates[0];
            }
            
            // Dapatkan detail kandidat bersama dengan info pasangan
            log_message('debug', 'CandidateProfile: Getting details for candidate ID ' . $activeCandidate['id']);
            $candidateDetail = $candidateModel->getCandidatePairWithDetails($activeCandidate['id']);
            
            if (!$candidateDetail) {
                log_message('error', 'CandidateProfile: Failed to get candidate details');
                return redirect()->to('/dashboard')->with('error', 'Terjadi kesalahan saat mengambil detail kandidat');
            }
            
            log_message('debug', 'CandidateProfile: Candidate details retrieved successfully');
            
            // Dapatkan detail pemilihan
            $election = $electionModel->find($candidateDetail['election_id']);
            
            // Periksa apakah profil belum lengkap dan set notifikasi jika diperlukan
            if (empty($candidateDetail['vision']) || empty($candidateDetail['mission']) || empty($candidateDetail['photo'])) {
                session()->setFlashdata('notification', 'Silakan lengkapi profil kandidat Anda untuk meningkatkan peluang terpilih dalam pemilihan.');
            }
            
            // Cek apakah user adalah ketua atau wakil dalam kandidat ini
            $isMainCandidate = ($candidateDetail['candidate_id'] == $user['id']);
            
            $data = [
                'title' => 'Profil Kandidat',
                'page' => 'candidate-profile',
                'candidate' => $candidateDetail,
                'election' => $election,
                'user' => $user,
                'isMainCandidate' => $isMainCandidate,
                'allCandidates' => $candidates
            ];
            
            return $this->render('frontend/pages/candidate/profile', $data);
            
        } catch (\Exception $e) {
            return redirect()->to('/')->with('error', $e->getMessage());
        }
    }
    
    /**
     * Perbarui profil kandidat
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update()
    {
        try {
            // Cek apakah user sudah login
            if (!$this->isLoggedIn || !$this->userData) {
                return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
            }
            
            $user = $this->userData;
            
            // Ambil ID kandidat dari form
            $candidateId = $this->request->getPost('candidate_id');
            if (!$candidateId) {
                return redirect()->to('/candidate-profile')->with('error', 'ID kandidat tidak ditemukan');
            }
            
            // Dapatkan informasi kandidat
            $candidateModel = new CandidateModel();
            $candidate = $candidateModel->find($candidateId);
            
            // Validasi bahwa user adalah bagian dari pasangan kandidat ini
            if (!$candidate || ($candidate['candidate_id'] != $user['id'] && $candidate['vice_candidate_id'] != $user['id'])) {
                return redirect()->to('/candidate-profile')->with('error', 'Anda tidak memiliki akses untuk mengubah kandidat ini');
            }
            
        // Handle upload foto
        $photo = $this->request->getFile('photo');
        $photoPath = $candidate['photo']; // Simpan foto yang sudah ada secara default
        $hasNewPhoto = false; // Flag untuk tracking apakah ada foto baru
        
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            log_message('debug', 'Uploading photo: ' . $photo->getName());
            
            // Pastikan direktori upload exists
            $uploadPath = ROOTPATH . 'public/uploads/candidates';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
                log_message('debug', 'Created upload directory: ' . $uploadPath);
            }
            
            $newName = $photo->getRandomName();
            if ($photo->move($uploadPath, $newName)) {
                $photoPath = 'uploads/candidates/' . $newName; // Store relative path only
                $hasNewPhoto = true; // Set flag bahwa ada foto baru yang berhasil di-upload
                log_message('debug', 'Photo uploaded successfully: ' . $photoPath);
            } else {
                log_message('error', 'Failed to move photo file');
            }
        } else {
            log_message('debug', 'No valid photo to upload');
        }
        
        // Siapkan data untuk update
        $data = [
            'id' => $candidateId,
            'vision' => $this->request->getPost('vision'),
            'mission' => $this->request->getPost('mission'),
            'programs' => $this->request->getPost('programs')
        ];
        
        // Update foto jika ada upload baru yang berhasil
        if ($hasNewPhoto) {
            $data['photo'] = $photoPath;
            log_message('debug', 'Adding photo to update data: ' . $photoPath);
        }
        
        log_message('debug', 'Update data: ' . json_encode($data));            // Update kandidat
            if (!$candidateModel->save($data)) {
                return redirect()->to('/candidate-profile')->with('error', 'Gagal memperbarui profil kandidat: ' . implode(', ', $candidateModel->errors()));
            }
            
            return redirect()->to('/candidate-profile')->with('success', 'Profil kandidat berhasil diperbarui');
            
        } catch (\Exception $e) {
            return redirect()->to('/candidate-profile')->with('error', $e->getMessage());
        }
    }
    
    /**
     * Berganti ke kandidat lain (jika user adalah kandidat di beberapa pemilihan)
     *
     * @param int $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function switchCandidate($id)
    {
        try {
            log_message('debug', 'CandidateProfile::switchCandidate: Called with ID: ' . $id);
            
            // Cek apakah user sudah login
            if (!$this->isLoggedIn || !$this->userData) {
                log_message('debug', 'CandidateProfile::switchCandidate: User not logged in, redirecting to login');
                return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
            }
            
            $user = $this->userData;
            log_message('debug', 'CandidateProfile::switchCandidate: User logged in as ' . $user['name'] . ' (ID: ' . $user['id'] . ')');
            
            // Dapatkan informasi kandidat
            $candidateModel = new CandidateModel();
            $candidate = $candidateModel->find($id);
            
            if (!$candidate) {
                log_message('debug', 'CandidateProfile::switchCandidate: Candidate ID ' . $id . ' not found');
                return redirect()->to('/candidate-profile')->with('error', 'Kandidat tidak ditemukan');
            }
            
            log_message('debug', 'CandidateProfile::switchCandidate: Found candidate for election ' . $candidate['election_id']);
            
            // Validasi bahwa user adalah bagian dari pasangan kandidat ini
            if ($candidate['candidate_id'] != $user['id'] && $candidate['vice_candidate_id'] != $user['id']) {
                log_message('debug', 'CandidateProfile::switchCandidate: User is not part of this candidate pair');
                return redirect()->to('/candidate-profile')->with('error', 'Anda tidak memiliki akses untuk melihat kandidat ini');
            }
            
            log_message('debug', 'CandidateProfile::switchCandidate: User is part of this candidate pair');
            
            // Simpan ID kandidat yang dipilih di session
            session()->set('selected_candidate_id', $id);
            log_message('debug', 'CandidateProfile::switchCandidate: Set selected_candidate_id in session to ' . $id);
            
            return redirect()->to('/candidate-profile');
            
        } catch (\Exception $e) {
            log_message('error', 'CandidateProfile::switchCandidate: Error - ' . $e->getMessage());
            return redirect()->to('/candidate-profile')->with('error', $e->getMessage());
        }
    }
    
    /**
     * Render view dengan layout standar
     *
     * @param string $view
     * @param array $data
     * @return string
     */
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
}
