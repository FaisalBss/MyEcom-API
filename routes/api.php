<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\CheckoutApiController;
use App\Http\Controllers\Api\SupportRequestApiController;
use App\Http\Controllers\Api\PasswordResetApiController;
use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\VerificationController;

Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/register', [AuthApiController::class, 'register']);
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware(['signed', 'throttle:6,1']);
Route::post('/password/forgot', [PasswordResetApiController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetApiController::class, 'resetPassword']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::post('/refresh', [AuthApiController::class, 'refresh']);

    Route::middleware(['verified'])->group(function () {
        Route::apiResource('categories', CategoryApiController::class);
        Route::apiResource('products', ProductApiController::class);

        Route::get('/orders', [OrderApiController::class, 'userOrders']);
        Route::get('/orders/{id}', [OrderApiController::class, 'show']);

        Route::get('/cart', [CartApiController::class, 'index']);
        Route::post('/cart/{product}', [CartApiController::class, 'store']);
        Route::put('/cart/{product}', [CartApiController::class, 'update']);
        Route::delete('/cart/clear', [CartApiController::class, 'clear']);
        Route::delete('/cart/{product}', [CartApiController::class, 'destroy']);

        Route::prefix('checkout')->group(function () {
            Route::get('/', [CheckoutApiController::class, 'index']);
            Route::get('/shipping', [CheckoutApiController::class, 'shipping']);
            Route::post('/shipping/store', [CheckoutApiController::class, 'storeShipping']);
            Route::post('/shipping/select', [CheckoutApiController::class, 'selectShipping']);
            Route::get('/payment', [CheckoutApiController::class, 'payment']);
            Route::post('/payment/save', [CheckoutApiController::class, 'savePaymentMethod']);
            Route::post('/payment/place', [CheckoutApiController::class, 'placeOrder']);
        });

        Route::get('/support', [SupportRequestApiController::class, 'index']);
        Route::post('/support', [SupportRequestApiController::class, 'store']);
        Route::get('/support/admin', [SupportRequestApiController::class, 'adminIndex']);
        Route::get('/support/admin/{id}', [SupportRequestApiController::class, 'adminShow']);
        Route::put('/support/admin/{id}', [SupportRequestApiController::class, 'adminUpdateStatus']);
    });
});

Route::get('/home/categories', [HomeApiController::class, 'categories']);
Route::get('/home/products/{catid?}', [HomeApiController::class, 'products']);
Route::get('/home/search', [HomeApiController::class, 'search']);
