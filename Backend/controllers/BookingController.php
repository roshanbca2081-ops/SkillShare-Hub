<?php

/**
 * Booking Controller
 */

class BookingController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function store()
    {
        if (!isLoggedIn() || !isFresher()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $data = [
            'mentor_id' => (int)($_POST['mentor_id'] ?? 0),
            'skill_id' => (int)($_POST['skill_id'] ?? 0),
            'session_title' => sanitize($_POST['session_title'] ?? ''),
            'session_description' => sanitize($_POST['session_description'] ?? ''),
            'session_date' => sanitize($_POST['session_date'] ?? ''),
            'session_time' => sanitize($_POST['session_time'] ?? ''),
            'duration' => (int)($_POST['duration'] ?? 60)
        ];

        // Validate
        if (!$data['mentor_id'] || empty($data['session_date']) || empty($data['session_time'])) {
            errorResponse('Missing required fields');
            return;
        }

        // Get mentor hourly rate
        $mentor = $this->db->fetch("SELECT hourly_rate FROM users WHERE id = ?", [$data['mentor_id']]);
        if (!$mentor) {
            errorResponse('Mentor not found');
            return;
        }

        $hourlyRate = $mentor['hourly_rate'] ?: 0;
        $totalAmount = ($hourlyRate * $data['duration']) / 60;
        $bookingNumber = generateBookingNumber();

        $bookingId = $this->db->insert('bookings', [
            'booking_number' => $bookingNumber,
            'mentor_id' => $data['mentor_id'],
            'fresher_id' => getUserId(),
            'skill_id' => $data['skill_id'] ?: null,
            'session_title' => $data['session_title'] ?: 'Mentorship Session',
            'session_description' => $data['session_description'],
            'session_date' => $data['session_date'],
            'session_time' => $data['session_time'],
            'duration' => $data['duration'],
            'hourly_rate' => $hourlyRate,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($bookingId) {
            // Create notification for mentor
            $this->db->insert('notifications', [
                'user_id' => $data['mentor_id'],
                'title' => 'New Booking Request',
                'message' => 'A new booking request has been received.',
                'type' => 'booking',
                'link' => APP_URL . 'mentor/bookings',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            successResponse(['booking_id' => $bookingId, 'booking_number' => $bookingNumber], 'Booking created successfully');
        } else {
            errorResponse('Failed to create booking');
        }
    }

    public function accept($id)
    {
        if (!isLoggedIn() || !isMentor()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $this->db->update('bookings', [
            'status' => 'confirmed',
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND mentor_id = ?', [$id, getUserId()]);

        successResponse([], 'Booking accepted');
    }

    public function reject($id)
    {
        if (!isLoggedIn() || !isMentor()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $reason = sanitize($_POST['reason'] ?? '');

        $this->db->update('bookings', [
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => date('Y-m-d H:i:s'),
            'cancelled_by' => getUserId(),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND mentor_id = ?', [$id, getUserId()]);

        successResponse([], 'Booking rejected');
    }

    public function cancel($id)
    {
        if (!isLoggedIn()) {
            errorResponse('Unauthorized', 401);
            return;
        }

        $reason = sanitize($_POST['reason'] ?? '');

        $this->db->update('bookings', [
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at' => date('Y-m-d H:i:s'),
            'cancelled_by' => getUserId(),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ? AND fresher_id = ?', [$id, getUserId()]);

        successResponse([], 'Booking cancelled');
    }

    public function mentorBookings()
    {
        if (!isLoggedIn() || !isMentor()) {
            redirect(APP_URL . 'login');
            return;
        }

        $userId = getUserId();
        $bookings = $this->db->fetchAll(
            "SELECT b.*, u.full_name as fresher_name, u.email as fresher_email, u.profile_picture as fresher_avatar FROM bookings b JOIN users u ON b.fresher_id = u.id WHERE b.mentor_id = ? ORDER BY b.created_at DESC",
            [$userId]
        );

        $data = [
            'title' => 'Bookings',
            'bookings' => $bookings
        ];
        $this->render('mentor/bookings', $data);
    }

    public function fresherBookings()
    {
        if (!isLoggedIn() || !isFresher()) {
            redirect(APP_URL . 'login');
            return;
        }

        $userId = getUserId();
        $bookings = $this->db->fetchAll(
            "SELECT b.*, u.full_name as mentor_name, u.email as mentor_email, u.profile_picture as mentor_avatar FROM bookings b JOIN users u ON b.mentor_id = u.id WHERE b.fresher_id = ? ORDER BY b.created_at DESC",
            [$userId]
        );

        $data = [
            'title' => 'My Bookings',
            'bookings' => $bookings
        ];
        $this->render('fresher/bookings', $data);
    }

    // Alias for existing API endpoint compatibility
    public function create()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true) ?: $_POST;

        $_POST = $data;
        $this->store();
    }

    private function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }
}
