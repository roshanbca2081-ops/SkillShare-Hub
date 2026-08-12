<?php

/**
 * Skill Model
 */

require_once __DIR__ . '/../config/database.php';

class Skill
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll($courseId = null)
    {
        try {
            $sql = "SELECT * FROM skills";
            $params = [];
            if (!empty($courseId)) {
                $sql .= " WHERE course_id = ?";
                $params[] = (int)$courseId;
            }
            $sql .= " ORDER BY name ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM skills WHERE id = ?");
            $stmt->execute([(int)$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO skills (course_id, name, description, status, created_at) 
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $res = $stmt->execute([
                (int)$data['course_id'],
                $data['name'],
                $data['description'] ?? null,
                $data['status'] ?? 'active'
            ]);
            return $res ? $this->db->lastInsertId() : false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            $fields = [];
            $params = [];
            foreach ($data as $key => $val) {
                $fields[] = "$key = ?";
                $params[] = $val;
            }
            $params[] = (int)$id;
            $sql = "UPDATE skills SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM skills WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (Exception $e) {
            return false;
        }
    }
}