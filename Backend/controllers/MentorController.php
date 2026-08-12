<?php

/**
 * Mentor Controller
 */

class MentorController
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

        $sql = "SELECT u.*, m.specialization, m.experience_years, m.mentoring_years, m.current_company, m.current_position, m.qualification, m.is_verified, m.rating, m.reviews_count, m.total_sessions, m.total_students, f.name as field_name FROM users u JOIN mentors m ON u.id = m.user_id LEFT JOIN academic_fields f ON u.academic_field = f.id WHERE u.role = 'mentor' AND u.status = 'active'";
        $params = [];

        if ($fieldId) {
            $sql .= " AND u.academic_field = ?";
            $params[] = $fieldId;
        }
        if ($search) {
            $sql .= " AND (u.full_name LIKE ? OR u.bio LIKE ? OR m.specialization LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY m.rating DESC";

        $mentors = $this->db->fetchAll($sql, $params);
        $fields = $this->db->fetchAll("SELECT * FROM academic_fields WHERE status = 'active' ORDER BY name");

        $data = [
            'title' => 'Mentors',
            'mentors' => $mentors,
            'fields' => $fields,
            'selectedField' => $fieldId,
            'search' => $search
        ];
        $this->render('mentors/index', $data);
    }

    public function show($id)
    {
        $mentor = $this->db->fetch(
            "SELECT u.*, m.specialization, m.experience_years, m.mentoring_years, m.current_company, m.current_position, m.qualification, m.is_verified, m.verified_at, m.rating, m.reviews_count, m.total_sessions, m.total_students, m.portfolio_url, m.linkedin_url, m.github_url, m.website_url, f.name as field_name FROM users u JOIN mentors m ON u.id = m.user_id LEFT JOIN academic_fields f ON u.academic_field = f.id WHERE u.id = ? AND u.role = 'mentor' AND u.status = 'active'",
            [$id]
        );

        if (!$mentor) {
            $this->render('errors/404', ['title' => 'Not Found']);
            return;
        }

        $reviews = $this->db->fetchAll(
            "SELECT r.*, u.full_name as reviewer_name, u.profile_picture as reviewer_avatar FROM reviews r JOIN users u ON r.reviewer_id = u.id WHERE r.reviewee_id = ? AND r.is_public = 1 ORDER BY r.created_at DESC LIMIT 10",
            [$id]
        );

        $availability = $this->db->fetchAll(
            "SELECT * FROM availability WHERE user_id = ? AND is_available = 1 ORDER BY FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')",
            [$id]
        );

        $skills = $this->db->fetchAll(
            "SELECT s.* FROM skills s JOIN skill_learners sl ON s.id = sl.skill_id WHERE sl.user_id = ? AND sl.status = 'completed'",
            [$id]
        );

        $data = [
            'title' => $mentor['full_name'],
            'mentor' => $mentor,
            'reviews' => $reviews,
            'availability' => $availability,
            'skills' => $skills
        ];
        $this->render('mentors/show', $data);
    }

    public function dashboard()
    {
        if (!isLoggedIn() || !isMentor()) {
            redirect(APP_URL . 'login');
            return;
        }

        $userId = getUserId();

        $stats = [
            'students' => $this->db->count('bookings', "mentor_id = ? AND status = 'completed'", [$userId]),
            'sessions' => $this->db->count('sessions', "mentor_id = ? AND status = 'completed'", [$userId]),
            'earnings' => $this->db->fetch("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE user_id = ? AND status = 'completed'", [$userId])['total'],
            'pending' => $this->db->count('bookings', "mentor_id = ? AND status = 'pending'", [$userId]),
            'rating' => $this->db->fetch("SELECT AVG(rating) as avg FROM reviews WHERE reviewee_id = ?", [$userId])['avg'] ?? 0
        ];

        $recentBookings = $this->db->fetchAll(
            "SELECT b.*, u.full_name as fresher_name FROM bookings b JOIN users u ON b.fresher_id = u.id WHERE b.mentor_id = ? ORDER BY b.created_at DESC LIMIT 10",
            [$userId]
        );

        $data = [
            'title' => 'Mentor Dashboard',
            'stats' => $stats,
            'recentBookings' => $recentBookings
        ];
        $this->render('mentor/dashboard', $data);
    }

    public function availability()
    {
        if (!isLoggedIn() || !isMentor()) {
            redirect(APP_URL . 'login');
            return;
        }

        $userId = getUserId();
        $availabilities = $this->db->fetchAll("SELECT * FROM availability WHERE user_id = ?", [$userId]);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->updateAvailability($userId);
        }

        $data = [
            'title' => 'Availability',
            'availabilities' => $availabilities
        ];
        $this->render('mentor/availability', $data);
    }

    private function updateAvailability($userId)
    {
        $this->db->delete('availability', 'user_id = ?', [$userId]);

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            $start = $_POST[$day . '_start'] ?? '';
            $end = $_POST[$day . '_end'] ?? '';
            $available = isset($_POST[$day . '_available']) && $_POST[$day . '_available'] === 'on';

            if ($start && $end) {
                $this->db->insert('availability', [
                    'user_id' => $userId,
                    'day_of_week' => $day,
                    'start_time' => $start,
                    'end_time' => $end,
                    'is_available' => $available ? 1 : 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        setFlash('success', 'Availability updated successfully');
        redirect(APP_URL . 'mentor/availability');
    }

    public function earnings()
    {
        if (!isLoggedIn() || !isMentor()) {
            redirect(APP_URL . 'login');
            return;
        }

        $userId = getUserId();

        $earnings = $this->db->fetchAll(
            "SELECT p.*, b.session_title, u.full_name as fresher_name FROM payments p JOIN bookings b ON p.booking_id = b.id JOIN users u ON b.fresher_id = u.id WHERE p.user_id = ? ORDER BY p.created_at DESC",
            [$userId]
        );

        $totalEarnings = $this->db->fetch(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE user_id = ? AND status = 'completed'",
            [$userId]
        )['total'];

        $data = [
            'title' => 'Earnings',
            'earnings' => $earnings,
            'totalEarnings' => $totalEarnings
        ];
        $this->render('mentor/earnings', $data);
    }

    private function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}
