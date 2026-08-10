<?php

/**
 * Certificate Service
 */

require_once __DIR__ . '/../models/Certificate.php';

class CertificateService
{
    private $certificateModel;

    public function __construct()
    {
        $this->certificateModel = new Certificate();
    }

    public function issueCertificate($userId, $courseId, $courseTitle)
    {
        return $this->certificateModel->generate($userId, $courseId, $courseTitle);
    }
}
