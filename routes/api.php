<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MacAddressController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// Single MAC address lookup (GET request)
Route::get('/mac-lookup', [MacAddressController::class, 'lookup']);

// Multiple MAC address lookup (POST request)
Route::post('/mac-lookup', [MacAddressController::class, 'lookup']);

