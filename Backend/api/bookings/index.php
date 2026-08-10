<?php

/**
 * API Bookings Endpoint
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../controllers/BookingController.php';

$controller = new BookingController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->create();
} else {
    sendError('Method not allowed', 405);
}
