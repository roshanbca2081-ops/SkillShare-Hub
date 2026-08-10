<?php

/**
 * Notification Model
 */

require_once __DIR__ . '/../config/database.php';

class Notification
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function create($userId, $title, $message, $type = 'info')
    {
        return true;
    }

    public function getByUser($userId)
    {
        return [];
    }

    public function markAsRead($notificationId)
    {
        return true;
    }
}
