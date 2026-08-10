<?php

/**
 * Message Controller
 */

require_once __DIR__ . '/../models/Message.php';

class MessageController
{
    private $messageModel;

    public function __construct()
    {
        $this->messageModel = new Message();
    }

    public function index()
    {
        sendSuccess([]);
    }
}
