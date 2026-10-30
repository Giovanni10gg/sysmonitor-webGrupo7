@extends('layouts.app')

@section('title', 'Módulo 2 - CPU & Hardware')
@section('module_tag', 'Módulo 02')
@section('module_icon', 'fa-gauge-high')
@section('module_description', 'Métricas del procesador, uso de memoria RAM y rendimiento del servidor')

@section('content')

@php
    $ramPct = $memoryInfo['ram']['used_pct'] ?? 0;
    $ramUsed = $memoryInfo['ram']['used_mb'] ?? 0;
    $ramTotal = $memoryInfo['ram']['total_mb'] ?? 0;

    $swapPct = $memoryInfo['swap']['used_pct'] ?? 0;
    $swapUsed = $memoryInfo['swap']['used_mb'] ?? 0;
    $swapTotal = $memoryInfo['swap']['total_mb'] ?? 0;
@endphp

<!-- TARJETAS DE MÉTRICAS RÁPIDAS (KPIs) -->
<div class="row g-3 mb-4">
    <!-- CPU -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Carga de CPU</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-dark">
                        {{ number_format($cpuUsage ?? 0, 1) }}%
                    </h2>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="fa-solid fa-microchip fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- RAM -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Uso de Memoria</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-dark">
                        {{ number_format($ramPct, 1) }}%
                    </h2>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                    <i class="fa-solid fa-memory fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tiempo Activo -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Tiempo Activo</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-dark" style="font-size: 1.05rem;">
                        {{ $cpuInfo['uptime'] ?? 'N/A' }}
                    </h2>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="fa-solid fa-clock fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Kernel / SO -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Sistema Operativo</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-dark" style="font-size: 1.1rem;">
                        Linux x86_64
                    </h2>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="fa-brands fa-linux fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SECCIÓN PRINCIPAL DE DETALLES -->
<div class="row g-4">
    <!-- DETALLES DE RECURSOS -->
    <div class="col-12 col-xl-8">
        <!-- Memoria RAM -->
        <div class="card custom-card mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-memory text-primary me-2"></i>Memoria Principal (RAM) y Swap</h5>
            </div>
            <div class="card-body p-4">
                <!-- RAM -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold small">Uso de Memoria RAM</span>
                        <span class="font-mono small fw-bold">
                            {{ number_format($ramUsed, 1) }} MB / {{ number_format($ramTotal, 1) }} MB
                        </span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" 
                             style="width: {{ $ramPct }}%"></div>
                    </div>
                </div>

                <!-- SWAP -->
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-semibold small">Memoria Swap</span>
                        <span class="font-mono small fw-bold">
                            {{ number_format($swapUsed, 1) }} MB / {{ number_format($swapTotal, 1) }} MB
                        </span>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" 
                             style="width: {{ $swapPct }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Especificaciones CPU -->
        <div class="card custom-card">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-microchip text-primary me-2"></i>Especificaciones del Procesador</h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">Modelo CPU</th>
                                <td class="font-mono text-dark fw-semibold">{{ $cpuInfo['model'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Núcleos / Cores</th>
                                <td class="font-mono"><span class="badge bg-dark">{{ $cpuInfo['cores'] ?? 1 }} vCPU</span></td>
                            </tr>
                            <tr>
                                <th>Carga Media (Load Avg)</th>
                                <td class="font-mono text-primary">{{ $memoryInfo['load_avg'] ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- INFORMACIÓN DEL SERVIDOR -->
    <div class="col-12 col-xl-4">
        <div class="card custom-card">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-sliders text-info me-2"></i>Servidor & VPS</h6>
            </div>
            <div class="card-body p-4">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Hosting</span>
                        <span class="fw-bold font-mono text-dark">Alwaysdata PaaS</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Versión PHP</span>
                        <span class="badge bg-primary font-mono">PHP {{ phpversion() }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Motor de BD</span>
                        <span class="badge bg-secondary font-mono">SQLite 3</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted">Zona Horaria</span>
                        <span class="font-mono text-dark">{{ config('app.timezone') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
