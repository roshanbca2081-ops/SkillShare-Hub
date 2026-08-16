<?php
// SkillShare Hub - Payment Configuration
// Switch PAYMENT_MODE to 'production' when going live.

return [
    'mode' => 'uat', // 'uat' | 'production'

    'esewa' => [
        'uat' => [
            'merchant_code' => 'EPAYTEST',
            'secret_key' => '8gBm/:&EnhH.1/q',
            'base_url' => 'https://uat.esewa.com.np/epay',
            'status_url' => 'https://uat.esewa.com.np/epay/transaction/status/',
            'success_url' => 'http://localhost/SkillShare-Hub/payment/esewa-success.php',
            'failure_url' => 'http://localhost/SkillShare-Hub/payment/esewa-failure.php',
        ],
        'production' => [
            'merchant_code' => env('ESEWA_MERCHANT_CODE', 'YOUR_MERCHANT_CODE'),
            'secret_key' => env('ESEWA_SECRET_KEY', 'YOUR_SECRET_KEY'),
            'base_url' => 'https://esewa.com.np/epay',
            'status_url' => 'https://esewa.com.np/epay/transaction/status/',
            'success_url' => env('ESEWA_SUCCESS_URL', 'http://localhost/SkillShare-Hub/payment/esewa-success.php'),
            'failure_url' => env('ESEWA_FAILURE_URL', 'http://localhost/SkillShare-Hub/payment/esewa-failure.php'),
        ],
    ],

    'fonepay' => [
        'enabled' => false,
        'uat' => [
            'merchant_code' => env('FONEPAY_MERCHANT_CODE', ''),
            'username' => env('FONEPAY_USERNAME', ''),
            'password' => env('FONEPAY_PASSWORD', ''),
            'base_url' => env('FONEPAY_BASE_URL', ''),
        ],
        'production' => [
            'merchant_code' => env('FONEPAY_MERCHANT_CODE', ''),
            'username' => env('FONEPAY_USERNAME', ''),
            'password' => env('FONEPAY_PASSWORD', ''),
            'base_url' => env('FONEPAY_BASE_URL', ''),
        ],
    ],
];
