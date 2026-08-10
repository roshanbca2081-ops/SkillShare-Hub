<?php

/**
 * File Upload Helper
 */

if (!function_exists('uploadFile')) {
    function uploadFile($file, $subFolder = 'general')
    {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'No file uploaded or error occurred.'];
        }

        $targetDir = UPLOAD_PATH . '/' . trim($subFolder, '/') . '/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'zip'];

        if (!in_array($extension, $allowedExtensions)) {
            return ['success' => false, 'message' => 'Invalid file format.'];
        }

        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'File exceeds maximum limit of ' . (MAX_FILE_SIZE / (1024 * 1024)) . 'MB.'];
        }

        $filename = uniqid('up_') . '_' . time() . '.' . $extension;
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $targetFile,
                'url' => UPLOAD_URL . '/' . trim($subFolder, '/') . '/' . $filename
            ];
        }

        return ['success' => false, 'message' => 'Failed to save file.'];
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile($filePath)
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}
