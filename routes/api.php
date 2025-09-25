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


/*
Json data for testing

/api/register
{
  "name": "krish virani",
  "email": "kris@example.com",
  "password": "abcd"
}

/api/Login
{
  "email": "kris@example.com",
  "password": "abcd"
}   --> provides Token




api/stores
{
  "name": "My s2 Store"
}


api/store-setup/business-details
{
  "store_id": 1,
  "business_name": "John's Store",
  "owner_name": "John Doe",
  "email": "john.store@example.com",
  "phone": "9876543210",
  "gst_number": "27ABCDE1234F2Z5",
  "address": "123 Main Street, City, Country"
}


GET :: /api/store-setup/business-details/
[All stores and their business profiles for current loggged in user]


delete :: /api/store-setup/business-details/{id}
*/