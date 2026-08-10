<?php

/**
 * Setting Controller
 */

class SettingController
{
    public function index()
    {
        sendSuccess([
            'site_name' => 'SkillShare Hub',
            'version' => '1.0.0',
            'environment' => ENVIRONMENT
        ]);
    }
}
