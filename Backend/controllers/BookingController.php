<?php

/**
 * Booking Controller
 */

require_once __DIR__ . '/../models/Booking.php';

class BookingController
{
    private $bookingModel;

    public function __construct()
    {
        $this->bookingModel = new Booking();
    }

    public function create()
    {
        $data = getRequestData();
        $userId = $data['user_id'] ?? ($_SESSION['user_id'] ?? null);
        $mentorId = $data['mentor_id'] ?? null;
        $date = $data['date'] ?? null;
        $time = $data['time'] ?? null;
        $topic = $data['topic'] ?? 'Mentorship Session';

        if (!$userId || !$mentorId || !$date) {
            sendError('User, Mentor, and Date are required.', 400);
        }

        $id = $this->bookingModel->create($userId, $mentorId, $date, $time, $topic);
        if ($id) {
            sendSuccess(['booking_id' => $id], 'Booking created successfully.', 201);
        } else {
            sendError('Failed to create booking.');
        }
    }
}
