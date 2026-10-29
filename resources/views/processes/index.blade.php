@extends('layouts.app')

@section('title', 'Módulo 1 - Procesos')
@section('module_tag', 'Módulo 01')
@section('module_icon', 'fa-microchip')
@section('module_description', 'Monitoreo y control de procesos del sistema operativo en tiempo real')

@section('content')

<!-- TARJETAS KPIs -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Procesos</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-dark">{{ $summary['total'] ?? count($processes) }}</h2>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                    <i class="fa-solid fa-list-check fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Ejecución (R)</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-success">{{ $summary['R'] ?? $summary['running'] ?? 0 }}</h2>
                </div>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                    <i class="fa-solid fa-play fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">En Espera (S)</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-info">{{ $summary['S'] ?? $summary['sleeping'] ?? 0 }}</h2>
                </div>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-3">
                    <i class="fa-solid fa-moon fa-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card custom-card p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Procesos App</span>
                    <h2 class="fw-bold mb-0 mt-1 font-mono text-warning">{{ count($whitelistedPids) }}</h2>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                    <i class="fa-solid fa-flask fa-lg"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLA Y PANEL LATERAL -->
<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card custom-card">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-microchip text-primary me-2"></i>Tabla de Procesos Activos</h5>
                @if(request('search'))
                    <a href="{{ route('processes.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar filtro</a>
                @endif
            </div>

            <!-- BUSCADOR -->
            <div class="p-3 bg-light border-bottom">
                <form method="GET" action="{{ route('processes.index') }}" class="row g-2">
                    <div class="col-12 col-md-9">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Buscar por PID, usuario o comando..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-12 col-md-3 d-grid">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter me-1"></i> Buscar</button>
                    </div>
                </form>
            </div>

            <!-- TABLA ESTILIZADA -->
            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>PID</th>
                            <th>Usuario / Creador</th>
                            <th>CPU %</th>
                            <th>Memoria %</th>
                            <th>Comando</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($processes as $proc)
                        @php
                            $pid = $proc['pid'] ?? 0;
                            $user = $proc['user'] ?? 'N/A';
                            $cpu = $proc['cpu'] ?? 0;
                            $mem = $proc['mem'] ?? 0;
                            $cmd = $proc['cmd'] ?? $proc['command'] ?? 'N/A';
                            $isAppProcess = in_array($pid, $whitelistedPids);
                            $logInfo = $spawnLogs[$pid] ?? null;
                            $creatorId = $logInfo ? $logInfo->user_id : null;
                            $creatorName = ($creatorId && isset($users[$creatorId])) ? $users[$creatorId] : 'Administrador';
                        @endphp
                        <tr class="{{ $isAppProcess ? 'table-warning-subtle' : '' }}">
                            <td>
                                <span class="badge {{ $isAppProcess ? 'bg-warning text-dark' : 'bg-secondary' }} font-mono">
                                    PID {{ $pid }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $user }}</div>
                                @if($isAppProcess)
                                    <span class="badge bg-primary font-mono" style="font-size: 0.65rem;">
                                        <i class="fa-solid fa-user me-1"></i>{{ $creatorName }}
                                    </span>
                                @else
                                    <span class="text-muted font-mono" style="font-size: 0.7rem;">Sistema</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis font-mono">
                                    {{ number_format((float)$cpu, 1) }}%
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark font-mono border">
                                    {{ number_format((float)$mem, 1) }}%
                                </span>
                            </td>
                            <td class="font-mono text-primary small" style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $cmd }}">
                                {{ $cmd }}
                            </td>
                            <td class="text-end">
                                @if(Auth::user() && Auth::user()->role === 'admin')
                                    @if($isAppProcess)
                                    <form action="{{ route('processes.kill') }}" method="POST" class="d-inline" onsubmit="return confirm('¿Finalizar proceso {{ $pid }}?');">
                                        @csrf
                                        <input type="hidden" name="pid" value="{{ $pid }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger font-mono py-0 px-2">
                                            <i class="fa-solid fa-skull"></i> Kill
                                        </button>
                                    </form>
                                    @else
                                    <span class="badge bg-light text-muted font-mono border">Protegido</span>
                                    @endif
                                @else
                                <span class="badge bg-light text-muted font-mono border">Lectura</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No se encontraron procesos activos que coincidan con la búsqueda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PANEL LATERAL -->
    <div class="col-12 col-xl-4">
        <!-- RENICE -->
        <div class="card custom-card mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-gauge text-warning me-2"></i>Ajustar Prioridad (RENICE)</h6>
            </div>
            <div class="card-body p-4">
                @if(Auth::user() && Auth::user()->role === 'admin')
                <form action="{{ route('processes.renice') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Proceso App Objetivo</label>
                        <select name="pid" class="form-select font-mono" required>
                            <option value="">Seleccionar PID...</option>
                            @foreach($whitelistedPids as $wPid)
                                <option value="{{ $wPid }}">PID {{ $wPid }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Valor Nice (-20 a 19)</label>
                        <input type="number" name="nice" class="form-control font-mono" min="-20" max="19" placeholder="Ej: 5" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 font-mono fw-semibold text-dark">
                        <i class="fa-solid fa-sliders me-1"></i> Aplicar RENICE
                    </button>
                </form>
                @else
                <div class="alert alert-warning mb-0 small"><i class="fa-solid fa-lock me-1"></i> Requiere Administrador.</div>
                @endif
            </div>
        </div>

        <!-- SPAWN -->
        <div class="card custom-card">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-plus text-success me-2"></i>Iniciar Proceso (SPAWN)</h6>
            </div>
            <div class="card-body p-4">
                @if(Auth::user() && Auth::user()->role === 'admin')
                <form action="{{ route('processes.spawn') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Duración (segundos)</label>
                        <input type="number" name="seconds" class="form-control font-mono" value="300" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 font-mono fw-semibold">
                        <i class="fa-solid fa-play me-1"></i> Iniciar Proceso (sleep)
                    </button>
                </form>
                @else
                <div class="alert alert-warning mb-0 small"><i class="fa-solid fa-lock me-1"></i> Requiere Administrador.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
