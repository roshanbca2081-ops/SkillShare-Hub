<?php

/**
 * Certificate Controller
 */

require_once __DIR__ . '/../models/Certificate.php';

class CertificateController
{
    private $certificateModel;

    public function __construct()
    {
        $this->certificateModel = new Certificate();
    }

    public function index()
    {
        sendSuccess([]);
    }
}
