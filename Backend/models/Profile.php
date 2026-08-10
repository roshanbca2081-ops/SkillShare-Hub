<?php

/**
 * Profile Model
 */

require_once __DIR__ . '/../config/database.php';

class Profile
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function updateProfile($userId, $data)
    {
        $fields = [];
        $params = [];
        foreach ($data as $k => $v) {
            $fields[] = "$k = ?";
            $params[] = $v;
        }
        $params[] = $userId;
        $stmt = $this->db->prepare("UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }
}
