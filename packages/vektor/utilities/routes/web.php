<?php

Route::group(['prefix' => 'api', 'middleware' => ['web', 'api_csrf']], function () {
    Route::get('countries', [Vektor\Utilities\Http\Controllers\Api\CountriesController::class, 'index'])->name('api.countries.index');
});
