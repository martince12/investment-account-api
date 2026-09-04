<?php

use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\ClientController;



Route::post(
    '/accounts/{account}/transactions',
    [TransactionController::class, 'store']
);

Route::get(
    '/accounts/{account}',
    [AccountController::class, 'show']
);

Route::get(
    '/accounts/{account}/transactions',
    [TransactionController::class, 'index']
);


Route::get(
    '/clients', 
    [ClientController::class, 'index']
);

Route::post(
    '/clients', 
    [ClientController::class, 'store']
);

Route::get(
    '/clients/{client}', 
    [ClientController::class, 'show']
);