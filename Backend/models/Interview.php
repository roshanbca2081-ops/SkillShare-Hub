<?php

/**
 * Interview Model
 */

require_once __DIR__ . '/../config/database.php';

class Interview
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function schedule($userId, $mentorId, $dateTime, $topic)
    {
        return true;
    }

    public function getByUser($userId)
    {
        return [];
    }
}
