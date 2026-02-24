<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/docs', function () {
    return view('documentation');
});

// User routes
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{user}', [UserController::class, 'show']);

//wallet routes
Route::post('/wallets', [WalletController::class, 'store']);
Route::get('/wallets/{wallet}', [WalletController::class, 'show']);
Route::put('/wallets/{wallet}', [WalletController::class, 'update']);
Route::delete('/wallets/{wallet}', [WalletController::class, 'destroy']);

//transaction routes
Route::post('/wallets/{wallet}/transactions', [TransactionController::class, 'store']);
Route::get('/wallets/{wallet}/transactions', [TransactionController::class, 'index']);
Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
