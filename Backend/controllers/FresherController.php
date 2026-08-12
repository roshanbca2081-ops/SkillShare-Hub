<?php

/**
 * Fresher Controller
 */

class FresherController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        $freshers = $this->db->fetchAll(
            "SELECT u.*, af.name as field_name, c.name as course_name FROM users u LEFT JOIN academic_fields af ON u.academic_field = af.id LEFT JOIN courses c ON u.course = c.id WHERE u.role = 'fresher' AND u.status = 'active' ORDER BY u.created_at DESC"
        );
        successResponse($freshers);
    }

    public function show($id)
    {
        $fresher = $this->db->fetch(
            "SELECT u.*, af.name as field_name, c.name as course_name FROM users u LEFT JOIN academic_fields af ON u.academic_field = af.id LEFT JOIN courses c ON u.course = c.id WHERE u.id = ? AND u.role = 'fresher'",
            [$id]
        );

        if (!$fresher) {
            sendError('Fresher not found', 404);
            return;
        }

        $fresher['enrolled_courses'] = $this->db->fetchAll(
            "SELECT ce.*, c.name as course_name FROM course_enrollments ce JOIN courses c ON ce.course_id = c.id WHERE ce.user_id = ?",
            [$id]
        );
        $fresher['bookings'] = $this->db->fetchAll(
            "SELECT b.*, u.full_name as mentor_name FROM bookings b JOIN users u ON b.mentor_id = u.id WHERE b.fresher_id = ?",
            [$id]
        );

        sendSuccess($fresher);
    }

    public function dashboard()
    {
        if (!isLoggedIn() || !isFresher()) {
            redirect(APP_URL . 'login');
            return;
        }

        $userId = getUserId();

        $stats = [
            'courses' => $this->db->count('course_enrollments', "user_id = ? AND status = 'active'", [$userId]),
            'completed' => $this->db->count('course_enrollments', "user_id = ? AND status = 'completed'", [$userId]),
            'sessions' => $this->db->count('sessions', "fresher_id = ? AND status = 'completed'", [$userId]),
            'certificates' => $this->db->count('certificates', "user_id = ? AND status = 'issued'", [$userId]),
            'assignments' => $this->db->count('submissions', "fresher_id = ? AND status = 'submitted'", [$userId])
        ];

        $progress = $this->db->fetchAll(
            "SELECT p.*, c.name as course_name, c.slug as course_slug FROM progress p JOIN courses c ON p.item_id = c.id WHERE p.user_id = ? AND p.item_type = 'course' ORDER BY p.last_accessed DESC LIMIT 5",
            [$userId]
        );

        $recentSessions = $this->db->fetchAll(
            "SELECT s.*, u.full_name as mentor_name, u.profile_picture as mentor_avatar FROM sessions s JOIN users u ON s.mentor_id = u.id WHERE s.fresher_id = ? ORDER BY s.created_at DESC LIMIT 5",
            [$userId]
        );

        $data = [
            'title' => 'Fresher Dashboard',
            'stats' => $stats,
            'progress' => $progress,
            'recentSessions' => $recentSessions
        ];
        $this->render('fresher/dashboard', $data);
    }

    public function profile()
    {
        if (!isLoggedIn() || !isFresher()) {
            redirect(APP_URL . 'login');
            return;
        }

        $userId = getUserId();

        $user = $this->db->fetch(
            "SELECT u.*, f.education_level, f.institution, f.graduation_year, f.interests, f.goals, af.name as field_name, c.name as course_name FROM users u LEFT JOIN freshers f ON u.id = f.user_id LEFT JOIN academic_fields af ON u.academic_field = af.id LEFT JOIN courses c ON u.course = c.id WHERE u.id = ?",
            [$userId]
        );

        $data = [
            'title' => 'My Profile',
            'user' => $user
        ];
        $this->render('fresher/profile', $data);
    }

    private function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}
