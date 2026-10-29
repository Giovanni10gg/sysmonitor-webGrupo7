<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysMonitor Web - Bitácora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; }
        .hero-banner { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 2rem 1rem; }
    </style>
</head>
<body>
    <header class="hero-banner mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold m-0"><i class="bi bi-journal-text me-2"></i>Módulo 06: Bitácora de Acciones</h2>
                <small class="text-light opacity-75">Registro de auditoría guardado en SQLite</small>
            </div>
            <a href="/" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Volver al Portal</a>
        </div>
    </header>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold m-0">Historial de Auditoría</h4>
            <form method="GET" action="/audit" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Buscar usuario/acción..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="card border-0 shadow-sm rounded-3 p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Acción</th>
                            <th>Objetivo</th>
                            <th>Resultado</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td class="small text-muted">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td class="fw-bold">{{ $log->username }}</td>
                            <td>
                                <span class="badge {{ $log->role === 'admin' ? 'bg-danger' : 'bg-info' }}">
                                    {{ strtoupper($log->role) }}
                                </span>
                            </td>
                            <td><code>{{ $log->action }}</code></td>
                            <td>{{ $log->target }}</td>
                            <td>
                                @if($log->result === 'SUCCESS')
                                    <span class="badge bg-success">ÉXITO</span>
                                @elseif($log->result === 'DENIED')
                                    <span class="badge bg-warning text-dark">DENEGADO</span>
                                @else
                                    <span class="badge bg-danger">FALLO</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $log->ip_address }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No se registran eventos en la bitácora aún.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</body>
</html>
