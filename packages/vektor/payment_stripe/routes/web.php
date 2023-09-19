<?php

Route::group(['prefix' => 'api', 'middleware' => ['web', 'api_csrf', 'checkout_module_enabled', 'stripe_module_enabled']], function () {
    Route::post('stripe/setupintent', [Vektor\Stripe\Http\Controllers\Api\StripePaymentController::class, 'setupIntent'])->name('api.payment_stripe.setup_intent');
    Route::post('stripe/paymentintent', [Vektor\Stripe\Http\Controllers\Api\StripePaymentController::class, 'paymentIntent'])->name('api.payment_stripe.payment_intent');

    Route::post('stripe/customercards', [Vektor\Stripe\Http\Controllers\Api\StripePaymentController::class, 'getCustomerCards'])->name('api.payment_stripe.get_customers_cards');
    Route::delete('stripe/customercards', [Vektor\Stripe\Http\Controllers\Api\StripePaymentController::class, 'deleteCustomerCards'])->name('api.payment_stripe.delete_customers_cards');

    Route::post('stripe/customer/create', [Vektor\Stripe\Http\Controllers\Api\StripePaymentController::class, 'customerCreate'])->name('api.payment_stripe.customer_create');
});