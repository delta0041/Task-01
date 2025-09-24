<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BusinessDetailController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (requires token)
Route::middleware('auth:sanctum')->group(function () {
    // Store routes
    Route::post('/stores', [StoreController::class, 'create']);
    Route::get('/stores', [StoreController::class, 'index']);
    Route::get('/stores/{id}', [StoreController::class, 'show']);

    // Business detail routes
    Route::post('/store-setup/business-details', [BusinessDetailController::class, 'createOrUpdate']);
    Route::get('/store-setup/business-details', [BusinessDetailController::class, 'show']);
    Route::delete('/store-setup/business-details/{storeId}', [BusinessDetailController::class, 'destroy']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
