<?php

use App\Http\Controllers\Api;
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\PageController::class, 'base'])->name('base');
Route::get('favicon', [App\Http\Controllers\AssetController::class, 'favicon'])->name('favicon');
Route::get('favicon_type', [App\Http\Controllers\AssetController::class, 'favicon_type'])->name('favicon_type');
Route::get('logo', [App\Http\Controllers\AssetController::class, 'logo'])->name('logo');
Route::get('logo_size', [App\Http\Controllers\AssetController::class, 'logo_size'])->name('logo_size');

Route::middleware('checkout_only_disabled')->group(function () {
    Route::get('about', [App\Http\Controllers\PageController::class, 'about'])->name('about');
    Route::get('tabs', [App\Http\Controllers\PageController::class, 'tabs'])->name('tabs');
    Route::get('article', [App\Http\Controllers\PageController::class, 'article'])->name('article');
    Route::get('map', [App\Http\Controllers\PageController::class, 'map'])->name('map');
    Route::get('contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');
});

Route::get('terms', [App\Http\Controllers\PageController::class, 'terms'])->name('terms');
Route::get('policy', [App\Http\Controllers\PageController::class, 'policy'])->name('policy');
Route::get('register', [Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::get('login', [Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::get('logout', [Auth\LoginController::class, 'logout'])->name('logout');
Route::get('password/reset', [Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::get('password/email', [Auth\ForgotPasswordController::class, 'showCheckEmailForm'])->name('password.email');
Route::get('password/reset/{token}', [Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/orders', [App\Http\Controllers\DashboardController::class, 'orders'])->name('dashboard.onecrm_orders');
    Route::get('dashboard/orders/{id}', [App\Http\Controllers\DashboardController::class, 'order'])->name('dashboard.onecrm_order');
});

Route::prefix('api')->middleware('api_csrf')->group(function () {
    Route::get('/', function () {})->name('api.base');

    Route::post('register', [Auth\RegisterController::class, 'register'])->name('api.register');
    Route::post('exists', [Auth\LoginController::class, 'exists'])->name('api.exists');
    Route::post('login', [Auth\LoginController::class, 'login'])->name('api.login');
    Route::get('login', [Auth\LoginController::class, 'isLoggedIn']);
    Route::post('password/email', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('api.password.email');
    Route::post('password/reset', [Auth\ResetPasswordController::class, 'reset'])->name('api.password.update');

    Route::post('contact', [Api\ContactController::class, 'handle'])->name('api.contact.submit');
    Route::post('upload', [Api\ContactController::class, 'handleFileUpload']);
});