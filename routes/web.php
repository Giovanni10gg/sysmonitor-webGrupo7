<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\HardwareController;
use App\Http\Controllers\DeadlockController;

Route::get('/', function () {
    return view('welcome');
});

// --- RUTAS DEL MÓDULO 1 (PROCESOS) ---
Route::get('/processes', [ProcessController::class, 'index'])->name('processes.index');
Route::post('/processes/spawn', [ProcessController::class, 'spawn'])->name('processes.spawn');
Route::post('/processes/kill', [ProcessController::class, 'kill'])->name('processes.kill');
Route::post('/processes/renice', [ProcessController::class, 'renice'])->name('processes.renice');

// --- RUTAS DEL MÓDULO 2 (CPU) ---
Route::get('/hardware', [HardwareController::class, 'index'])->name('hardware.index');

// --- RUTAS DEL MODULO 4 (INTERBLOQUEOS) ---
Route::get('/deadlocks', [DeadlockController::class, 'index'])
    ->name('deadlocks.index');
Route::post('/deadlocks/validate',
    [DeadlockController::class, 'validateMatrices'])
    ->name('deadlocks.validate');
