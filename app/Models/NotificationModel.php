<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['user_id', 'message', 'link', 'is_read', 'created_at'];
    protected $useTimestamps = false;
    
    /**
     * Get unread notifications for a user
     *
     * @param int $userId
     * @return array
     */
    public function getUnreadNotifications($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    /**
     * Mark a notification as read
     *
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }
    
    /**
     * Mark all notifications as read for a user
     *
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
                    ->set(['is_read' => 1])
                    ->update();
    }
}