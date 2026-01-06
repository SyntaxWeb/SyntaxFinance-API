<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RendaController;
use App\Http\Controllers\DividaController;
use App\Http\Controllers\CartaoController;
use App\Http\Controllers\ParcelamentoController;
use App\Http\Controllers\CofrinhoController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('dividas/recorrentes', [DividaController::class, 'storeRecorrentes']);
    Route::apiResource('rendas', RendaController::class);
    Route::apiResource('dividas', DividaController::class);
    Route::apiResource('cartoes', CartaoController::class);
    Route::apiResource('parcelamentos', ParcelamentoController::class);
    Route::apiResource('cofrinhos', CofrinhoController::class);
});
