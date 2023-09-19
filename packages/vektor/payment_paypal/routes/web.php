<?php

Route::group(['prefix' => 'api', 'middleware' => ['web', 'api_csrf', 'checkout_module_enabled', 'paypal_module_enabled']], function () {
    Route::post('paypal/create', [Vektor\Paypal\Http\Controllers\Api\PaypalPaymentController::class, 'create'])->name('api.payment_paypal.create');
    Route::post('paypal/execute', [Vektor\Paypal\Http\Controllers\Api\PaypalPaymentController::class, 'execute'])->name('api.payment_paypal.execute');
});
