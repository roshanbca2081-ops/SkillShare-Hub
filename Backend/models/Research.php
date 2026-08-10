<?php

/**
 * Research Model
 */

require_once __DIR__ . '/../config/database.php';

class Research
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll($limit = 10)
    {
        return [];
    }

    public function findById($id)
    {
        return false;
    }
}
