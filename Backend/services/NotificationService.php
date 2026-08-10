<?php

/**
 * Notification Service
 */

require_once __DIR__ . '/../models/Notification.php';

class NotificationService
{
    private $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    public function notifyUser($userId, $title, $message)
    {
        return $this->notificationModel->create($userId, $title, $message);
    }
}
