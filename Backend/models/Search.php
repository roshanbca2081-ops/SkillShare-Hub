<?php

/**
 * Search Model
 */

require_once __DIR__ . '/../config/database.php';

class Search
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function globalSearch($query)
    {
        $results = [
            'fields' => [],
            'courses' => [],
            'mentors' => []
        ];

        try {
            $fStmt = $this->db->prepare("SELECT id, name, slug, description FROM acad_fields WHERE name LIKE ? OR description LIKE ? LIMIT 5");
            $fStmt->execute(["%$query%", "%$query%"]);
            $results['fields'] = $fStmt->fetchAll();

            $cStmt = $this->db->prepare("SELECT id, name, slug, description FROM acad_courses WHERE name LIKE ? OR description LIKE ? LIMIT 5");
            $cStmt->execute(["%$query%", "%$query%"]);
            $results['courses'] = $cStmt->fetchAll();

            $mStmt = $this->db->prepare("SELECT id, name, title, company FROM acad_mentors WHERE name LIKE ? OR title LIKE ? OR company LIKE ? LIMIT 5");
            $mStmt->execute(["%$query%", "%$query%", "%$query%"]);
            $results['mentors'] = $mStmt->fetchAll();
        } catch (Exception $e) {
            // fallback
        }

        return $results;
    }
}
