@extends('layouts.app')

@section('title', 'Historial de Simulaciones - Módulo 3')
@section('module_tag', 'Módulo 03')
@section('module_icon', 'fa-clock-rotate-left')
@section('module_description', 'Historial de ejecuciones registradas en el simulador de planificación')

@section('content')

<div class="card custom-card mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-history text-primary me-2"></i>Historial de Simulaciones</h5>
        <span class="badge bg-secondary font-mono">{{ count($simulaciones) }} registros</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Algoritmo</th>
                    <th>Promedio Espera (s)</th>
                    <th>Promedio Retorno (s)</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($simulaciones as $sim)
                <tr>
                    <td class="font-mono text-muted">#{{ $sim->id }}</td>
                    <td><span class="badge bg-primary font-mono">{{ $sim->algoritmo }}</span></td>
                    <td class="font-mono text-warning fw-bold">{{ number_format((float)$sim->promedio_espera, 2) }} s</td>
                    <td class="font-mono text-success fw-bold">{{ number_format((float)$sim->promedio_retorno, 2) }} s</td>
                    <td class="font-mono text-muted small">{{ $sim->created_at ? $sim->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No hay registros de simulación acumulados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-start mb-4">
    <a href="{{ route('scheduler.index') }}" class="btn btn-outline-secondary font-mono fw-semibold">
        <i class="fa-solid fa-arrow-left me-1"></i> Volver al Simulador
    </a>
</div>

@endsection
