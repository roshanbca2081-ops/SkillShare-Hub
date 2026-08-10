<?php

/**
 * Payment Controller
 */

require_once __DIR__ . '/../models/Payment.php';

class PaymentController
{
    private $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new Payment();
    }

    public function index()
    {
        sendSuccess([]);
    }
}
