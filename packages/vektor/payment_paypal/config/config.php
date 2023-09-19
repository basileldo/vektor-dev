<?php

return [
    'enabled' => env('CHECKOUT_PAYMENT_PAYPAL_ENABLED', false),
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'client_secret' => env('PAYPAL_CLIENT_SECRET'),
    'create_url' => env('APP_URL', 'http://localhost').'/api/paypal/create',
    'execute_url' => env('APP_URL', 'http://localhost').'/api/paypal/execute',
];
