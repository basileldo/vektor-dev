<?php

return [
    'enabled' => env('CHECKOUT_PAYMENT_ACCOUNT_ENABLED', false),
    'payment_url' => env('APP_URL', 'http://localhost').'/api/account/pay',
];
