<?php

use App\Modules\Categories\Controllers\CategoryController;
use App\Modules\Contact\Controllers\ContactController;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Administration\Controllers\AccessCheckController;
use App\Modules\Products\Controllers\ProductController;
use App\Modules\Quotes\Controllers\QuoteController;
use App\Modules\Services\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:register');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('auth/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:login');
    Route::post('auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:login');
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('admin/access-check', AccessCheckController::class)->middleware('permission:users.view');
        Route::get('quotes/{reference}', [QuoteController::class, 'show']);
    });

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{slug}', [ProductController::class, 'show'])->where('slug', '[a-z0-9-]+');
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{slug}', [CategoryController::class, 'show'])->where('slug', '[a-z0-9-]+');
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{slug}', [ServiceController::class, 'show'])->where('slug', '[a-z0-9-]+');
    Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:contact');
    Route::post('quotes', [QuoteController::class, 'store'])->middleware('throttle:quotes');
});
