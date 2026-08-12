<?php

/**
 * Review Model
 */

require_once __DIR__ . '/../config/database.php';

class Review
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAllForUser($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM reviews WHERE reviewee_id = ? ORDER BY created_at DESC");
            $stmt->execute([(int)$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = ?");
            $stmt->execute([(int)$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO reviews (reviewer_id, reviewee_id, booking_id, rating, comment, is_public, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $res = $stmt->execute([
                (int)$data['reviewer_id'],
                (int)$data['reviewee_id'],
                $data['booking_id'] ?? null,
                (int)($data['rating'] ?? 5),
                $data['comment'] ?? null,
                $data['is_public'] ?? 1
            ]);
            if ($res) {
                $id = $this->db->lastInsertId();
                $this->updateMentorRating((int)$data['reviewee_id']);
                return $id;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT reviewee_id FROM reviews WHERE id = ?");
            $stmt->execute([(int)$id]);
            $review = $stmt->fetch();
            $res = $this->db->prepare("DELETE FROM reviews WHERE id = ?");
            $res->execute([(int)$id]);
            if ($res && $review) {
                $this->updateMentorRating((int)$review['reviewee_id']);
            }
            return $res;
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateMentorRating($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count, AVG(rating) as avg_rating FROM reviews WHERE reviewee_id = ?");
            $stmt->execute([(int)$userId]);
            $row = $stmt->fetch();
            if (!$row) {
                return false;
            }
            $rating = $row['avg_rating'] !== null ? round((float)$row['avg_rating'], 2) : 0.00;
            $update = $this->db->prepare("UPDATE mentors SET reviews_count = ?, rating = ? WHERE user_id = ?");
            return $update->execute([(int)$row['count'], $rating, (int)$userId]);
        } catch (Exception $e) {
            return false;
        }
    }
}