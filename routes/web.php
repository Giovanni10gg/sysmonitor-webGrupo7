<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\HardwareController;
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

// --- RUTAS DEL MÓDULO 3 (PLANIFICACIÓN CPU) ---
Route::get('/scheduler', [SchedulerController::class, 'index'])
    ->name('scheduler.index');
Route::post('/scheduler/fcfs', [SchedulerController::class, 'fcfs'])
    ->name('scheduler.fcfs');
Route::post('/scheduler/sjf', [SchedulerController::class, 'sjf'])
    ->name('scheduler.sjf');
Route::post('/scheduler/prioridad', [SchedulerController::class, 'prioridad'])
    ->name('scheduler.prioridad');
Route::post('/scheduler/roundrobin', [SchedulerController::class, 'roundRobin'])
    ->name('scheduler.roundrobin');
