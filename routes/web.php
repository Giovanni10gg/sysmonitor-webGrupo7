<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\HardwareController;
use App\Http\Controllers\DeadlockController;
use App\Http\Controllers\SchedulerController;

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
Route::post('/deadlocks/request', [DeadlockController::class, 'requestResources'])->name('deadlocks.request');
Route::post('/deadlocks/cycles', [DeadlockController::class, 'analyzeCycles'])->name('deadlocks.cycles');
// --- RUTAS DEL MÓDULO 3 (PLANIFICACIÓN CPU) ---
Route::get('/scheduler', [SchedulerController::class, 'index'])
    ->name('scheduler.index');
