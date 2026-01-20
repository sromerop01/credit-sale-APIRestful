<?php

use App\Http\Controllers\Api\LoanRoadController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\UserController;
use App\Models\LoanRoad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:60,1'])->group(function () {

    // Health Check
    Route::get('health', fn() => response()->json(['status' => 'ok']))->name('health');

    // Test endpoint
    Route::get('test', fn() => 'API is working!')->name('api.test');

    //Route::apiResource('users', UserController::class);

    Route::apiResource('loan-roads', LoanRoadController::class);

    Route::apiResource('customers', CustomerController::class);

});
