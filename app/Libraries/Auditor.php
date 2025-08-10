<?php

namespace App\Libraries;

use App\Models\AuditLogModel;

class Auditor
{
    protected $auditLogModel;
    protected $currentUser;
    
    public function __construct()
    {
        $this->auditLogModel = new AuditLogModel();
        
        // Try to get current user
        $auth = service('auth');
        $this->currentUser = $auth->getCurrentUser();
    }
    
    /**
     * Log a login event
     *
     * @param int $userId User ID
     * @param bool $success Whether login was successful
     * @param string $reason Reason for failure if unsuccessful
     * @return bool
     */
    public function logLogin($userId, $success = true, $reason = null)
    {
        $event = $success ? 'login.success' : 'login.failed';
        $description = $success ? 'User logged in successfully' : 'Login failed: ' . $reason;
        
        return $this->auditLogModel->logEvent(
            $event,
            'user',
            $userId,
            $description,
            null,
            null,
            $userId
        );
    }
    
    /**
     * Log a logout event
     *
     * @param int $userId User ID
     * @return bool
     */
    public function logLogout($userId)
    {
        return $this->auditLogModel->logEvent(
            'logout',
            'user',
            $userId,
            'User logged out',
            null,
            null,
            $userId
        );
    }
    
    /**
     * Log a vote event
     *
     * @param int $electionId Election ID
     * @param int $candidateId Candidate ID
     * @param int $voterId Voter ID
     * @param bool $success Whether vote was successful
     * @param string $reason Reason for failure if unsuccessful
     * @return bool
     */
    public function logVote($electionId, $candidateId, $voterId, $success = true, $reason = null)
    {
        $event = $success ? 'vote.cast' : 'vote.failed';
        $description = $success ? 'Vote cast successfully' : 'Vote failed: ' . $reason;
        
        return $this->auditLogModel->logEvent(
            $event,
            'election',
            $electionId,
            $description,
            null,
            [
                'candidate_id' => $candidateId
            ],
            $voterId
        );
    }
    
    /**
     * Log a model change event
     *
     * @param string $modelName Model name
     * @param int $resourceId Resource ID
     * @param string $action Action (create, update, delete)
     * @param array $oldValues Old values
     * @param array $newValues New values
     * @return bool
     */
    public function logModelChange($modelName, $resourceId, $action, $oldValues = null, $newValues = null)
    {
        $event = strtolower($modelName) . '.' . $action;
        $description = ucfirst($action) . ' ' . $modelName . ' #' . $resourceId;
        
        return $this->auditLogModel->logEvent(
            $event,
            strtolower($modelName),
            $resourceId,
            $description,
            $oldValues,
            $newValues,
            $this->currentUser ? $this->currentUser['id'] : null
        );
    }
    
    /**
     * Log an admin action
     *
     * @param string $action Action
     * @param string $resourceType Resource type
     * @param int $resourceId Resource ID
     * @param string $description Description
     * @param array $details Additional details
     * @return bool
     */
    public function logAdminAction($action, $resourceType, $resourceId, $description, $details = null)
    {
        $event = 'admin.' . $action;
        
        return $this->auditLogModel->logEvent(
            $event,
            $resourceType,
            $resourceId,
            $description,
            null,
            $details,
            $this->currentUser ? $this->currentUser['id'] : null
        );
    }
    
    /**
     * Log a security event
     *
     * @param string $action Action
     * @param string $description Description
     * @param array $details Additional details
     * @return bool
     */
    public function logSecurityEvent($action, $description, $details = null)
    {
        $event = 'security.' . $action;
        
        return $this->auditLogModel->logEvent(
            $event,
            'security',
            null,
            $description,
            null,
            $details,
            $this->currentUser ? $this->currentUser['id'] : null
        );
    }
    
    /**
     * Log a verification event
     *
     * @param string $type Verification type (email, otp, ktm, face)
     * @param int $userId User ID
     * @param bool $success Whether verification was successful
     * @param string $reason Reason for failure if unsuccessful
     * @return bool
     */
    public function logVerification($type, $userId, $success = true, $reason = null)
    {
        $event = 'verification.' . $type . ($success ? '.success' : '.failed');
        $description = ucfirst($type) . ' verification ' . ($success ? 'successful' : 'failed: ' . $reason);
        
        return $this->auditLogModel->logEvent(
            $event,
            'user',
            $userId,
            $description,
            null,
            null,
            $userId
        );
    }
    
    /**
     * Log a blockchain event
     *
     * @param string $action Action
     * @param string $transactionHash Transaction hash
     * @param string $description Description
     * @param array $details Additional details
     * @return bool
     */
    public function logBlockchainEvent($action, $transactionHash, $description, $details = null)
    {
        $event = 'blockchain.' . $action;
        
        return $this->auditLogModel->logEvent(
            $event,
            'blockchain',
            $transactionHash,
            $description,
            null,
            $details,
            $this->currentUser ? $this->currentUser['id'] : null
        );
    }
}