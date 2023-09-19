<?php

Route::group(['prefix' => 'api', 'middleware' => ['web', 'api_csrf']], function () {
    Route::group(['prefix' => 'onecrm'], function () {
        Route::post('orders', [Vektor\OneCRM\Http\Controllers\Api\SalesOrderController::class, 'index'])->name('api.onecrm.orders.index');
        Route::post('orders/{id}', [Vektor\OneCRM\Http\Controllers\Api\SalesOrderController::class, 'show'])->name('api.onecrm.orders.show');

        Route::put('account', [Vektor\OneCRM\Http\Controllers\Api\DashboardAccountController::class, 'update'])->name('api.onecrm.personal_details.update');
        Route::get('account', [Vektor\OneCRM\Http\Controllers\Api\DashboardAccountController::class, 'show'])->name('api.onecrm.personal_details.show');
    });
});