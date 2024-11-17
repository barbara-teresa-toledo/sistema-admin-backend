<?php

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Financial\FinancialController;
use App\Http\Controllers\ServiceOrder\ServiceOrderController;
use App\Http\Controllers\User\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Clients Routes group
Route::group(['prefix' => 'clients'], function () {
    Route::get('/', [ClientController::class, 'index']);
    Route::post('/', [ClientController::class, 'store']);
    Route::get('/{client}', [ClientController::class, 'edit']);
    Route::get('/name/{name}', [ClientController::class, 'getClientByName']);
    Route::get('/document/{document}', [ClientController::class, 'getClientsByDocument']);
    Route::put('/{client}', [ClientController::class, 'update']);
    Route::delete('/{client}', [ClientController::class, 'destroy']);
});

//ServiceOrders Routes group
Route::group(['prefix' => 'service-orders'], function () {
    Route::get('/', [ServiceOrderController::class, 'index']);
    Route::post('/', [ServiceOrderController::class, 'store']);
    Route::get('/{serviceOrder}', [ServiceOrderController::class, 'edit']);
    Route::put('/{serviceOrder}', [ServiceOrderController::class, 'update']);
    Route::delete('/{serviceOrder}', [ServiceOrderController::class, 'destroy']);
});

//Financial Routes group
Route::group(['prefix' => 'financial'], function () {
    Route::get('/', [FinancialController::class, 'index']);
    Route::post('/', [FinancialController::class, 'store']);
    Route::get('/{financial}', [FinancialController::class, 'edit']);
    Route::put('/{financial}', [FinancialController::class, 'update']);
    Route::delete('/{financial}', [FinancialController::class, 'destroy']);
});

