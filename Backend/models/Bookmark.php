<?php

/**
 * Bookmark Model
 */

require_once __DIR__ . '/../config/database.php';

class Bookmark
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getForUser($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM bookmarks WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([(int)$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function isBookmarked($userId, $itemType, $itemId)
    {
        try {
            $stmt = $this->db->prepare("SELECT id FROM bookmarks WHERE user_id = ? AND item_type = ? AND item_id = ?");
            $stmt->execute([(int)$userId, $itemType, (int)$itemId]);
            return (bool)$stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function add($userId, $itemType, $itemId)
    {
        try {
            if ($this->isBookmarked($userId, $itemType, $itemId)) {
                return false;
            }
            $sql = "INSERT INTO bookmarks (user_id, item_type, item_id, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([(int)$userId, $itemType, (int)$itemId]);
        } catch (Exception $e) {
            return false;
        }
    }

    public function remove($userId, $itemType, $itemId)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM bookmarks WHERE user_id = ? AND item_type = ? AND item_id = ?");
            return $stmt->execute([(int)$userId, $itemType, (int)$itemId]);
        } catch (Exception $e) {
            return false;
        }
    }
}