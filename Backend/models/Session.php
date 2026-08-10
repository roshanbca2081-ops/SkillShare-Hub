<?php

/**
 * Session Model
 */

require_once __DIR__ . '/../config/database.php';

class SessionModel
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getActiveSessions($userId)
    {
        return [];
    }

    public function getSessionHistory($userId)
    {
        return [];
    }

    public function findById($id)
    {
        return false;
    }
}
