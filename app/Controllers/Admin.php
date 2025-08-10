<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\FacultyModel;
use App\Models\DepartmentModel;
use App\Models\ElectionModel;
use App\Models\CandidateModel;

class Admin extends BaseController{

    public function addPairedCandidates()
    {
        try {
            $this->requireRole(['admin']);
            $json = $this->request->getJSON(true);
            $electionId = $json['election_id'] ?? null;
            $pairs = $json['pairs'] ?? [];
            if (!$electionId || empty($pairs)) {
                return $this->sendError('election_id dan pairs wajib diisi', 400);
            }
            $candidateModel = new CandidateModel();
            $userModel = new UserModel();
            $eligibilityModel = new \App\Models\EligibilityModel();
            $results = [];
            foreach ($pairs as $pair) {
                $ketua = $userModel->where('nim', $pair['nim_ketua'] ?? '')->first();
                $wakil = $userModel->where('nim', $pair['nim_wakil'] ?? '')->first();
                $result = [
                    'nim_ketua' => $pair['nim_ketua'] ?? '',
                    'nim_wakil' => $pair['nim_wakil'] ?? '',
                    'status' => 'success',
                    'message' => ''
                ];
                if (!$ketua || !$wakil) {
                    $result['status'] = 'error';
                    $result['message'] = 'NIM ketua/wakil tidak ditemukan';
                    $results[] = $result;
                    continue;
                }
                if ($pair['nim_ketua'] === $pair['nim_wakil']) {
                    $result['status'] = 'error';
                    $result['message'] = 'NIM ketua dan wakil tidak boleh sama.';
                    $results[] = $result;
                    continue;
                }
                if (!$eligibilityModel->isUserEligible($electionId, $ketua['id'])) {
                    $result['status'] = 'error';
                    $result['message'] = "Ketua dengan NIM {$pair['nim_ketua']} tidak eligible untuk pemilihan ini.";
                    $results[] = $result;
                    continue;
                }
                if (!$eligibilityModel->isUserEligible($electionId, $wakil['id'])) {
                    $result['status'] = 'error';
                    $result['message'] = "Wakil dengan NIM {$pair['nim_wakil']} tidak eligible untuk pemilihan ini.";
                    $results[] = $result;
                    continue;
                }
                $data = [
                    'candidate_id' => $ketua['id'],
                    'vice_candidate_id' => $wakil['id'],
                    'election_id' => $electionId,
                ];
                if (!$candidateModel->insert($data)) {
                    $result['status'] = 'errorx';
                    $result['message'] = json_encode($candidateModel->errors());
                }
                $results[] = $result;
            }
            return $this->sendResponse([
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function createCandidate($electionId)
    {
        $electionModel = new ElectionModel();
        $election = $electionModel->find($electionId);
        if (!$election) {
            return redirect()->back()->with('error', 'Election tidak ditemukan');
        }
        return view('admin/create_candidate', ['election' => $election]);
    }

    public function storeCandidate($electionId)
    {
        $candidateModel = new CandidateModel();
        $userModel = new UserModel();
        $eligibilityModel = new \App\Models\EligibilityModel();

        $pairs = $this->request->getPost('pairs'); // array: [['nim_ketua' => ..., 'nim_wakil' => ...], ...]

        $errors = [];
        foreach ($pairs as $pair) {
            $ketua = $userModel->where('nim', $pair['nim_ketua'])->first();
            $wakil = $userModel->where('nim', $pair['nim_wakil'])->first();

            if (!$ketua || !$wakil) {
                $errors[] = "NIM ketua/wakil tidak ditemukan";
                continue;
            }

            // Cek eligibility ketua
            if (!$eligibilityModel->isUserEligible($electionId, $ketua['id'])) {
                $errors[] = "Ketua dengan NIM {$pair['nim_ketua']} tidak eligible untuk pemilihan ini.";
                continue;
            }
            // Cek eligibility wakil
            if (!$eligibilityModel->isUserEligible($electionId, $wakil['id'])) {
                $errors[] = "Wakil dengan NIM {$pair['nim_wakil']} tidak eligible untuk pemilihan ini.";
                continue;
            }

            $data = [
                'candidate_id' => $ketua['id'],
                'vice_candidate_id' => $wakil['id'],
                'election_id' => $electionId,
            ];
            if (!$candidateModel->insert($data)) {
                $errors[] = $candidateModel->errors();
            }
        }

        if ($errors) {
            return redirect()->back()->with('error', implode(', ', $errors));
        }
        return redirect()->to("/admin/elections/$electionId")->with('success', 'Kandidat berhasil ditambahkan');
    }

    public function getUsers()
    {
        try {
            $this->requireRole(['admin']);

            $userModel = new UserModel();
            $users = $userModel->findAll();

            return $this->sendResponse($users);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getUser($userId)
    {
        try {
            $this->requireRole(['admin']);

            $userModel = new UserModel();
            $user = $userModel->getUserWithAcademic($userId);

            if (!$user) {
                return $this->sendError('User tidak ditemukan', 404);
            }

            return $this->sendResponse($user);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateUserRole($userId)
    {
        try {
            $currentUser = $this->requireRole(['admin']);

            $userModel = new UserModel();
            $user = $userModel->find($userId);

            if (!$user) {
                return $this->sendError('User tidak ditemukan', 404);
            }

            // Super admin tidak bisa diubah
            if ($user['is_super_admin']) {
                return $this->sendError('Tidak bisa mengubah role super admin', 403);
            }

            $input = $this->request->getJSON();
            $newRole = $input->role ?? null;
            $validRoles = ['admin', 'mahasiswa', 'kandidat'];

            if (!in_array($newRole, $validRoles)) {
                return $this->sendError('Role tidak valid', 400);
            }

            $userModel->update($userId, ['role' => $newRole]);

            return $this->sendResponse([
                'message' => 'Role berhasil diubah',
                'user' => $userModel->find($userId)
            ]);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateUser($userId)
    {
        try {
            $currentUser = $this->requireRole(['admin']);

            $userModel = new UserModel();
            $user = $userModel->find($userId);

            if (!$user) {
                return $this->sendError('User tidak ditemukan', 404);
            }

            if ($user['is_super_admin'] && $currentUser['id'] != $userId) {
                return $this->sendError('Tidak bisa mengubah super admin', 403);
            }

            // Ambil input JSON
            $input = $this->request->getJSON(true); // true = array

            $data = [];

            // Cek nim, hanya update jika beda
            if (!empty($input['nim']) && $input['nim'] !== $user['nim']) {
                $data['nim'] = $input['nim'];
            }

            if (!empty($input['name'])) {
                $data['name'] = $input['name'];
            }
            if (!empty($input['faculty_id'])) {
                $data['faculty_id'] = $input['faculty_id'];
            }
            if (!empty($input['department_id'])) {
                $data['department_id'] = $input['department_id'];
            }
            if (!empty($input['role'])) {
                $data['role'] = $input['role'];
            }
            if (!empty($input['status'])) {
                $data['status'] = $input['status'];
            }

            if (!empty($input['password'])) {
                $data['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
            }

            if (empty($data)) {
                return $this->sendError('Tidak ada data yang dikirim', 400);
            }

            // Update
            if (!$userModel->update($userId, $data)) {
                return $this->sendError($userModel->errors(), 400);
            }

            $updatedUser = $userModel->getUserWithAcademic($userId);

            return $this->sendResponse([
                'message' => 'User berhasil diubah',
                'user' => $updatedUser
            ]);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }



    public function createFaculty()
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code')
            ];

            $facultyModel = new FacultyModel();

            if (!$facultyModel->save($data)) {
                return $this->sendError($facultyModel->errors(), 400);
            }

            return $this->sendResponse([
                'message' => 'Fakultas berhasil dibuat',
                'faculty' => $facultyModel->find($facultyModel->getInsertID())
            ], 201);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function createDepartment()
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code'),
                'faculty_id' => $this->request->getPost('faculty_id')
            ];

            $departmentModel = new DepartmentModel();

            if (!$departmentModel->save($data)) {
                return $this->sendError($departmentModel->errors(), 400);
            }

            return $this->sendResponse([
                'message' => 'Jurusan berhasil dibuat',
                'department' => $departmentModel->find($departmentModel->getInsertID())
            ], 201);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function createElection()
    {
        try {
            $currentUser = $this->requireRole(['admin']);

            // Get JSON input
            $json = $this->request->getJSON(true);
            if (!$json) {
                return $this->sendError('Invalid JSON input', 400);
            }

            // Prepare election data
            $data = [
                'title' => $json['title'] ?? null,
                'description' => $json['description'] ?? null,
                'level' => $json['level'] ?? null,
                'status' => $json['status'] ?? 'draft',
                'created_by' => $currentUser['id'],
            ];

            // Process date fields to ensure proper format
            if (isset($json['start_time']) && !empty($json['start_time'])) {
                try {
                    // Convert to DateTime object to ensure proper format
                    $startTime = new \DateTime($json['start_time']);
                    $data['start_time'] = $startTime->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    return $this->sendError("Format waktu mulai tidak valid", 400);
                }
            }

            if (isset($json['end_time']) && !empty($json['end_time'])) {
                try {
                    // Convert to DateTime object to ensure proper format
                    $endTime = new \DateTime($json['end_time']);
                    $data['end_time'] = $endTime->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    return $this->sendError("Format waktu selesai tidak valid", 400);
                }
            }

            // Manual validation for end_time > start_time using DateTime
            if (isset($data['start_time']) && isset($data['end_time'])) {
                $startTime = new \DateTime($data['start_time']);
                $endTime = new \DateTime($data['end_time']);

                if ($endTime <= $startTime) {
                    return $this->sendError("Waktu selesai harus lebih besar dari waktu mulai", 400);
                }
            }

            // Validate required fields
            $requiredFields = ['title', 'description', 'level', 'start_time', 'end_time'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return $this->sendError("Field {$field} harus diisi", 400);
                }
            }

            $electionModel = new ElectionModel();
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // Save election
                if (!$electionModel->save($data)) {
                    $db->transRollback();
                    return $this->sendError($electionModel->errors(), 400);
                }

                $electionId = $electionModel->getInsertID();

                // Handle eligibility if not all students
                if (isset($json['all_students']) && !$json['all_students'] && isset($json['eligibility']) && is_array($json['eligibility'])) {
                    $eligibilityModel = new \App\Models\EligibilityModel();
                    foreach ($json['eligibility'] as $eligibility) {
                        if (!isset($eligibility['faculty_id']) || empty($eligibility['faculty_id'])) {
                            continue;
                        }
                        $eligibilityData = [
                            'election_id' => $electionId,
                            'faculty_id' => $eligibility['faculty_id'],
                            'department_id' => !empty($eligibility['department_id']) ? $eligibility['department_id'] : null
                        ];
                        if (!$eligibilityModel->save($eligibilityData)) {
                            $db->transRollback();
                            return $this->sendError($eligibilityModel->errors(), 400);
                        }
                    }
                }

                $db->transCommit();
                $election = $electionModel->getElectionWithDetails($electionId);

                return $this->sendResponse([
                    'message' => 'Pemilihan berhasil dibuat',
                    'election' => $election
                ], 201);
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getElections()
    {
        try {
            $this->requireRole(['admin']);

            $electionModel = new ElectionModel();
            $elections = $electionModel->orderBy('created_at', 'DESC')->findAll();

            // Get candidates count for each election
            $candidateModel = new CandidateModel();
            foreach ($elections as &$election) {
                $election['candidates_count'] = $candidateModel->where('election_id', $election['id'])->countAllResults();
            }

            return $this->sendResponse($elections);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getElection($id)
    {
        try {
            $this->requireRole(['admin']);

            $electionModel = new ElectionModel();
            $election = $electionModel->getElectionWithDetails($id);

            if (!$election) {
                return $this->sendError('Pemilihan tidak ditemukan', 404);
            }

            // Get eligibility
            $eligibilityModel = new \App\Models\EligibilityModel();
            $eligibility = $eligibilityModel->where('election_id', $id)->findAll();
            $election['eligibility'] = $eligibility;

            // Get candidates
            $candidateModel = new CandidateModel();
            $candidates = $candidateModel->getCandidatesWithUser($id);
            $election['candidates'] = $candidates;

            return $this->sendResponse($election);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateElection($id)
    {
        try {
            $currentUser = $this->requireRole(['admin']);

            $electionModel = new ElectionModel();
            $election = $electionModel->find($id);

            if (!$election) {
                return $this->sendError('Pemilihan tidak ditemukan', 404);
            }

            // Get JSON input
            $json = $this->request->getJSON(true);
            if (!$json) {
                return $this->sendError('Invalid JSON input', 400);
            }

            // Prepare election data
            $data = [
                'id' => $id,
                'title' => $json['title'] ?? $election['title'],
                'description' => $json['description'] ?? $election['description'],
                'level' => $json['level'] ?? $election['level'],
                'status' => $json['status'] ?? $election['status'],
            ];

            // Process start_time if provided
            if (isset($json['start_time']) && !empty($json['start_time'])) {
                try {
                    // Convert to DateTime object to ensure proper format
                    $startTime = new \DateTime($json['start_time']);
                    $data['start_time'] = $startTime->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    return $this->sendError("Format waktu mulai tidak valid", 400);
                }
            } else {
                $data['start_time'] = $election['start_time'];
            }

            // Process end_time if provided
            if (isset($json['end_time']) && !empty($json['end_time'])) {
                try {
                    // Convert to DateTime object to ensure proper format
                    $endTime = new \DateTime($json['end_time']);
                    $data['end_time'] = $endTime->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    return $this->sendError("Format waktu selesai tidak valid", 400);
                }
            } else {
                $data['end_time'] = $election['end_time'];
            }

            // Manual validation for end_time > start_time
            $startTime = strtotime($data['start_time']);
            $endTime = strtotime($data['end_time']);

            if ($endTime <= $startTime) {
                return $this->sendError("Waktu selesai harus lebih besar dari waktu mulai", 400);
            }

            // Begin transaction
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // Update election
                if (!$electionModel->save($data)) {
                    $db->transRollback();
                    return $this->sendError($electionModel->errors(), 400);
                }

                // Handle eligibility if not all students
                if (isset($json['all_students']) && !$json['all_students'] && isset($json['eligibility']) && is_array($json['eligibility'])) {
                    $eligibilityModel = new \App\Models\EligibilityModel();

                    // Delete existing eligibility
                    $eligibilityModel->where('election_id', $id)->delete();

                    // Add new eligibility
                    foreach ($json['eligibility'] as $eligibility) {
                        if (!isset($eligibility['faculty_id']) || empty($eligibility['faculty_id'])) {
                            continue;
                        }

                        $eligibilityData = [
                            'election_id' => $id,
                            'faculty_id' => $eligibility['faculty_id'],
                            'department_id' => $eligibility['department_id'] ?? null
                        ];

                        if (!$eligibilityModel->save($eligibilityData)) {
                            $db->transRollback();
                            return $this->sendError($eligibilityModel->errors(), 400);
                        }
                    }
                } elseif (isset($json['all_students']) && $json['all_students']) {
                    // Delete all eligibility if all students
                    $eligibilityModel = new \App\Models\EligibilityModel();
                    $eligibilityModel->where('election_id', $id)->delete();
                }

                $db->transCommit();

                $updatedElection = $electionModel->getElectionWithDetails($id);

                return $this->sendResponse([
                    'message' => 'Pemilihan berhasil diperbarui',
                    'election' => $updatedElection
                ]);
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function deleteElection($id)
    {
        try {
            $this->requireRole(['admin']);

            $electionModel = new ElectionModel();
            $election = $electionModel->find($id);

            if (!$election) {
                return $this->sendError('Pemilihan tidak ditemukan', 404);
            }

            // Check if there are votes for this election
            $voteModel = new \App\Models\VoteModel();
            $votes = $voteModel->where('election_id', $id)->findAll();

            if (!empty($votes)) {
                return $this->sendError('Tidak dapat menghapus pemilihan yang sudah memiliki vote', 400);
            }

            // Begin transaction
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // Delete candidates
                $candidateModel = new CandidateModel();
                $candidateModel->where('election_id', $id)->delete();

                // Delete eligibility
                $eligibilityModel = new \App\Models\EligibilityModel();
                $eligibilityModel->where('election_id', $id)->delete();

                // Delete election
                if (!$electionModel->delete($id)) {
                    $db->transRollback();
                    return $this->sendError('Gagal menghapus pemilihan', 500);
                }

                $db->transCommit();

                return $this->sendResponse([
                    'message' => 'Pemilihan berhasil dihapus'
                ]);
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Add a paired candidate to an election
     *
     * @return mixed
     */
    /**
     * Add a paired candidate to an election
     * Admin only needs to select users, and the users will fill in their own details
     *
     * @return mixed
     */
    public function addPairedCandidate()
    {
        try {
            $this->requireRole(['admin']);

            $data = [
                'user_id' => $this->request->getPost('user_id'),
                'election_id' => $this->request->getPost('election_id'),
                // Set empty defaults for fields that will be filled by the candidate
                'vision' => '',
                'mission' => '',
                'programs' => '',
                'photo' => null,
            ];

            // Get running mate ID if provided
            $runningMateId = $this->request->getPost('running_mate_id');
            if ($runningMateId) {
                $data['running_mate_id'] = $runningMateId;
            }

            // Validate that the user is eligible for this election based on level
            $electionModel = new ElectionModel();
            $election = $electionModel->find($data['election_id']);
            $userModel = new UserModel();
            $user = $userModel->getUserWithAcademic($data['user_id']);

            if (!$election || !$user) {
                return $this->sendError('Election or user not found', 404);
            }

            // Check eligibility based on election level
            if ($election['level'] === 'fakultas' && $user['faculty_id'] != $election['target_id']) {
                return $this->sendError('User is not eligible for this faculty-level election', 400);
            }

            if ($election['level'] === 'jurusan' && $user['department_id'] != $election['target_id']) {
                return $this->sendError('User is not eligible for this department-level election', 400);
            }

            // For university level, all users are eligible (no additional check needed)

            // Validate required fields (only user_id and election_id)
            $requiredFields = ['user_id', 'election_id'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return $this->sendError("Field {$field} harus diisi", 400);
                }
            }

            $candidateModel = new CandidateModel();

            // Check if user is already a candidate in this election
            $existingCandidate = $candidateModel->where('user_id', $data['user_id'])
                                               ->where('election_id', $data['election_id'])
                                               ->first();

            if ($existingCandidate) {
                return $this->sendError('Mahasiswa ini sudah menjadi kandidat dalam pemilihan ini', 400);
            }

            // If running mate is provided, validate the running mate
            if ($runningMateId) {
                // Check if running mate is the same as primary candidate
                if ($runningMateId == $data['user_id']) {
                    return $this->sendError('Running mate cannot be the same as primary candidate', 400);
                }

                // Check if running mate is eligible for this election
                $runningMate = $userModel->getUserWithAcademic($runningMateId);
                if (!$runningMate) {
                    return $this->sendError('Running mate not found', 404);
                }

                // Check eligibility based on election level for running mate
                if ($election['level'] === 'fakultas' && $runningMate['faculty_id'] != $election['target_id']) {
                    return $this->sendError('Running mate is not eligible for this faculty-level election', 400);
                }

                if ($election['level'] === 'jurusan' && $runningMate['department_id'] != $election['target_id']) {
                    return $this->sendError('Running mate is not eligible for this department-level election', 400);
                }

                // Check if either user is already in an active candidate pair for an election of the same level
                if ($candidateModel->isUserInActiveElection($data['user_id'], $election['level'])) {
                    return $this->sendError('Primary candidate is already part of an active candidate pair in an election of the same level', 400);
                }

                if ($candidateModel->isUserInActiveElection($runningMateId, $election['level'])) {
                    return $this->sendError('Running mate is already part of an active candidate pair in an election of the same level', 400);
                }

                // Check if running mate is already a candidate in this election
                $existingRunningMate = $candidateModel->where('user_id', $runningMateId)
                                                     ->where('election_id', $data['election_id'])
                                                     ->first();

                if ($existingRunningMate) {
                    return $this->sendError('Running mate sudah menjadi kandidat dalam pemilihan ini', 400);
                }

                // Check if running mate is already a running mate in this election
                $existingAsRunningMate = $candidateModel->where('running_mate_id', $runningMateId)
                                                       ->where('election_id', $data['election_id'])
                                                       ->first();

                if ($existingAsRunningMate) {
                    return $this->sendError('Running mate sudah menjadi wakil kandidat dalam pemilihan ini', 400);
                }
            }

            // Begin transaction
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // Save candidate
                if (!$candidateModel->save($data)) {
                    $db->transRollback();
                    return $this->sendError($candidateModel->errors(), 400);
                }

                $candidateId = $candidateModel->getInsertID();

                // Update primary candidate role to kandidat
                $userModel->update($data['user_id'], ['role' => 'kandidat']);

                // Update running mate role to kandidat if provided
                if ($runningMateId) {
                    $userModel->update($runningMateId, ['role' => 'kandidat']);
                }

                // Always record on blockchain
                $blockchain = new \App\Libraries\Blockchain();
                $blockchainResult = $blockchain->addCandidate(
                    $data['election_id'],
                    $userModel->find($data['user_id'])['name'],
                    json_encode([
                        'vision' => $data['vision'],
                        'mission' => $data['mission'],
                        'programs' => $data['programs']
                    ])
                );

                if ($blockchainResult['status'] === 'failed') {
                    $db->transRollback();
                    return $this->sendError('Gagal mencatat kandidat ke blockchain: ' . ($blockchainResult['error'] ?? 'Unknown error'), 500);
                }

                // Save blockchain transaction
                $txModel = new \App\Models\BlockchainTransactionModel();
                $txModel->save([
                    'election_id' => $data['election_id'],
                    'tx_hash' => $blockchainResult['transaction_hash'],
                    'tx_type' => 'add_candidate',
                    'status' => 'pending',
                    'data' => json_encode([
                        'candidate_id' => $candidateId,
                        'user_id' => $data['user_id'],
                        'election_id' => $data['election_id']
                    ]),
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $db->transCommit();

                // Send notifications to both primary candidate and running mate
                $this->sendCandidateNotification($data['user_id'], $candidateId, false);
                if ($runningMateId) {
                    $this->sendCandidateNotification($runningMateId, $candidateId, true);
                }

                $candidate = $candidateModel->getCandidatePairWithDetails($candidateId);

                return $this->sendResponse([
                    'message' => 'Kandidat berhasil ditambahkan',
                    'candidate' => $candidate
                ], 201);
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Add a single candidate to an election
     * Admin only needs to select user, and the user will fill in their own details
     *
     * @return mixed
     */
    public function addCandidate()
    {
        try {
            $this->requireRole(['admin']);

            $data = [
                'user_id' => $this->request->getPost('user_id'),
                'election_id' => $this->request->getPost('election_id'),
                // Set empty defaults for fields that will be filled by the candidate
                'vision' => '',
                'mission' => '',
                'programs' => '',
                'photo' => null,
            ];

            // Validate that the user is eligible for this election based on level
            $electionModel = new ElectionModel();
            $election = $electionModel->find($data['election_id']);
            $userModel = new UserModel();
            $user = $userModel->getUserWithAcademic($data['user_id']);

            if (!$election || !$user) {
                return $this->sendError('Election or user not found', 404);
            }

            // Check eligibility based on election level
            // if ($election['level'] === 'fakultas' && $user['faculty_id'] != $election['target_id']) {
            //     return $this->sendError('User is not eligible for this faculty-level election', 400);
            // }

            // if ($election['level'] === 'jurusan' && $user['department_id'] != $election['target_id']) {
            //     return $this->sendError('User is not eligible for this department-level election', 400);
            // }

            // For university level, all users are eligible (no additional check needed)

            // Validate required fields (only user_id and election_id)
            $requiredFields = ['user_id', 'election_id'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return $this->sendError("Field {$field} harus diisi", 400);
                }
            }

            $candidateModel = new CandidateModel();

            // Check if user is already a candidate in this election
            $existingCandidate = $candidateModel->where('user_id', $data['user_id'])
                                               ->where('election_id', $data['election_id'])
                                               ->first();

            if ($existingCandidate) {
                return $this->sendError('Mahasiswa ini sudah menjadi kandidat dalam pemilihan ini', 400);
            }

            // Begin transaction
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // Save candidate
                if (!$candidateModel->save($data)) {
                    $db->transRollback();
                    return $this->sendError($candidateModel->errors(), 400);
                }

                $candidateId = $candidateModel->getInsertID();

                // Update user role to kandidat
                $userModel = new UserModel();
                $userModel->update($data['user_id'], ['role' => 'kandidat']);

                return $this->sendResponse([
                    'message' => 'Kandidat berhasil ditambahkan',
                    'candidate' => $candidate
                ], 201);
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

public function getCandidates($electionId = null)
    {
        try {
            $this->requireRole(['admin']);

            $candidateModel = new CandidateModel();

            if ($electionId) {
                $candidates = $candidateModel->getCandidatesWithUser($electionId);
            } else {
                $candidates = $candidateModel->select('candidates.*, users.name as user_name, elections.title as election_title')
                                           ->join('users', 'users.id = candidates.user_id')
                                           ->join('elections', 'elections.id = candidates.election_id')
                                           ->findAll();
            }

            return $this->sendResponse($candidates);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getCandidate($id)
    {
        try {
            $this->requireRole(['admin']);

            $candidateModel = new CandidateModel();
            $candidate = $candidateModel->getCandidateWithDetails($id);

            if (!$candidate) {
                return $this->sendError('Kandidat tidak ditemukan', 404);
            }

            return $this->sendResponse($candidate);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateCandidate($id)
    {
        try {
            $this->requireRole(['admin']);

            $candidateModel = new CandidateModel();
            $candidate = $candidateModel->find($id);

            if (!$candidate) {
                return $this->sendError('Kandidat tidak ditemukan', 404);
            }

            // Handle file upload
            $photo = $this->request->getFile('photo');
            $photoPath = $candidate['photo'];

            if ($photo && $photo->isValid() && !$photo->hasMoved()) {
                $newName = $photo->getRandomName();
                $photo->move(ROOTPATH . 'public/uploads/candidates', $newName);
                $photoPath = base_url('uploads/candidates/' . $newName);
            }

            $data = [
                'id' => $id,
                'user_id' => $this->request->getPost('user_id') ?? $candidate['user_id'],
                'election_id' => $this->request->getPost('election_id') ?? $candidate['election_id'],
                'vision' => $this->request->getPost('vision') ?? $candidate['vision'],
                'mission' => $this->request->getPost('mission') ?? $candidate['mission'],
                'programs' => $this->request->getPost('programs') ?? $candidate['programs'],
                'photo' => $photoPath,
            ];

            // Begin transaction
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // Update candidate
                if (!$candidateModel->save($data)) {
                    $db->transRollback();
                    return $this->sendError($candidateModel->errors(), 400);
                }

                $db->transCommit();

                $updatedCandidate = $candidateModel->getCandidateWithDetails($id);

                return $this->sendResponse([
                    'message' => 'Kandidat berhasil diperbarui',
                    'candidate' => $updatedCandidate
                ]);
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function deleteCandidate($id)
    {
        try {
            $this->requireRole(['admin']);

            $candidateModel = new CandidateModel();
            $candidate = $candidateModel->find($id);

            if (!$candidate) {
                return $this->sendError('Kandidat tidak ditemukan', 404);
            }

            // Check if there are votes for this candidate
            $voteModel = new \App\Models\VoteModel();
            $votes = $voteModel->where('candidate_id', $id)->findAll();

            if (!empty($votes)) {
                return $this->sendError('Tidak dapat menghapus kandidat yang sudah memiliki vote', 400);
            }

            // Begin transaction
            $db = \Config\Database::connect();
            $db->transBegin();

            try {
                // Delete candidate
                if (!$candidateModel->delete($id)) {
                    $db->transRollback();
                    return $this->sendError('Gagal menghapus kandidat', 500);
                }

                // Reset user role if needed
                $userModel = new UserModel();
                $user = $userModel->find($candidate['user_id']);

                if ($user && $user['role'] === 'kandidat') {
                    // Check if user is still a candidate in other elections
                    $otherCandidates = $candidateModel->where('user_id', $candidate['user_id'])
                                                     ->where('id !=', $id)
                                                     ->findAll();

                    if (empty($otherCandidates)) {
                        $userModel->update($candidate['user_id'], ['role' => 'user']);
                    }
                }

                $db->transCommit();

                return $this->sendResponse([
                    'message' => 'Kandidat berhasil dihapus'
                ]);
            } catch (\Exception $e) {
                $db->transRollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    // Faculty CRUD Operations
    public function getFaculties()
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $facultyModel = new FacultyModel();
            $faculties = $facultyModel->findAll();

            return $this->sendResponse($faculties);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getFaculty($id)
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $facultyModel = new FacultyModel();
            $faculty = $facultyModel->find($id);

            if (!$faculty) {
                return $this->sendError('Fakultas tidak ditemukan', 404);
            }

            return $this->sendResponse($faculty);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateFaculty($id)
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $facultyModel = new FacultyModel();
            $faculty = $facultyModel->find($id);

            if (!$faculty) {
                return $this->sendError('Fakultas tidak ditemukan', 404);
            }

            $json = $this->request->getJSON(true);
            $data = [
                'name' => $json['name'] ?? $faculty['name'],
                'code' => $json['code'] ?? $faculty['code']
            ];

            if (!$facultyModel->update($id, $data)) {
                return $this->sendError($facultyModel->errors(), 400);
            }

            return $this->sendResponse([
                'message' => 'Fakultas berhasil diperbarui',
                'faculty' => $facultyModel->find($id)
            ]);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function deleteFaculty($id)
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $facultyModel = new FacultyModel();
            $faculty = $facultyModel->find($id);

            if (!$faculty) {
                return $this->sendError('Fakultas tidak ditemukan', 404);
            }

            // Check if there are departments associated with this faculty
            $departmentModel = new DepartmentModel();
            $departments = $departmentModel->where('faculty_id', $id)->findAll();

            if (!empty($departments)) {
                return $this->sendError('Tidak dapat menghapus fakultas yang memiliki jurusan', 400);
            }

            if (!$facultyModel->delete($id)) {
                return $this->sendError('Gagal menghapus fakultas', 500);
            }

            return $this->sendResponse([
                'message' => 'Fakultas berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    // Department CRUD Operations
    public function getDepartments()
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $departmentModel = new DepartmentModel();
            $departments = $departmentModel->select('departments.*, faculties.name as faculty_name')
                                          ->join('faculties', 'faculties.id = departments.faculty_id')
                                          ->findAll();

            return $this->sendResponse($departments);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getDepartment($id)
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $departmentModel = new DepartmentModel();
            $department = $departmentModel->select('departments.*, faculties.name as faculty_name')
                                         ->join('faculties', 'faculties.id = departments.faculty_id')
                                         ->where('departments.id', $id)
                                         ->first();

            if (!$department) {
                return $this->sendError('Jurusan tidak ditemukan', 404);
            }

            return $this->sendResponse($department);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function updateDepartment($id)
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $departmentModel = new DepartmentModel();
            $department = $departmentModel->find($id);

            if (!$department) {
                return $this->sendError('Jurusan tidak ditemukan', 404);
            }

            $json = $this->request->getJSON(true);
            $data = [
                'name' => $json['name'] ?? $department['name'],
                'code' => $json['code'] ?? $department['code'],
                'faculty_id' => $json['faculty_id'] ?? $department['faculty_id']
            ];

            if (!$departmentModel->update($id, $data)) {
                return $this->sendError($departmentModel->errors(), 400);
            }

            return $this->sendResponse([
                'message' => 'Jurusan berhasil diperbarui',
                'department' => $departmentModel->find($id)
            ]);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function deleteDepartment($id)
    {
        try {
            $this->requireRole(['admin', 'operator']);

            $departmentModel = new DepartmentModel();
            $department = $departmentModel->find($id);

            if (!$department) {
                return $this->sendError('Jurusan tidak ditemukan', 404);
            }

            // Check if there are users associated with this department
            $userModel = new UserModel();
            $users = $userModel->where('department_id', $id)->findAll();

            if (!empty($users)) {
                return $this->sendError('Tidak dapat menghapus jurusan yang memiliki mahasiswa', 400);
            }

            if (!$departmentModel->delete($id)) {
                return $this->sendError('Gagal menghapus jurusan', 500);
            }

            return $this->sendResponse([
                'message' => 'Jurusan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Send notification to a candidate
     *
     * @param int $userId User ID
     * @param int $candidateId Candidate ID
     * @param bool $isRunningMate Whether this user is a running mate
     * @return void
     */
    private function sendCandidateNotification($userId, $candidateId, $isRunningMate = false)
    {
        // In a real implementation, this might send an email or push notification
        // For now, we'll just add a session flash message

        $role = $isRunningMate ? 'wakil kandidat' : 'kandidat utama';
        $message = "Anda telah ditambahkan sebagai {$role}. Silakan lengkapi profil kandidat Anda.";

        // Store notification in database or session
        session()->setFlashdata('candidate_notification', $message);
        session()->setFlashdata('candidate_id', $candidateId);
    }
}