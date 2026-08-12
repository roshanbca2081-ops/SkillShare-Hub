<?php

/**
 * Progress Model
 */

require_once __DIR__ . '/../config/database.php';

class Progress
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getForUser($userId, $itemType = null)
    {
        try {
            $sql = "SELECT * FROM progress WHERE user_id = ?";
            $params = [(int)$userId];
            if (!empty($itemType)) {
                $sql .= " AND item_type = ?";
                $params[] = $itemType;
            }
            $sql .= " ORDER BY last_accessed DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function updateOrCreate($userId, $itemType, $itemId, $percent)
    {
        try {
            $sql = "INSERT INTO progress (user_id, item_type, item_id, progress_percent, last_accessed) 
                    VALUES (?, ?, ?, ?, NOW()) 
                    ON DUPLICATE KEY UPDATE progress_percent = VALUES(progress_percent), last_accessed = NOW()";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([(int)$userId, $itemType, (int)$itemId, (int)$percent]);
        } catch (Exception $e) {
            return false;
        }
    }
}