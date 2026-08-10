<?php

/**
 * Payment Service
 */

require_once __DIR__ . '/../models/Payment.php';

class PaymentService
{
    private $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new Payment();
    }

    public function processPayment($userId, $amount, $paymentMethod)
    {
        return $this->paymentModel->recordTransaction($userId, $amount, $paymentMethod, 'completed');
    }
}
