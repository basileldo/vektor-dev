<?php

return [
    'enabled' => env('CHECKOUT_PAYMENT_STRIPE_ENABLED', false),
    'request' => [
        'enabled' => env('CHECKOUT_PAYMENT_STRIPE_REQUEST_ENABLED', false),
    ],
    'public_key' => env('STRIPE_PUBLIC_KEY'),
    'secret_key' => env('STRIPE_SECRET_KEY'),
    'setupintent_url' => env('APP_URL', 'http://localhost').'/api/stripe/setupintent',
    'paymentintent_url' => env('APP_URL', 'http://localhost').'/api/stripe/paymentintent',
    'customercards_url' => env('APP_URL', 'http://localhost').'/api/stripe/customercards',
];