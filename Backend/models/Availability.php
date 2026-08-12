<?php

/**
 * Availability Model
 */

require_once __DIR__ . '/../config/database.php';

class Availability
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getForUser($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM availability WHERE user_id = ? ORDER BY day_of_week ASC, start_time ASC");
            $stmt->execute([(int)$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function upsertBatch($userId, array $slots)
    {
        try {
            $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($slots as $slot) {
                $day = strtolower((string)($slot['day'] ?? $slot['day_of_week'] ?? ''));
                $start = $slot['start'] ?? $slot['start_time'] ?? null;
                $end = $slot['end'] ?? $slot['end_time'] ?? null;
                $available = isset($slot['available']) ? (int)$slot['available'] : 1;
                if ($start === null || $end === null || !in_array($day, $validDays)) {
                    continue;
                }
                $sql = "INSERT INTO availability (user_id, day_of_week, start_time, end_time, is_available) 
                        VALUES (?, ?, ?, ?, ?) 
                        ON DUPLICATE KEY UPDATE start_time = VALUES(start_time), end_time = VALUES(end_time), is_available = VALUES(is_available)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([(int)$userId, $day, $start, $end, $available]);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteForUser($userId)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM availability WHERE user_id = ?");
            return $stmt->execute([(int)$userId]);
        } catch (Exception $e) {
            return false;
        }
    }
}