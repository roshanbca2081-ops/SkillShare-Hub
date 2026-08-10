<?php

/**
 * Course Model
 */

require_once __DIR__ . '/../config/database.php';

class Course
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll($limit = 12, $offset = 0)
    {
        try {
            $sql = "SELECT c.*, f.name as field_name FROM acad_courses c 
                    LEFT JOIN acad_fields f ON c.field_id = f.id 
                    ORDER BY c.id DESC LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int)$limit, (int)$offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM acad_courses WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSubjects($courseId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM acad_subjects WHERE course_id = ? AND status = 'active'");
            $stmt->execute([$courseId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}
