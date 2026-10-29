@extends('layouts.app')

@section('title', 'Resultados de Simulación - Módulo 3')
@section('module_tag', 'Módulo 03')
@section('module_icon', 'fa-chart-pie')
@section('module_description', 'Resultado y Diagrama de Gantt generado para el algoritmo seleccionando')

@section('content')

<!-- TARJETAS DE PROMEDIOS -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card custom-card p-3">
            <span class="text-muted small fw-semibold text-uppercase">Algoritmo Ejecutado</span>
            <h3 class="fw-bold mb-0 mt-1 font-mono text-primary">{{ $algoritmo }}</h3>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card custom-card p-3">
            <span class="text-muted small fw-semibold text-uppercase">Tiempo Promedio de Espera</span>
            <h3 class="fw-bold mb-0 mt-1 font-mono text-warning">{{ $promedioEspera }} s</h3>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card custom-card p-3">
            <span class="text-muted small fw-semibold text-uppercase">Tiempo Promedio de Retorno</span>
            <h3 class="fw-bold mb-0 mt-1 font-mono text-success">{{ $promedioRetorno }} s</h3>
        </div>
    </div>
</div>

<!-- DIAGRAMA DE GANTT VISUAL -->
<div class="card custom-card mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4">
        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-chart-gantt text-info me-2"></i>Diagrama de Gantt</h5>
    </div>
    <div class="card-body p-4">
        <div class="d-flex align-items-center flex-wrap gap-1 p-3 bg-dark rounded-3 overflow-auto">
            @forelse($gantt as $g)
                @php
                    $duracion = max(1, $g['fin'] - $g['inicio']);
                    $flexGrow = $duracion;
                @endphp
                <div class="text-center p-2 rounded text-white font-mono" style="background-color: #3b82f6; min-width: 60px; flex-grow: {{ $flexGrow }};">
                    <div class="fw-bold small">{{ $g['proceso'] }}</div>
                    <div style="font-size: 0.7rem;" class="text-light opacity-75">{{ $g['inicio'] }} - {{ $g['fin'] }}s</div>
                </div>
            @empty
                <div class="text-muted small">No se generó diagrama para esta simulación.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- TABLA DETALLADA DE TIEMPOS -->
<div class="card custom-card mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4">
        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-table me-2 text-secondary"></i>Detalle de Tiempos por Proceso</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Proceso</th>
                    <th>Tiempo Llegada (s)</th>
                    <th>Ráfaga CPU (s)</th>
                    <th>Tiempo Espera (s)</th>
                    <th>Tiempo Retorno (s)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($procesos as $p)
                <tr>
                    <td><span class="badge bg-primary font-mono">{{ $p['nombre'] }}</span></td>
                    <td class="font-mono">{{ $p['llegada'] }}</td>
                    <td class="font-mono">{{ $p['rafaga'] }}</td>
                    <td class="font-mono text-warning fw-bold">{{ $p['espera'] }}</td>
                    <td class="font-mono text-success fw-bold">{{ $p['retorno'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="{{ route('scheduler.index') }}" class="btn btn-outline-secondary font-mono fw-semibold">
        <i class="fa-solid fa-arrow-left me-1"></i> Nueva Simulación
    </a>
    <a href="{{ route('scheduler.comparativa') }}" class="btn btn-primary font-mono fw-semibold">
        Ver Comparativa <i class="fa-solid fa-arrow-right ms-1"></i>
    </a>
</div>

@endsection
