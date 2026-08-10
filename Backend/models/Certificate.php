<?php

/**
 * Certificate Model
 */

require_once __DIR__ . '/../config/database.php';

class Certificate
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getByUser($userId)
    {
        return [];
    }

    public function generate($userId, $courseId, $title)
    {
        return uniqid('CERT_');
    }
}
