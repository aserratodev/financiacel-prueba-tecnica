<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PhoneController;



Route::prefix('/')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

Route::post('/credits', [CreditController::class, 'store']);
Route::get('/credits/{id}', [CreditController::class, 'show']);
Route::get('/credits/{id}/installments', [CreditController::class, 'indexInstallments']);
Route::post('/credits/{id}/approve', [CreditController::class, 'approve']);

Route::get('/clients', [ClientController::class, 'index']);
Route::get('/phones', [PhoneController::class, 'index']);