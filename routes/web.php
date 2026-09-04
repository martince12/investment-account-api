<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionHistoryController;
use App\Http\Controllers\AccountTransactionController;


    Route::get('/', function () {
        return auth()->check()
            ? redirect()->route('dashboard')
            : redirect()->route('login');
    });
    
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])
            ->name('login');

        Route::post('/login', [LoginController::class, 'store']);
    });

    Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

    Route::get(
    '/transactions',
    [TransactionHistoryController::class, 'index'])
    ->middleware('auth')
    ->name('transactions.history');

    Route::post(
    '/transactions',
    [AccountTransactionController::class, 'store'])
    ->middleware('auth')
    ->name('transactions.store');

    Route::get(
    '/transactions/sell/{holding}',
    [AccountTransactionController::class, 'sell'])
    ->middleware('auth')
    ->name('transactions.sell');


