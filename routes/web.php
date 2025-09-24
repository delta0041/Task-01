<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessDetailController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
