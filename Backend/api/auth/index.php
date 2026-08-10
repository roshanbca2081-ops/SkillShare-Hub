<?php

/**
 * API Auth Endpoint
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$action = $_GET['action'] ?? 'me';
$authController = new AuthController();

if ($action === 'login') {
    $authController->apiLogin();
} elseif ($action === 'register') {
    $authController->apiRegister();
} else {
    $authController->getCurrentUser();
}
