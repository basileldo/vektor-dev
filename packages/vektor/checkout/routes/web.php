<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('product_images/{filename}', [Vektor\Checkout\Http\Controllers\AssetController::class, 'product_images'])->name('checkout.product_images.product_images');
});

Route::group(['middleware' => ['web', 'checkout_module_enabled']], function () {
    Route::get('shop', [Vektor\Checkout\Http\Controllers\ProductController::class, 'index'])->name('checkout.product.index');
    Route::get('product/{slug}', [Vektor\Checkout\Http\Controllers\ProductController::class, 'show'])->name('checkout.product.show');
    Route::get('cart', [Vektor\Checkout\Http\Controllers\CartController::class, 'index'])->name('checkout.cart.index');
    Route::get('checkout', [Vektor\Checkout\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.checkout.index');
    Route::post('success', [Vektor\Checkout\Http\Controllers\SuccessController::class, 'index'])->name('checkout.success.index');
});

Route::group(['prefix' => 'api', 'middleware' => ['web', 'api_csrf', 'checkout_module_enabled']], function () {
    Route::get('shipping_methods', [Vektor\Checkout\Http\Controllers\Api\ShippingMethodController::class, 'index'])->name('api.shipping_methods.index');
    Route::post('products', [Vektor\Checkout\Http\Controllers\Api\CheckoutController::class, 'index'])->name('api.products.index');
    Route::get('products', [Vektor\Checkout\Http\Controllers\Api\CheckoutController::class, 'index'])->name('api.products.new_index');

    Route::get('cart', [Vektor\Checkout\Http\Controllers\Api\CartController::class, 'index'])->name('api.cart.index');
    Route::post('cart', [Vektor\Checkout\Http\Controllers\Api\CartController::class, 'store'])->name('api.cart.store');
    Route::put('cart/{rowId}', [Vektor\Checkout\Http\Controllers\Api\CartController::class, 'update'])->name('api.cart.update');
    Route::delete('cart/{rowId}', [Vektor\Checkout\Http\Controllers\Api\CartController::class, 'remove'])->name('api.cart.remove');
    Route::delete('cart', [Vektor\Checkout\Http\Controllers\Api\CartController::class, 'destroy'])->name('api.cart.destroy');

    Route::post('customer/email', [Vektor\Checkout\Http\Controllers\Api\CustomerController::class, 'showByEmail'])->name('api.customer.show_by_email');
});