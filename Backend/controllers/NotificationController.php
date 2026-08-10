<?php

/**
 * Notification Controller
 */

require_once __DIR__ . '/../models/Notification.php';

class NotificationController
{
    private $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    public function index()
    {
        sendSuccess([]);
    }
}
