<?php

/**
 * Payment Model
 */

require_once __DIR__ . '/../config/database.php';

class Payment
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function recordTransaction($userId, $amount, $type, $status = 'completed')
    {
        return true;
    }

    public function getHistory($userId)
    {
        return [];
    }
}
