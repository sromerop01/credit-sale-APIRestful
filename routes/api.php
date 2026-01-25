<?php

use App\Http\Controllers\Api\LoanRoadController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Models\LoanRoad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:60,1'])->group(function () {

    // Health Check
    Route::get('health', fn() => response()->json(['status' => 'ok']))->name('health');

    Route::prefix('auth')->controller(AuthController::class)->group(function(){

        Route::middleware('guest')->group(function(){
            Route::post('register', 'register')->name('auth.register');
            Route::post('login', 'login')->name('auth.login');
        });

        Route::middleware('jwt.auth')->group(function(){
            Route::get('me', 'me')->name('auth.me');
            Route::post('logout', 'logout')->name('auth.logout');
            Route::post('refresh-token', 'refreshToken')->name('auth.refreshToken');
        });

    });

    Route::middleware('jwt.auth')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('loan-roads', LoanRoadController::class);
    });

});
