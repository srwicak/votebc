<?php

namespace App\Controllers;

use App\Models\CandidateModel;
use App\Models\ElectionModel;
use App\Models\UserModel;

class Candidate extends BaseController
{
    /**
     * Show candidate profile page for self-update
     *
     * @return string
     */
    public function profile()
    {
        try {
            // Require authentication and ensure user is a candidate
            $user = $this->requireRole(['kandidat']);
            
            // Get candidate information for this user
            $candidateModel = new CandidateModel();
            $candidates = $candidateModel->where('candidate_id', $user['id'])->findAll();
            
            // If user is not a candidate, redirect to profile
            if (empty($candidates)) {
                return redirect()->to('/profile')->with('error', 'Anda bukan kandidat dalam pemilihan apapun');
            }
            
            // Get the first active candidate (we'll show one at a time)
            $activeCandidate = null;
            $electionModel = new ElectionModel();
            
            foreach ($candidates as $candidate) {
                $election = $electionModel->find($candidate['election_id']);
                if ($election && $election['status'] === 'active') {
                    $activeCandidate = $candidate;
                    break;
                }
            }
            
            // If no active candidate found, use the first one
            if (!$activeCandidate) {
                $activeCandidate = $candidates[0];
            }
            
            // Get candidate details with running mate info
            $candidate = $candidateModel->getCandidatePairWithDetails($activeCandidate['id']);
            
            // Get election details
            $election = $electionModel->find($candidate['election_id']);
            
            // Check if profile is incomplete and set notification if needed
            if (empty($candidate['vision']) || empty($candidate['mission']) || empty($candidate['photo'])) {
                // Only set the notification if it's not already set (to avoid showing it repeatedly)
                if (!session()->has('candidate_notification')) {
                    session()->setFlashdata('candidate_notification', 'Silakan lengkapi profil kandidat Anda untuk meningkatkan peluang terpilih dalam pemilihan.');
                    session()->setFlashdata('candidate_id', $candidate['id']);
                }
            }
            
            // Get running mate details if exists
            $runningMate = null;
            if (!empty($candidate['vice_candidate_id'])) {
                $userModel = new UserModel();
                $runningMate = $userModel->getUserWithAcademic($candidate['vice_candidate_id']);
            }
            
            $data = [
                'title' => 'Profil Kandidat - E-Voting BEM',
                'page' => 'candidate-profile',
                'candidate' => $candidate,
                'election' => $election,
                'user' => $user,
                'runningMate' => $runningMate
            ];
            
            return $this->render('frontend/pages/candidate_profile', $data);
            
        } catch (\Exception $e) {
            return redirect()->to('/profile')->with('error', $e->getMessage());
        }
    }
    
    /**
     * Update candidate information
     *
     * @param int $id Candidate ID
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        try {
            // Require authentication and ensure user is a candidate
            $user = $this->requireRole(['kandidat']);
            
            // Get candidate information
            $candidateModel = new CandidateModel();
            $candidate = $candidateModel->find($id);
            
            // Validate that this candidate belongs to the current user
            if (!$candidate || $candidate['candidate_id'] != $user['id']) {
                return redirect()->to('/profile')->with('error', 'Anda tidak memiliki akses untuk mengubah kandidat ini');
            }
            
            // Handle file upload
            $photo = $this->request->getFile('photo');
            $photoPath = $candidate['photo']; // Keep existing photo by default
            
            if ($photo && $photo->isValid() && !$photo->hasMoved()) {
                $newName = $photo->getRandomName();
                $photo->move(ROOTPATH . 'public/uploads/candidates', $newName);
                $photoPath = base_url('uploads/candidates/' . $newName);
            }
            
            // Prepare data for update
            $data = [
                'id' => $id,
                'vision' => $this->request->getPost('vision'),
                'mission' => $this->request->getPost('mission'),
                'programs' => $this->request->getPost('programs'),
                'photo' => $photoPath
            ];
            
            // Update candidate
            if (!$candidateModel->save($data)) {
                return redirect()->to('/candidate/profile')->with('error', 'Gagal memperbarui profil kandidat: ' . implode(', ', $candidateModel->errors()));
            }
            
            // If this is a paired candidate and the current user is the primary candidate,
            // check if running mate also needs to update their information
            if (!empty($candidate['vice_candidate_id']) && $candidate['candidate_id'] == $user['id']) {
                // Notify the running mate to complete their information if needed
                $userModel = new UserModel();
                $runningMate = $userModel->find($candidate['vice_candidate_id']);
                
                if ($runningMate) {
                    // Store notification for running mate in database or session
                    // This is a simplified implementation - in a real app, you might use a notification system
                    $notificationModel = new \App\Models\NotificationModel();
                    $notificationModel->save([
                        'user_id' => $candidate['vice_candidate_id'],
                        'message' => 'Anda telah ditambahkan sebagai wakil kandidat. Silakan lengkapi profil kandidat Anda.',
                        'link' => '/candidate/profile',
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            
            return redirect()->to('/candidate/profile')->with('success', 'Profil kandidat berhasil diperbarui');
            
        } catch (\Exception $e) {
            return redirect()->to('/candidate/profile')->with('error', $e->getMessage());
        }
    }
    
    /**
     * Render view with standard layout
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