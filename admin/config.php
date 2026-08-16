<?php
/**
 * Admin Panel Configuration
 * Includes root config and adds admin-specific settings
 */

require_once __DIR__ . '/../config.php';

// Admin-specific constants
define('ADMIN_PATH', BASE_PATH . '/admin');
define('ADMIN_URL', BASE_URL . 'admin/');
define('ADMIN_ASSETS_URL', ADMIN_URL . 'assets/');

// Admin session guard
function adminOnly() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isLoggedIn() || getUserRole() !== 'admin') {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

function getAdminUser() {
    if (!isLoggedIn() || getUserRole() !== 'admin') {
        return null;
    }
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT id, full_name, email, profile_picture, role, status, created_at, last_login FROM users WHERE id = ?");
    $stmt->execute([getUserId()]);
    return $stmt->fetch();
}

// Admin statistics helper
function getAdminStats() {
    $pdo = getDB();
    $stats = [];
    
    $queries = [
        'users' => "SELECT COUNT(*) FROM users WHERE status = 'active'",
        'mentors' => "SELECT COUNT(*) FROM users WHERE role = 'mentor' AND status = 'active'",
        'freshers' => "SELECT COUNT(*) FROM users WHERE role = 'fresher' AND status = 'active'",
        'fields' => "SELECT COUNT(*) FROM academic_fields WHERE status = 'active'",
        'courses' => "SELECT COUNT(*) FROM courses WHERE status = 'active'",
        'bookings' => "SELECT COUNT(*) FROM bookings",
        'pending_bookings' => "SELECT COUNT(*) FROM bookings WHERE status = 'pending'",
        'sessions' => "SELECT COUNT(*) FROM sessions",
        'assignments' => "SELECT COUNT(*) FROM assignments",
        'research' => "SELECT COUNT(*) FROM research",
        'payments' => "SELECT COUNT(*) FROM payments",
        'pending_payments' => "SELECT COUNT(*) FROM payments WHERE status = 'pending'",
        'certificates' => "SELECT COUNT(*) FROM certificates",
        'feedback' => "SELECT COUNT(*) FROM reviews",
    ];
    
    foreach ($queries as $key => $sql) {
        $stats[$key] = (int)$pdo->query($sql)->fetchColumn();
    }
    
    $stats['revenue'] = (float)$pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid'")->fetchColumn();
    
    return $stats;
}

// Recent activity helper
function getRecentActivity($limit = 10) {
    $pdo = getDB();
    $activities = [];
    
    $recentUsers = $pdo->query("SELECT id, full_name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT $limit")->fetchAll();
    foreach ($recentUsers as $u) {
        $activities[] = [
            'type' => 'user',
            'title' => 'New ' . $u['role'] . ' registered',
            'subtitle' => $u['full_name'] . ' (' . $u['email'] . ')',
            'time' => $u['created_at'],
            'icon' => $u['role'] === 'mentor' ? 'fa-user-tie' : ($u['role'] === 'admin' ? 'fa-shield-halved' : 'fa-user-graduate'),
            'color' => $u['role'] === 'mentor' ? '#8b5cf6' : ($u['role'] === 'admin' ? '#ef4444' : '#3b82f6')
        ];
    }
    
    $recentBookings = $pdo->query("SELECT b.id, b.status, b.created_at, u.full_name as user_name, m.full_name as mentor_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN users m ON b.mentor_id = m.id ORDER BY b.created_at DESC LIMIT $limit")->fetchAll();
    foreach ($recentBookings as $b) {
        $activities[] = [
            'type' => 'booking',
            'title' => 'Booking ' . $b['status'],
            'subtitle' => $b['user_name'] . ' with ' . $b['mentor_name'],
            'time' => $b['created_at'],
            'icon' => 'fa-calendar-check',
            'color' => '#f59e0b'
        ];
    }
    
    $recentPayments = $pdo->query("SELECT p.id, p.status, p.amount, p.created_at, u.full_name as user_name FROM payments p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT $limit")->fetchAll();
    foreach ($recentPayments as $p) {
        $activities[] = [
            'type' => 'payment',
            'title' => 'Payment ' . $p['status'],
            'subtitle' => $p['user_name'] . ' - $' . number_format($p['amount'], 2),
            'time' => $p['created_at'],
            'icon' => 'fa-credit-card',
            'color' => '#22c55e'
        ];
    }
    
    $recentResearch = $pdo->query("SELECT r.id, r.status, r.title, r.created_at, u.full_name as author_name FROM research r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC LIMIT $limit")->fetchAll();
    foreach ($recentResearch as $r) {
        $activities[] = [
            'type' => 'research',
            'title' => 'Research ' . $r['status'],
            'subtitle' => $r['title'] . ' by ' . $r['author_name'],
            'time' => $r['created_at'],
            'icon' => 'fa-flask',
            'color' => '#06b6d4'
        ];
    }
    
    usort($activities, function($a, $b) {
        return strtotime($b['time']) - strtotime($a['time']);
    });
    
    return array_slice($activities, 0, $limit);
}

// Chart data helpers
function getUsersByRoleChart() {
    $pdo = getDB();
    $roles = ['admin', 'mentor', 'fresher'];
    $data = [];
    foreach ($roles as $role) {
        $data[] = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = '$role' AND status = 'active'")->fetchColumn();
    }
    return ['labels' => $roles, 'data' => $data];
}

function getBookingStatsChart() {
    $pdo = getDB();
    $statuses = ['pending', 'accepted', 'completed', 'cancelled', 'rejected'];
    $data = [];
    foreach ($statuses as $status) {
        $data[] = (int)$pdo->query("SELECT COUNT(*) FROM bookings WHERE status = '$status'")->fetchColumn();
    }
    return ['labels' => array_map('ucfirst', $statuses), 'data' => $data];
}

function getMonthlyRegistrations() {
    $pdo = getDB();
    $months = [];
    $data = [];
    for ($i = 5; $i >= 0; $i--) {
        $months[] = date('M Y', strtotime("-$i months"));
        $data[] = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL $i MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL $i MONTH))")->fetchColumn();
    }
    return ['labels' => $months, 'data' => $data];
}
