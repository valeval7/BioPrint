<?php
use App\Http\Controllers\TrabajoController;
use App\Http\Controllers\ColaController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/trabajos/pendientes',     [TrabajoController::class, 'pendientes']);
    Route::post('/trabajos/{id}/liberar',  [TrabajoController::class, 'liberar']);
    Route::post('/trabajos/{id}/cancelar', [TrabajoController::class, 'cancelar']);
});

Route::patch('/trabajos/{trabajo}/resultado', [ColaController::class, 'resultado']);