<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'ip_address', 'event', 'resource_type', 'resource_id',
        'description', 'old_values', 'new_values', 'user_agent', 'created_at'
    ];
    protected $useTimestamps = false;
    
    /**
     * Log an event
     *
     * @param string $event Event name
     * @param string $resourceType Resource type
     * @param string $resourceId Resource ID
     * @param string $description Description
     * @param array $oldValues Old values
     * @param array $newValues New values
     * @param int $userId User ID
     * @return bool
     */
    public function logEvent($event, $resourceType = null, $resourceId = null, $description = null, $oldValues = null, $newValues = null, $userId = null)
    {
        $request = service('request');
        
        $data = [
            'user_id' => $userId,
            'ip_address' => $request->getIPAddress(),
            'event' => $event,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'description' => $description,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Get audit logs for a specific resource
     *
     * @param string $resourceType Resource type
     * @param string $resourceId Resource ID
     * @return array
     */
    public function getResourceLogs($resourceType, $resourceId)
    {
        return $this->where('resource_type', $resourceType)
                    ->where('resource_id', $resourceId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get audit logs for a specific user
     *
     * @param int $userId User ID
     * @return array
     */
    public function getUserLogs($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get audit logs for a specific event
     *
     * @param string $event Event name
     * @return array
     */
    public function getEventLogs($event)
    {
        return $this->where('event', $event)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get audit logs for a specific IP address
     *
     * @param string $ipAddress IP address
     * @return array
     */
    public function getIpLogs($ipAddress)
    {
        return $this->where('ip_address', $ipAddress)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get audit logs for a specific time period
     *
     * @param string $startDate Start date (Y-m-d)
     * @param string $endDate End date (Y-m-d)
     * @return array
     */
    public function getLogsByDateRange($startDate, $endDate)
    {
        return $this->where('created_at >=', $startDate . ' 00:00:00')
                    ->where('created_at <=', $endDate . ' 23:59:59')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get the latest audit logs
     *
     * @param int $limit Limit
     * @return array
     */
    public function getLatestLogs($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get audit logs with pagination
     *
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array
     */
    public function getLogsWithPagination($page = 1, $perPage = 20)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->paginate($perPage, 'default', $page);
    }
    
    /**
     * Search audit logs
     *
     * @param array $criteria Search criteria
     * @return array
     */
    public function searchLogs($criteria)
    {
        $builder = $this->builder();
        
        if (isset($criteria['user_id'])) {
            $builder->where('user_id', $criteria['user_id']);
        }
        
        if (isset($criteria['event'])) {
            $builder->where('event', $criteria['event']);
        }
        
        if (isset($criteria['resource_type'])) {
            $builder->where('resource_type', $criteria['resource_type']);
        }
        
        if (isset($criteria['resource_id'])) {
            $builder->where('resource_id', $criteria['resource_id']);
        }
        
        if (isset($criteria['ip_address'])) {
            $builder->where('ip_address', $criteria['ip_address']);
        }
        
        if (isset($criteria['start_date'])) {
            $builder->where('created_at >=', $criteria['start_date'] . ' 00:00:00');
        }
        
        if (isset($criteria['end_date'])) {
            $builder->where('created_at <=', $criteria['end_date'] . ' 23:59:59');
        }
        
        if (isset($criteria['search'])) {
            $builder->groupStart()
                    ->like('description', $criteria['search'])
                    ->orLike('event', $criteria['search'])
                    ->orLike('resource_type', $criteria['search'])
                    ->orLike('resource_id', $criteria['search'])
                    ->groupEnd();
        }
        
        return $builder->orderBy('created_at', 'DESC')
                       ->get()
                       ->getResultArray();
    }
}