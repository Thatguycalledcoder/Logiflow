<?php

use App\Http\Controllers\Api\V1\ShipmentController as V1ShipmentController;
use App\Http\Controllers\Api\V2\ShipmentController as V2ShipmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- API Version 1 Namespace ---
Route::prefix('v1')->group(function () {
    Route::get('/shipments', [V1ShipmentController::class, 'index']);
    Route::post('/shipments', [V1ShipmentController::class, 'store']);
});

// --- API Version 2 Namespace ---
Route::prefix('v2')->group(function () {
    Route::get('/shipments', [V2ShipmentController::class, 'index']);
    Route::post('/shipments', [V2ShipmentController::class, 'store']);
});
