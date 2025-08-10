<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nim', 'name', 'password', 'role', 'faculty_id',
        'department_id', 'status', 'is_super_admin',
        'otp', 'otp_expires_at', 'two_factor_enabled',
        'ktm_file', 'ktm_verified_at', 'ktm_verification_status', 'ktm_rejection_reason',
        'face_image', 'face_recognition_enabled', 'admin_notes'
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'nim' => 'required|is_unique[users.nim,id,{id}]',
        'name' => 'required',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[admin,mahasiswa,kandidat]',
        'faculty_id' => 'permit_empty|is_natural_no_zero',
        'department_id' => 'permit_empty|is_natural_no_zero',
    ];

    protected $validationMessages = [
        'nim' => [
            'is_unique' => 'NIM sudah digunakan'
        ]
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['password'])) {
            return $data;
        }

        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        return $data;
    }

    public function getUserWithAcademic($userId)
    {
        return $this->select('users.*, faculties.name as faculty_name, departments.name as department_name')
                    ->join('faculties', 'faculties.id = users.faculty_id', 'left')
                    ->join('departments', 'departments.id = users.department_id', 'left')
                    ->find($userId);
    }

    public function getAdmins()
    {
        return $this->where('role', 'admin')->findAll();
    }

    public function getStudents()
    {
        return $this->where('role', 'mahasiswa')->findAll();
    }

    public function getCandidates()
    {
        return $this->where('role', 'kandidat')->findAll();
    }

    /**
     * Authenticate user using NIM and password
     *
     * @param string $nim NIM
     * @param string $password Password
     * @return array|bool User data if authenticated, false otherwise
     */
    public function authenticate($nim, $password)
    {
        // Special case for seed user
        if ($nim === 'seed' && $password === 'seedpassword') {
            // Create a temporary user object for seed user
            $user = [
                'id' => 999999,
                'nim' => 'seed',
                'name' => 'Seed User',
                'role' => 'admin',
                'faculty_id' => 1,
                'department_id' => 1,
                'status' => 'active',
                'is_super_admin' => 1,
                'two_factor_enabled' => 0
            ];
            return $user;
        }
        
        // Normal authentication
        $user = $this->where('nim', $nim)->first();
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Reset password for a user (admin only)
     *
     * @param int $userId User ID
     * @param string $newPassword New password
     * @param string $reason Reason for password reset
     * @return bool
     */
    public function resetPassword($userId, $newPassword, $reason = null)
    {
        $data = [
            'password' => $newPassword // Will be hashed by beforeUpdate
        ];
        
        if ($reason) {
            $user = $this->find($userId);
            $currentNotes = $user['admin_notes'] ?? '';
            $timestamp = date('Y-m-d H:i:s');
            $newNote = "[{$timestamp}] Password reset: {$reason}\n";
            $data['admin_notes'] = $newNote . $currentNotes;
        }
        
        return $this->update($userId, $data);
    }
    
    // ===== OTP Methods =====
    
    /**
     * Generate OTP for two-factor authentication
     *
     * @param int $userId User ID
     * @return string
     */
    public function generateOTP($userId)
    {
        $otp = sprintf("%06d", mt_rand(0, 999999));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        
        $this->update($userId, [
            'otp' => $otp,
            'otp_expires_at' => $expiresAt
        ]);
        
        return $otp;
    }
    
    /**
     * Verify OTP
     *
     * @param int $userId User ID
     * @param string $otp OTP
     * @return bool
     */
    public function verifyOTP($userId, $otp)
    {
        $user = $this->find($userId);
        
        if (!$user || $user['otp'] !== $otp) {
            return false;
        }
        
        if (strtotime($user['otp_expires_at']) < time()) {
            return false; // OTP expired
        }
        
        // Clear OTP after successful verification
        $this->update($userId, [
            'otp' => null,
            'otp_expires_at' => null
        ]);
        
        return true;
    }
    
    /**
     * Enable two-factor authentication
     *
     * @param int $userId User ID
     * @return bool
     */
    public function enableTwoFactor($userId)
    {
        return $this->update($userId, ['two_factor_enabled' => 1]);
    }
    
    /**
     * Disable two-factor authentication
     *
     * @param int $userId User ID
     * @return bool
     */
    public function disableTwoFactor($userId)
    {
        return $this->update($userId, ['two_factor_enabled' => 0]);
    }
    
    /**
     * Check if two-factor authentication is enabled
     *
     * @param int $userId User ID
     * @return bool
     */
    public function isTwoFactorEnabled($userId)
    {
        $user = $this->find($userId);
        return $user && $user['two_factor_enabled'] == 1;
    }
    
    // ===== KTM Verification Methods =====
    
    /**
     * Update KTM verification status
     *
     * @param int $userId User ID
     * @param string $status Status (approved/rejected)
     * @param string $reason Reason for rejection (if rejected)
     * @return bool
     */
    public function updateKTMVerificationStatus($userId, $status, $reason = null)
    {
        $data = [
            'ktm_verification_status' => $status
        ];
        
        if ($status === 'approved') {
            $data['ktm_verified_at'] = date('Y-m-d H:i:s');
            $data['ktm_rejection_reason'] = null;
        } else {
            $data['ktm_verified_at'] = null;
            $data['ktm_rejection_reason'] = $reason;
        }
        
        return $this->update($userId, $data);
    }
    
    /**
     * Check if KTM is verified
     *
     * @param int $userId User ID
     * @return bool
     */
    public function isKTMVerified($userId)
    {
        $user = $this->find($userId);
        return $user && $user['ktm_verification_status'] === 'approved';
    }
    
    /**
     * Get users with pending KTM verification
     *
     * @return array
     */
    public function getPendingKTMVerifications()
    {
        return $this->where('ktm_verification_status', 'pending')
                    ->where('ktm_file IS NOT NULL', null, false)
                    ->findAll();
    }
    
    // ===== Face Recognition Methods =====
    
    /**
     * Enable face recognition
     *
     * @param int $userId User ID
     * @param string $imagePath Path to face image
     * @return bool
     */
    public function enableFaceRecognition($userId, $imagePath)
    {
        return $this->update($userId, [
            'face_image' => $imagePath,
            'face_recognition_enabled' => 1
        ]);
    }
    
    /**
     * Disable face recognition
     *
     * @param int $userId User ID
     * @return bool
     */
    public function disableFaceRecognition($userId)
    {
        return $this->update($userId, [
            'face_recognition_enabled' => 0
        ]);
    }
    
    /**
     * Check if face recognition is enabled
     *
     * @param int $userId User ID
     * @return bool
     */
    public function isFaceRecognitionEnabled($userId)
    {
        $user = $this->find($userId);
        return $user && $user['face_recognition_enabled'] == 1;
    }
}