<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\HardwareController;

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
