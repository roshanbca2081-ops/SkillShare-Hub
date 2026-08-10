<?php

/**
 * AcademicField Model
 */

require_once __DIR__ . '/../config/database.php';

class AcademicField
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll($status = 'active')
    {
        try {
            $sql = "SELECT * FROM acad_fields";
            $params = [];
            if ($status) {
                $sql .= " WHERE status = ?";
                $params[] = $status;
            }
            $sql .= " ORDER BY name ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function findBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM acad_fields WHERE slug = ?");
            $stmt->execute([$slug]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM acad_fields WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getCourses($fieldId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM acad_courses WHERE field_id = ? AND status = 'active'");
            $stmt->execute([$fieldId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}
