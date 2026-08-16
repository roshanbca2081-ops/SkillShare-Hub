<?php
/**
 * API Admin Endpoint - returns system stats
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/functions.php';
require_once __DIR__ . '/../../controllers/AdminController.php';

$controller = new AdminController();
$controller->dashboard();
