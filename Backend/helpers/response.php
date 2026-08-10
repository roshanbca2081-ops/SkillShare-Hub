<?php

/**
 * Response Helper Functions
 */

if (!function_exists('sendResponse')) {
    function sendResponse($success = true, $message = '', $data = null, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit();
    }
}

if (!function_exists('sendError')) {
    function sendError($message = 'An error occurred', $code = 400, $data = null)
    {
        sendResponse(false, $message, $data, $code);
    }
}

if (!function_exists('sendSuccess')) {
    function sendSuccess($data = null, $message = 'Success', $code = 200)
    {
        sendResponse(true, $message, $data, $code);
    }
}
