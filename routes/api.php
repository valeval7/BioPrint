<?php

use App\Http\Controllers\TrabajoController;
use App\Http\Controllers\ColaController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/trabajos/pendientes',          [TrabajoController::class, 'pendientes']);
    Route::post('/trabajos/{id}/liberar',        [ColaController::class, 'liberarDesdeAgente']);
    Route::post('/trabajos/{id}/cancelar',       [TrabajoController::class, 'cancelar']);
    Route::post('/trabajos/{id}/fallo-facial',   [ColaController::class, 'falloFacial']);
});