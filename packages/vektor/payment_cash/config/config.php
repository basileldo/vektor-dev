<?php

return [
    'enabled' => env('CHECKOUT_PAYMENT_CASH_ENABLED', false),
    'payment_url' => env('APP_URL', 'http://localhost').'/api/cash/pay',
];
