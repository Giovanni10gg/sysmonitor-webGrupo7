<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\HardwareController;
use App\Http\Controllers\DeadlockController;
use App\Http\Controllers\SchedulerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuditController;

// --- RUTAS PÚBLICAS (AUTENTICACIÓN) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// --- RUTAS PROTEGIDAS (REQUIEREN SESIÓN) ---
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return view('welcome');
    });

    // --- RUTAS DEL MÓDULO 1 (PROCESOS) ---
    Route::get('/processes', [ProcessController::class, 'index'])->name('processes.index');
    
    // Acciones del SO reservadas exclusivamente para el rol Administrador
    Route::middleware(['admin'])->group(function () {
        Route::post('/processes/spawn', [ProcessController::class, 'spawn'])->name('processes.spawn');
        Route::post('/processes/kill', [ProcessController::class, 'kill'])->name('processes.kill');
        Route::post('/processes/renice', [ProcessController::class, 'renice'])->name('processes.renice');
    });

    // --- RUTAS DEL MÓDULO 2 (CPU & HARDWARE) ---
    Route::get('/hardware', [HardwareController::class, 'index'])->name('hardware.index');

    // --- RUTAS DEL MÓDULO 3 (PLANIFICACIÓN CPU) ---
    Route::get('/scheduler', [SchedulerController::class, 'index'])->name('scheduler.index');
    Route::post('/scheduler/fcfs', [SchedulerController::class, 'fcfs'])->name('scheduler.fcfs');
    Route::post('/scheduler/sjf', [SchedulerController::class, 'sjf'])->name('scheduler.sjf');
    Route::post('/scheduler/prioridad', [SchedulerController::class, 'prioridad'])->name('scheduler.prioridad');
    Route::post('/scheduler/roundrobin', [SchedulerController::class, 'roundRobin'])->name('scheduler.roundrobin');
    Route::post('/scheduler/srtf', [SchedulerController::class, 'srtf'])->name('scheduler.srtf');
    Route::get('/scheduler/historial', [SchedulerController::class, 'historial'])->name('scheduler.historial');
    Route::get('/scheduler/comparativa', [SchedulerController::class, 'comparativa'])->name('scheduler.comparativa');

    // --- RUTAS DEL MÓDULO 4 (INTERBLOQUEOS) ---
    Route::get('/deadlocks', [DeadlockController::class, 'index'])->name('deadlocks.index');
    Route::post('/deadlocks/validate', [DeadlockController::class, 'validateMatrices'])->name('deadlocks.validate');
    Route::post('/deadlocks/request', [DeadlockController::class, 'requestResources'])->name('deadlocks.request');
    Route::post('/deadlocks/cycles', [DeadlockController::class, 'analyzeCycles'])->name('deadlocks.cycles');

    // --- RUTAS DEL MÓDULO 6 (BITÁCORA Y AUDITORÍA) ---
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
});
