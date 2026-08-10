<?php

/**
 * Session Controller
 */

require_once __DIR__ . '/../models/Session.php';

class SessionController
{
    private $sessionModel;

    public function __construct()
    {
        $this->sessionModel = new SessionModel();
    }

    public function index()
    {
        sendSuccess([]);
    }
}
