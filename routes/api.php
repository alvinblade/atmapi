<?php

use App\Http\Controllers\Api\V1\ATM\BankAccountController;
use App\Http\Controllers\Api\V1\ATM\BankTransactionController;
use App\Http\Controllers\Api\V1\ATM\OzioATMController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::post('/withdraw/{bankAccountId}', [OzioATMController::class, 'withdraw']);

    Route::group(['middleware' => ['is_admin'], 'prefix' => 'atm'], function () {
        Route::get('/', [OzioATMController::class, 'showATM']);
        Route::put('/', [OzioATMController::class, 'updateATM']);
    });

    Route::group(['middleware' => ['is_admin'], 'prefix' => 'bank-accounts'], function () {
        Route::get('/all', [BankAccountController::class, 'getAllBankAccounts']);
        Route::get('/auth', [BankAccountController::class, 'getAuthUserBankAccounts'])
            ->withoutMiddleware(['is_admin']);
        Route::post('', [BankAccountController::class, 'createBankAccount']);
        Route::put('/{bankAccountId}', [BankAccountController::class, 'updateBankAccount']);
        Route::delete('/{bankAccountId}', [BankAccountController::class, 'deleteBankAccount']);
    });

    Route::group(['middleware' => ['is_admin'], 'prefix' => 'bank-transactions'], function () {
        Route::get('/all', [BankTransactionController::class, 'getAllBankTransactions']);
        Route::get('/auth', [BankTransactionController::class, 'getBankTransactionsByAuth'])
            ->withoutMiddleware('is_admin');
        Route::get('/user/{userId}', [BankTransactionController::class, 'getBankTransactionsByUser']);
        Route::get('/{bankTransactionId}', [BankTransactionController::class, 'showBankTransaction']);
        Route::delete('/{bankTransactionId}', [BankTransactionController::class, 'deleteBankTransaction'])
            ->withoutMiddleware('is_admin');
    });
});
