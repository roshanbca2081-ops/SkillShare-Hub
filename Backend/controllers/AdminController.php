<?php

/**
 * Admin Controller
 */

class AdminController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function dashboard()
    {
        if (!isLoggedIn() || !isAdmin()) {
            redirect(APP_URL . 'login');
            return;
        }

        $stats = [
            'users' => $this->db->count('users', "status = 'active'"),
            'mentors' => $this->db->count('users', "role = 'mentor' AND status = 'active'"),
            'freshers' => $this->db->count('users', "role = 'fresher' AND status = 'active'"),
            'fields' => $this->db->count('academic_fields', "status = 'active'"),
            'courses' => $this->db->count('courses', "status = 'active'"),
            'bookings' => $this->db->count('bookings'),
            'revenue' => $this->db->fetch("SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE status = 'completed'")['total'],
            'pending' => $this->db->count('bookings', "status = 'pending'")
        ];

        $recentUsers = $this->db->fetchAll(
            "SELECT * FROM users ORDER BY created_at DESC LIMIT 10"
        );

        $data = [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'recentUsers' => $recentUsers
        ];
        $this->render('admin/dashboard', $data);
    }

    public function users()
    {
        if (!isLoggedIn() || !isAdmin()) {
            redirect(APP_URL . 'login');
            return;
        }

        $users = $this->db->fetchAll(
            "SELECT u.*, af.name as field_name FROM users u LEFT JOIN academic_fields af ON u.academic_field_id = af.id ORDER BY u.created_at DESC"
        );

        $data = [
            'title' => 'Users',
            'users' => $users
        ];
        $this->render('admin/users', $data);
    }

    public function userUpdate($id)
    {
        if (!isLoggedIn() || !isAdmin()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $data = [
            'full_name' => sanitize($_POST['full_name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'role' => sanitize($_POST['role'] ?? ''),
            'status' => sanitize($_POST['status'] ?? 'active')
        ];

        $this->db->update('users', $data, 'id = ?', [$id]);
        successResponse([], 'User updated successfully');
    }

    public function userDelete($id)
    {
        if (!isLoggedIn() || !isAdmin()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $this->db->delete('users', 'id = ?', [$id]);
        successResponse([], 'User deleted successfully');
    }

    public function verifyMentor($id)
    {
        if (!isLoggedIn() || !isAdmin()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $this->db->update('mentors', [
            'is_verified' => 1,
            'verified_at' => date('Y-m-d H:i:s'),
            'verified_by' => getUserId()
        ], 'user_id = ?', [$id]);

        successResponse([], 'Mentor verified successfully');
    }

    public function suspendMentor($id)
    {
        if (!isLoggedIn() || !isAdmin()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $this->db->update('users', [
            'status' => 'suspended',
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$id]);

        successResponse([], 'Mentor suspended successfully');
    }

    private function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}
