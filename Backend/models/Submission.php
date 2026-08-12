<?php

/**
 * Submission Model
 */

require_once __DIR__ . '/../config/database.php';

class Submission
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll($assignmentId = null)
    {
        try {
            $sql = "SELECT * FROM submissions";
            $params = [];
            if (!empty($assignmentId)) {
                $sql .= " WHERE assignment_id = ?";
                $params[] = (int)$assignmentId;
            }
            $sql .= " ORDER BY submitted_at DESC";
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
            $stmt = $this->db->prepare("SELECT * FROM submissions WHERE id = ?");
            $stmt->execute([(int)$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO submissions (assignment_id, fresher_id, content, file_path, status, submitted_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $res = $stmt->execute([
                (int)$data['assignment_id'],
                (int)$data['fresher_id'],
                $data['content'] ?? null,
                $data['file_path'] ?? null,
                $data['status'] ?? 'submitted'
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
            $fields[] = 'graded_at = NOW()';
            $params[] = (int)$id;
            $sql = "UPDATE submissions SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM submissions WHERE id = ?");
            return $stmt->execute([(int)$id]);
        } catch (Exception $e) {
            return false;
        }
    }
}