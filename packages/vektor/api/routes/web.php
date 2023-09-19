<?php

Route::group(['prefix' => 'api', 'middleware' => ['web']], function () {
    Route::post('token', [Vektor\Api\Api::class, 'generateToken'])->name('api.token');
});
