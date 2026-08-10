<?php

/**
 * Profile Controller
 */

require_once __DIR__ . '/../models/Profile.php';

class ProfileController
{
    private $profileModel;

    public function __construct()
    {
        $this->profileModel = new Profile();
    }

    public function show($userId)
    {
        $profile = $this->profileModel->getByUserId($userId);
        if ($profile) {
            unset($profile['password']);
            sendSuccess($profile);
        } else {
            sendError('User profile not found.', 404);
        }
    }
}
