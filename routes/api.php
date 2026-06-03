<?php

use App\Http\Controllers\Api\LoanRoadController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:60,1'])->group(function () {

    // Health Check
    Route::get('health', fn() => response()
        ->json(['status' => 'ok']))
        ->name('health');

    Route::prefix('auth')->controller(AuthController::class)->group(function(){

        Route::middleware('guest')->group(function(){
            Route::post('login', 'login')
                ->name('auth.login');
        });

        Route::middleware('jwt.auth')->group(function(){
            Route::post('register', 'register')
                ->middleware('level:administrador')
                ->name('auth.register');
            Route::get('me', 'me')
                ->name('auth.me');
            Route::post('logout', 'logout')
                ->name('auth.logout');
            Route::post('refresh-token', 'refreshToken')
                ->name('auth.refreshToken');
        });
    });

    Route::middleware('jwt.auth')->group(function () {

        // Users: solo administrador en todas las acciones
        Route::apiResource('users', UserController::class)
            ->middleware('level:administrador');

        // Customers: destroy solo administrador; el resto con scope en controller
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/',        [CustomerController::class, 'index'])->name('index');
            Route::post('/',       [CustomerController::class, 'store'])->name('store');
            Route::get('/{id}',    [CustomerController::class, 'show'])->name('show');
            Route::put('/{id}',    [CustomerController::class, 'update'])->name('update');
            Route::delete('/{id}', [CustomerController::class, 'destroy'])
                ->middleware('level:administrador')->name('destroy');
        });

        // Loan-roads: middleware por acción + Policy en controller para ownership
        Route::prefix('loan-roads')->name('loan-roads.')->group(function () {
            Route::get('/', [LoanRoadController::class, 'index'])
                ->middleware('level:administrador,supervisor')->name('index');
            Route::post('/', [LoanRoadController::class, 'store'])
                ->middleware('level:administrador')->name('store');
            Route::get('/{id}',    [LoanRoadController::class, 'show'])->name('show');
            Route::put('/{id}',    [LoanRoadController::class, 'update'])
                ->middleware('level:administrador,supervisor')->name('update');
            Route::delete('/{id}', [LoanRoadController::class, 'destroy'])
                ->middleware('level:administrador')->name('destroy');
        });
    });

});
