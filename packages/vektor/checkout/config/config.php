<?php

return [
    'enabled' => env('CHECKOUT_ENABLED', false),
    'as_base' => env('CHECKOUT_AS_BASE', false),
    'only' => env('CHECKOUT_ONLY', false),
    'pagination' => [
        'enabled' => env('CHECKOUT_PAGINATION_ENABLED', false),
        'per_pages' => env('CHECKOUT_PAGINATION_PER_PAGES', '3,6'),
    ],
    'hide_pricing' => env('CHECKOUT_HIDE_PRICING', false),
    'billing_required' => env('CHECKOUT_BILLING_REQUIRED', true),
    'shipping_required' => env('CHECKOUT_SHIPPING_REQUIRED', true),
    'customer_unique' => env('CHECKOUT_CUSTOMER_UNIQUE', false),
    'email_domain_check' => [
        'enabled' => env('CHECKOUT_EMAIL_DOMAIN_CHECK_ENABLED', false),
        'list' => env('CHECKOUT_EMAIL_DOMAIN_CHECK_LIST') ? str_replace(',', '|', str_replace(' ', '', env('CHECKOUT_EMAIL_DOMAIN_CHECK_LIST'))) : null,
    ],
    'agree_terms' => env('CHECKOUT_AGREE_TERMS', false),
];
