<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        try {
            // Debug input
            $input = $this->request->getJSON();
            log_message('debug', 'Login input: ' . json_encode($input));
            
            if (!$input || empty((array)$input)) {
                $nim = $this->request->getPost('nim');
                $password = $this->request->getPost('password');
                log_message('debug', 'Post data: nim=' . $nim);
            } else {
                $nim = $input->nim ?? null;
                $password = $input->password ?? null;
            }
            
            // Special case for "seed" user
            if ($nim === 'seed') {
                log_message('debug', 'Seed user login attempt');
            }

            if (!$nim || !$password) {
                log_message('error', 'Missing nim or password');
                return $this->sendError('NIM dan password harus diisi', 400);
            }

            $userModel = new UserModel();
            $user = $userModel->authenticate($nim, $password);

            if (!$user) {
                log_message('error', 'Invalid credentials for nim: ' . $nim);
                return $this->sendError('NIM atau password salah', 401);
            }
            
            // 2FA disabled as requested

            $jwt = new \App\Libraries\JWT();
            $token = $jwt->encode(['user_id' => $user['id']]);

            log_message('debug', 'Login successful for user: ' . $user['id']);

            return $this->sendResponse([
                'token' => $token,
                'user' => $user
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function register()
    {
        try {
            // Debug input
            $input = $this->request->getJSON();
            log_message('debug', 'Register input: ' . json_encode($input));
            
            // Check if data is coming as JSON or form data
            if (!$input || empty((array)$input)) {
                // Handle form data
                $data = [
                    'nim' => $this->request->getPost('nim'),
                    'name' => $this->request->getPost('name'),
                    'password' => $this->request->getPost('password'),
                    'faculty_id' => $this->request->getPost('faculty_id'),
                    'department_id' => $this->request->getPost('department_id'),
                    'role' => 'mahasiswa'
                ];
            } else {
                // Handle JSON data
                $data = [
                    'nim' => $input->nim ?? null,
                    'name' => $input->name ?? null,
                    'password' => $input->password ?? null,
                    'faculty_id' => $input->faculty_id ?? null,
                    'department_id' => $input->department_id ?? null,
                    'role' => 'mahasiswa'
                ];
            }
            
            // Validate required fields
            if (!$data['nim'] || !$data['name'] || !$data['password'] || !$data['faculty_id'] || !$data['department_id']) {
                log_message('error', 'Missing required fields: ' . json_encode(array_filter($data, function($value) { return $value === null; })));
                return $this->sendError('Semua field harus diisi', 400);
            }

            $userModel = new UserModel();
            
            // Log the data being saved
            log_message('debug', 'Attempting to save user data: ' . json_encode(array_diff_key($data, ['password' => ''])));
            
            if (!$userModel->save($data)) {
                $errors = $userModel->errors();
                log_message('error', 'Validation errors during registration: ' . json_encode($errors));
                return $this->sendError($errors, 400);
            }

            $userId = $userModel->getInsertID();
            $user = $userModel->find($userId);
            
            unset($user['password']);

            return $this->sendResponse([
                'message' => 'Registrasi berhasil.',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            log_message('error', 'Exception during registration: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return $this->sendError('Terjadi kesalahan saat registrasi: ' . $e->getMessage(), 500);
        }
    }

    public function profile()
    {
        try {
            $user = $this->requireAuth();
            $userModel = new UserModel();
            $user = $userModel->getUserWithAcademic($user['id']);

            return $this->sendResponse($user);

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
 
    public function resetPassword()
    {
        try {
            $user = $this->requireAuth();
            
            // Hanya admin yang bisa reset password
            if ($user['role'] !== 'admin') {
                return $this->sendError('Anda tidak memiliki izin untuk melakukan reset password', 403);
            }
            
            $userId = $this->request->getPost('user_id');
            $newPassword = $this->request->getPost('new_password');
            $reason = $this->request->getPost('reason');
            
            if (!$userId || !$newPassword) {
                return $this->sendError('ID pengguna dan password baru harus diisi', 400);
            }
            
            $userModel = new UserModel();
            
            // Dapatkan informasi user yang akan direset passwordnya
            $targetUser = $userModel->find($userId);
            if (!$targetUser) {
                return $this->sendError('Pengguna tidak ditemukan', 404);
            }
            
            // Catat aktivitas di audit log
            $auditor = new \App\Libraries\Auditor();
            $auditor->logAdminAction(
                'reset_password',
                'user',
                $userId,
                'Admin reset password untuk pengguna ' . $targetUser['nim'],
                ['admin_id' => $user['id'], 'reason' => $reason]
            );
            
            if (!$userModel->resetPassword($userId, $newPassword, $reason)) {
                return $this->sendError('Gagal mereset password', 500);
            }
            
            return $this->sendResponse([
                'message' => 'Password berhasil direset'
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    public function logout()
    {
        try {
            // Tidak ada yang perlu dilakukan di server untuk logout
            // Token JWT akan dibuang di sisi klien
            
            return $this->sendResponse([
                'message' => 'Logout berhasil'
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}