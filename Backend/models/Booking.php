<?php

/**
 * Booking Model
 */

require_once __DIR__ . '/../config/database.php';

class Booking
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function create($userId, $mentorId, $date, $time, $topic, $notes = '')
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO bookings (user_id, mentor_id, booking_date, booking_time, topic, notes, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
            $res = $stmt->execute([$userId, $mentorId, $date, $time, $topic, $notes]);
            return $res ? $this->db->lastInsertId() : false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getByUser($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT b.*, m.name as mentor_name FROM bookings b LEFT JOIN acad_mentors m ON b.mentor_id = m.id WHERE b.user_id = ? ORDER BY b.created_at DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getByMentor($mentorId)
    {
        try {
            $stmt = $this->db->prepare("SELECT b.*, u.name as user_name FROM bookings b LEFT JOIN users u ON b.user_id = u.id WHERE b.mentor_id = ? ORDER BY b.created_at DESC");
            $stmt->execute([$mentorId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function updateStatus($bookingId, $status)
    {
        try {
            $stmt = $this->db->prepare("UPDATE bookings SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $bookingId]);
        } catch (Exception $e) {
            return false;
        }
    }
}
