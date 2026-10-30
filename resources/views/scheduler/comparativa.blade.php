@extends('layouts.app')

@section('title', 'Comparativa de Algoritmos - Módulo 3')
@section('module_tag', 'Módulo 03')
@section('module_icon', 'fa-chart-column')
@section('module_description', 'Análisis comparativo de rendimiento promedio e historial de ejecuciones')

@section('content')

@php
    $sims = $simulaciones ?? [];
    $compData = $comparativaData ?? collect();
@endphp

<!-- TARJETAS DE MÉTRICAS CLAVE -->
@if(count($compData) > 0)
@php
    $bestEspera = $compData->sortBy('avg_espera')->first();
    $bestRetorno = $compData->sortBy('avg_retorno')->first();
@endphp
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Simulaciones</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-dark">{{ count($sims) }}</h2>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="fa-solid fa-calculator fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-4">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Mejor Espera Promedio</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-success">{{ $bestEspera->algoritmo ?? 'N/A' }}</h2>
                    <span class="text-muted small font-mono">{{ number_format((float)($bestEspera->avg_espera ?? 0), 2) }} s</span>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="fa-solid fa-trophy fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Mejor Retorno Promedio</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-info">{{ $bestRetorno->algoritmo ?? 'N/A' }}</h2>
                    <span class="text-muted small font-mono">{{ number_format((float)($bestRetorno->avg_retorno ?? 0), 2) }} s</span>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                    <i class="fa-solid fa-gauge-high fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLA 1: RESUMEN AGRUPADO POR ALGORITMO -->
<div class="card custom-card mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-square-poll-vertical text-primary me-2"></i>Rendimiento Promedio por Algoritmo
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>Algoritmo</th>
                    <th>Total Ejecuciones</th>
                    <th>Promedio Espera (s)</th>
                    <th>Promedio Retorno (s)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($compData as $row)
                <tr>
                    <td><span class="badge bg-primary font-mono">{{ $row->algoritmo }}</span></td>
                    <td class="font-mono">{{ $row->total_runs }}</td>
                    <td class="font-mono text-warning fw-bold">{{ number_format((float)$row->avg_espera, 2) }} s</td>
                    <td class="font-mono text-success fw-bold">{{ number_format((float)$row->avg_retorno, 2) }} s</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No hay métricas agrupadas disponibles.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- TABLA 2: HISTORIAL INDIVIDUAL DE EJECUCIONES -->
<div class="card custom-card mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-list-check text-secondary me-2"></i>Historial de Ejecuciones Individuales
        </h5>
        <span class="badge bg-secondary font-mono">{{ count($sims) }} registros</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th># ID</th>
                    <th>Algoritmo</th>
                    <th>Promedio Espera</th>
                    <th>Promedio Retorno</th>
                    <th>Fecha / Hora</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sims as $sim)
                <tr>
                    <td class="font-mono text-muted">#{{ $sim->id }}</td>
                    <td>
                        <span class="badge bg-info-subtle text-info-emphasis font-mono">
                            {{ $sim->algoritmo }}
                        </span>
                    </td>
                    <td class="font-mono text-warning fw-bold">
                        {{ number_format((float)$sim->promedio_espera, 2) }} s
                    </td>
                    <td class="font-mono text-success fw-bold">
                        {{ number_format((float)$sim->promedio_retorno, 2) }} s
                    </td>
                    <td class="font-mono text-muted small">
                        {{ $sim->created_at ? $sim->created_at->format('Y-m-d H:i:s') : 'N/A' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No existen simulaciones registradas en la base de datos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- BOTÓN VOLVER -->
<div class="d-flex justify-content-start mb-4">
    <a href="{{ route('scheduler.index') }}" class="btn btn-outline-secondary font-mono fw-semibold">
        <i class="fa-solid fa-arrow-left me-1"></i> Volver al Simulador
    </a>
</div>

@endsection
