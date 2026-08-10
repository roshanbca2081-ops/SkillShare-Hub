<?php

/**
 * Fresher Model
 */

require_once __DIR__ . '/../config/database.php';

class Fresher
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getProfile($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND role = 'fresher'");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getAll($limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'fresher' ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([(int)$limit, (int)$offset]);
        return $stmt->fetchAll();
    }

    public function getEnrolledCourses($userId)
    {
        return [];
    }

    public function getBookings($userId)
    {
        return [];
    }
}
