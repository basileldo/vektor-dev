<?php

Route::group(['prefix' => 'api', 'middleware' => ['web', 'api_csrf', 'checkout_module_enabled', 'cash_module_enabled']], function () {
    Route::post('cash/pay', [Vektor\Cash\Http\Controllers\Api\CashPaymentController::class, 'handle'])->name('api.payment_cash.pay');
});