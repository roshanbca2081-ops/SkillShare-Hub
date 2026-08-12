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
        try {
            $sql = "SELECT ce.*, c.name AS course_name, c.slug AS course_slug, c.description, f.name AS field_name
                    FROM course_enrollments ce
                    JOIN acad_courses c ON ce.course_id = c.id
                    LEFT JOIN acad_fields f ON c.field_id = f.id
                    WHERE ce.user_id = ?
                    ORDER BY ce.enrollment_date DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getBookings($userId)
    {
        try {
            $sql = "SELECT b.*, m.name AS mentor_name
                    FROM bookings b
                    LEFT JOIN acad_mentors m ON b.mentor_id = m.id
                    WHERE b.fresher_id = ?
                    ORDER BY b.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}
