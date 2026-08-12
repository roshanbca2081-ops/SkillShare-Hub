<?php

/**
 * Academic Field Controller
 */

class AcademicFieldController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        $fields = $this->db->fetchAll(
            "SELECT f.*, (SELECT COUNT(*) FROM courses WHERE academic_field_id = f.id) as course_count, (SELECT COUNT(*) FROM users WHERE academic_field = f.id AND role = 'mentor' AND status = 'active') as mentor_count FROM academic_fields f WHERE f.status = 'active' ORDER BY f.sort_order"
        );

        $data = [
            'title' => 'Academic Fields',
            'fields' => $fields
        ];
        $this->render('academic-fields/index', $data);
    }

    public function show($slug)
    {
        $field = $this->db->fetch(
            "SELECT f.*, (SELECT COUNT(*) FROM courses WHERE academic_field_id = f.id) as course_count, (SELECT COUNT(*) FROM users WHERE academic_field = f.id AND role = 'mentor' AND status = 'active') as mentor_count FROM academic_fields f WHERE f.slug = ? AND f.status = 'active'",
            [$slug]
        );

        if (!$field) {
            $this->render('errors/404', ['title' => 'Not Found']);
            return;
        }

        $courses = $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM skills WHERE course_id = c.id) as skill_count, (SELECT COUNT(*) FROM users WHERE course = c.id AND role = 'mentor' AND status = 'active') as mentor_count FROM courses c WHERE c.academic_field_id = ? AND c.status = 'active'",
            [$field['id']]
        );

        $mentors = $this->db->fetchAll(
            "SELECT u.*, m.rating, m.reviews_count FROM users u JOIN mentors m ON u.id = m.user_id WHERE u.role = 'mentor' AND u.status = 'active' AND u.academic_field = ? ORDER BY m.rating DESC LIMIT 6",
            [$field['id']]
        );

        $data = [
            'title' => $field['name'],
            'field' => $field,
            'courses' => $courses,
            'mentors' => $mentors
        ];
        $this->render('academic-fields/show', $data);
    }

    private function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}
