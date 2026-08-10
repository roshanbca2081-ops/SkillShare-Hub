<?php

/**
 * Mentor Model
 */

require_once __DIR__ . '/../config/database.php';

class Mentor
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll($limit = 10, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM acad_mentors ORDER BY rating DESC LIMIT ? OFFSET ?");
            $stmt->execute([(int)$limit, (int)$offset]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM acad_mentors WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getSkills($mentorId)
    {
        try {
            $sql = "SELECT s.* FROM acad_skills s 
                    JOIN acad_mentor_skills ms ON s.id = ms.skill_id 
                    WHERE ms.mentor_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$mentorId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO acad_mentors (name, title, company, avatar, color, rating, reviews, students, experience, price, online, verified) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $res = $stmt->execute([
                $data['name'],
                $data['title'] ?? '',
                $data['company'] ?? '',
                $data['avatar'] ?? 'blue',
                $data['color'] ?? 'blue',
                $data['rating'] ?? 5.0,
                $data['reviews'] ?? 0,
                $data['students'] ?? 0,
                $data['experience'] ?? '1 year',
                $data['price'] ?? 0.00,
                $data['online'] ?? 1,
                $data['verified'] ?? 1
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
            foreach ($data as $k => $v) {
                $fields[] = "$k = ?";
                $params[] = $v;
            }
            $params[] = $id;
            $stmt = $this->db->prepare("UPDATE acad_mentors SET " . implode(', ', $fields) . " WHERE id = ?");
            return $stmt->execute($params);
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM acad_mentors WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            return false;
        }
    }
}
