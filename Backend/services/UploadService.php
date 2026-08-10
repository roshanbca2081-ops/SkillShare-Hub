<?php

/**
 * Upload Service
 */

require_once __DIR__ . '/../helpers/upload.php';

class UploadService
{
    public function uploadProfilePicture($file)
    {
        return uploadFile($file, 'profiles');
    }

    public function uploadCertificate($file)
    {
        return uploadFile($file, 'certificates');
    }

    public function uploadResource($file)
    {
        return uploadFile($file, 'resources');
    }
}
