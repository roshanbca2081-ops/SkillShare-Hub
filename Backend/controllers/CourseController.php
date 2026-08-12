<?php

/**
 * Course Controller
 */

class CourseController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        $fieldId = $_GET['field'] ?? null;
        $search = $_GET['search'] ?? '';

        $sql = "SELECT c.*, f.name as field_name, f.slug as field_slug, (SELECT COUNT(*) FROM skills WHERE course_id = c.id) as skill_count, (SELECT COUNT(*) FROM users WHERE course = c.id AND role = 'mentor' AND status = 'active') as mentor_count FROM courses c JOIN academic_fields f ON c.academic_field_id = f.id WHERE c.status = 'active'";
        $params = [];

        if ($fieldId) {
            $sql .= " AND c.academic_field_id = ?";
            $params[] = $fieldId;
        }
        if ($search) {
            $sql .= " AND (c.name LIKE ? OR c.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY c.name";

        $courses = $this->db->fetchAll($sql, $params);
        $fields = $this->db->fetchAll("SELECT * FROM academic_fields WHERE status = 'active' ORDER BY name");

        $data = [
            'title' => 'Courses',
            'courses' => $courses,
            'fields' => $fields,
            'selectedField' => $fieldId,
            'search' => $search
        ];
        $this->render('courses/index', $data);
    }

    public function show($slug)
    {
        $course = $this->db->fetch(
            "SELECT c.*, f.name as field_name, f.slug as field_slug, (SELECT COUNT(*) FROM skills WHERE course_id = c.id) as skill_count, (SELECT COUNT(*) FROM users WHERE course = c.id AND role = 'mentor' AND status = 'active') as mentor_count FROM courses c JOIN academic_fields f ON c.academic_field_id = f.id WHERE c.slug = ? AND c.status = 'active'",
            [$slug]
        );

        if (!$course) {
            $this->render('errors/404', ['title' => 'Not Found']);
            return;
        }

        $skills = $this->db->fetchAll(
            "SELECT * FROM skills WHERE course_id = ? AND status = 'active' ORDER BY name",
            [$course['id']]
        );

        $mentors = $this->db->fetchAll(
            "SELECT u.*, m.rating, m.reviews_count, m.total_sessions FROM users u JOIN mentors m ON u.id = m.user_id WHERE u.role = 'mentor' AND u.status = 'active' AND u.course = ? ORDER BY m.rating DESC LIMIT 6",
            [$course['id']]
        );

        $data = [
            'title' => $course['name'],
            'course' => $course,
            'skills' => $skills,
            'mentors' => $mentors
        ];
        $this->render('courses/show', $data);
    }

    public function enroll()
    {
        if (!isLoggedIn() || !isFresher()) {
            jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $courseId = (int)($_POST['course_id'] ?? 0);
        $userId = getUserId();

        $existing = $this->db->fetch(
            "SELECT id FROM course_enrollments WHERE user_id = ? AND course_id = ?",
            [$userId, $courseId]
        );

        if ($existing) {
            errorResponse('Already enrolled');
            return;
        }

        $this->db->insert('course_enrollments', [
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d'),
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        successResponse([], 'Enrolled successfully');
    }

    private function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}
