<?php

/**
 * Assignment Model
 */

require_once __DIR__ . '/../config/database.php';

class Assignment
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getByCourse($courseId)
    {
        return [];
    }

    public function submit($assignmentId, $userId, $fileUrl, $notes = '')
    {
        return true;
    }
}
