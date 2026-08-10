<?php

/**
 * Admin Controller
 */

require_once __DIR__ . '/../models/Admin.php';

class AdminController
{
    private $adminModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
    }

    public function dashboard()
    {
        $stats = $this->adminModel->getSystemStats();
        sendSuccess($stats);
    }
}
