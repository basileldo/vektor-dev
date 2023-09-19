<?php

Route::group(['prefix' => 'api', 'middleware' => ['web', 'api_csrf', 'checkout_module_enabled', 'account_module_enabled']], function () {
    Route::post('account/pay', [Vektor\Account\Http\Controllers\Api\AccountPaymentController::class, 'handle'])->name('api.payment_account.pay');
});