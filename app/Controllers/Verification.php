<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\EmailSender;
use App\Libraries\FaceRecognition;

class Verification extends BaseController
{
    /**
     * Upload dan verifikasi KTM
     */
    public function uploadKTM()
    {
        try {
            $user = $this->requireAuth();
            
            $validationRules = [
                'ktm_file' => [
                    'label' => 'KTM File',
                    'rules' => 'uploaded[ktm_file]|max_size[ktm_file,2048]|mime_in[ktm_file,image/jpg,image/jpeg,image/png,application/pdf]',
                    'errors' => [
                        'uploaded' => 'File KTM harus diunggah',
                        'max_size' => 'Ukuran file maksimal 2MB',
                        'mime_in' => 'Format file harus JPG, PNG, atau PDF'
                    ]
                ]
            ];
            
            if (!$this->validate($validationRules)) {
                return $this->sendError($this->validator->getErrors(), 400);
            }
            
            $file = $this->request->getFile('ktm_file');
            if (!$file->isValid()) {
                return $this->sendError('File tidak valid', 400);
            }
            
            // Create directory if it doesn't exist
            $uploadPath = WRITEPATH . 'uploads/ktm';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            
            $userModel = new UserModel();
            $userModel->update($user['id'], [
                'ktm_file' => $newName,
                'ktm_verification_status' => 'pending',
                'ktm_verified_at' => null,
                'ktm_rejection_reason' => null
            ]);
            
            return $this->sendResponse([
                'message' => 'File KTM berhasil diunggah dan sedang diverifikasi',
                'file_name' => $newName
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    /**
     * Verifikasi KTM oleh admin
     */
    public function verifyKTM($userId)
    {
        try {
            $admin = $this->requireRole(['admin']);
            
            $status = $this->request->getPost('status'); // 'approved' or 'rejected'
            $reason = $this->request->getPost('reason'); // Required if rejected
            
            if (!in_array($status, ['approved', 'rejected'])) {
                return $this->sendError('Status harus approved atau rejected', 400);
            }
            
            if ($status === 'rejected' && empty($reason)) {
                return $this->sendError('Alasan penolakan harus diisi', 400);
            }
            
            $userModel = new UserModel();
            $user = $userModel->find($userId);
            
            if (!$user) {
                return $this->sendError('User tidak ditemukan', 404);
            }
            
            $userModel->updateKTMVerificationStatus($userId, $status, $reason);
            
            // Kirim email notifikasi ke user
            $emailSender = new EmailSender();
            $emailSender->sendKTMVerificationEmail($user['email'], $user['name'], $status, $reason);
            
            return $this->sendResponse([
                'message' => 'Verifikasi KTM berhasil diperbarui',
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    /**
     * Mendapatkan status verifikasi KTM
     */
    public function getKTMStatus()
    {
        try {
            $user = $this->requireAuth();
            
            $userModel = new UserModel();
            $userData = $userModel->find($user['id']);
            
            return $this->sendResponse([
                'ktm_file' => $userData['ktm_file'],
                'ktm_verification_status' => $userData['ktm_verification_status'],
                'ktm_verified_at' => $userData['ktm_verified_at'],
                'ktm_rejection_reason' => $userData['ktm_rejection_reason']
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    /**
     * Mendapatkan daftar KTM yang belum diverifikasi (untuk admin)
     */
    public function getPendingKTMVerifications()
    {
        try {
            $admin = $this->requireRole(['admin']);
            
            $userModel = new UserModel();
            $pendingUsers = $userModel->getPendingKTMVerifications();
            
            return $this->sendResponse($pendingUsers);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    /**
     * Mendaftarkan wajah untuk face recognition
     */
    public function registerFace()
    {
        try {
            $user = $this->requireAuth();
            
            $imageData = $this->request->getPost('image_data');
            
            if (!$imageData) {
                return $this->sendError('Data gambar harus diisi', 400);
            }
            
            $faceRecognition = new FaceRecognition();
            $result = $faceRecognition->registerFace($user['id'], $imageData);
            
            if (!$result['success']) {
                return $this->sendError($result['error'] ?? 'Gagal mendaftarkan wajah', 500);
            }
            
            $userModel = new UserModel();
            $userModel->enableFaceRecognition($user['id'], $result['image_path']);
            
            return $this->sendResponse([
                'message' => 'Wajah berhasil didaftarkan',
                'face_id' => $result['face_id'] ?? null
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    /**
     * Verifikasi wajah untuk login
     */
    public function verifyFace()
    {
        try {
            $email = $this->request->getPost('email');
            $imageData = $this->request->getPost('image_data');
            
            if (!$email || !$imageData) {
                return $this->sendError('Email dan data gambar harus diisi', 400);
            }
            
            $userModel = new UserModel();
            $user = $userModel->where('email', $email)->first();
            
            if (!$user) {
                return $this->sendError('User tidak ditemukan', 404);
            }
            
            if (!$userModel->isFaceRecognitionEnabled($user['id'])) {
                return $this->sendError('Face recognition tidak diaktifkan untuk user ini', 400);
            }
            
            $faceRecognition = new FaceRecognition();
            $result = $faceRecognition->verifyFace($user['id'], $imageData);
            
            if (!$result['success']) {
                return $this->sendError($result['error'] ?? 'Gagal memverifikasi wajah', 500);
            }
            
            if (!$result['is_match']) {
                return $this->sendError('Wajah tidak cocok', 401);
            }
            
            // Wajah cocok, buat token autentikasi
            $jwt = new \App\Libraries\JWT();
            $token = $jwt->encode(['user_id' => $user['id']]);
            
            unset($user['password']);
            
            return $this->sendResponse([
                'token' => $token,
                'user' => $user,
                'confidence' => $result['confidence']
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
    
    /**
     * Menonaktifkan face recognition
     */
    public function disableFaceRecognition()
    {
        try {
            $user = $this->requireAuth();
            
            $userModel = new UserModel();
            $userModel->disableFaceRecognition($user['id']);
            
            return $this->sendResponse([
                'message' => 'Face recognition berhasil dinonaktifkan'
            ]);
            
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}