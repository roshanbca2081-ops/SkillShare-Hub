<?php

/**
 * Message Model
 */

require_once __DIR__ . '/../config/database.php';

class Message
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function sendMessage($senderId, $receiverId, $message)
    {
        return true;
    }

    public function getConversation($user1, $user2)
    {
        return [];
    }
}
