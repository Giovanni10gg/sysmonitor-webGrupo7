<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysMonitor Web - Módulo 6: Bitácora y Permisos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-main: #f4f6f9;
            --slate-header: #1e293b;
            --slate-header-hover: #334155;
            --card-bg: #ffffff;
            --primary-blue: #2563eb;
            --success-green: #10b981;
            --danger-red: #ef4444;
            --text-dark: #0f172a;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-dark);
            min-height: 100vh;
        }

        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .navbar-slate { background-color: var(--slate-header); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); }
        .navbar-slate .nav-link { color: #94a3b8; font-weight: 500; padding: 0.5rem 0.9rem; border-radius: 6px; }
        .navbar-slate .nav-link:hover { color: #ffffff; background-color: var(--slate-header-hover); }
        .navbar-slate .nav-link.active { color: #ffffff; background-color: #3b82f6; }

        .custom-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .icon-shape { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .badge-role-admin { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-role-observer { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        .badge-success-soft { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-danger-soft { background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-warning-soft { background-color: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

        .table-custom th {
            background-color: #f8fafc; color: #475569; font-size: 0.75rem;
            text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; padding: 12px 16px;
        }
        .table-custom td { padding: 14px 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }

        .json-box {
            background-color: #0f172a; color: #38bdf8; border-radius: 8px; padding: 16px;
            font-family: 'JetBrains Mono', monospace; font-size: 0.825rem; max-height: 350px; overflow-y: auto;
        }
        .pid-tag { background-color: #e2e8f0; color: #334155; padding: 2px 6px; border-radius: 4px; font-size: 0.8rem; }
    </style>
</head>
<body>

    <!-- TOP NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-slate sticky-top py-2">
        <div class="container-fluid px-lg-4">
            <a class="navbar-brand fw-bold d-flex align-items-center me-4" href="/">
                <div class="bg-primary text-white rounded-3 p-2 me-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                    <i class="fa-solid fa-server"></i>
                </div>
                <span>SysMonitor <span class="text-primary fw-light">Web</span></span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNavbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/processes"><i class="fa-solid fa-microchip me-1"></i> Procesos</a></li>
                    <li class="nav-item"><a class="nav-link" href="/hardware"><i class="fa-solid fa-gauge-high me-1"></i> CPU</a></li>
                    <li class="nav-item"><a class="nav-link" href="/scheduler"><i class="fa-solid fa-clock me-1"></i> Scheduler</a></li>
                    <li class="nav-item"><a class="nav-link" href="/deadlocks"><i class="fa-solid fa-lock me-1"></i> Deadlocks</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/audit"><i class="fa-solid fa-list-check me-1"></i> Bitácora</a></li>
                </ul>

                @auth
                <div class="d-flex align-items-center gap-3 p-2 rounded-3 ms-auto">
                    <div class="d-flex align-items-center me-2">
                        <div class="lh-sm text-end">
                            <div class="fw-bold text-white small">{{ Auth::user()->name }}</div>
                            <span class="badge {{ Auth::user()->role === 'admin' ? 'badge-role-admin' : 'badge-role-observer' }}">
                                Rol: {{ strtoupper(Auth::user()->role) }}
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light"><i class="fa-solid fa-right-from-bracket"></i></button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- STATUS BAR -->
    <div class="bg-white border-bottom py-2 shadow-sm">
        <div class="container-fluid px-lg-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-dark font-mono"><i class="fa-solid fa-cube text-info me-1"></i> Módulo 6</span>
                <span class="small fw-semibold text-secondary"><i class="fa-solid fa-database text-warning me-1"></i> SQLite Engine: <span class="text-dark font-mono">database.sqlite</span></span>
            </div>
            <a href="/" class="btn btn-sm btn-outline-secondary rounded-2"><i class="fa-solid fa-arrow-left me-1"></i> Volver al Portal</a>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <main class="container-fluid px-lg-4 py-4">

        <!-- TOP KPI CARDS -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card custom-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Total Acciones</span>
                            <h2 class="fw-bold mb-0 mt-1 font-mono text-dark">{{ $logs->total() }}</h2>
                        </div>
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary"><i class="fa-solid fa-bolt fa-lg"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card custom-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Usuario Actual</span>
                            <h2 class="fw-bold mb-0 mt-1 font-mono text-dark" style="font-size: 1.2rem;">{{ Auth::user()->name ?? 'Invitado' }}</h2>
                        </div>
                        <div class="icon-shape bg-info bg-opacity-10 text-info"><i class="fa-solid fa-user-shield fa-lg"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card custom-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Estado del Sistema</span>
                            <h2 class="fw-bold mb-0 mt-1 font-mono text-success" style="font-size: 1.2rem;">ACTIVO</h2>
                        </div>
                        <div class="icon-shape bg-success bg-opacity-10 text-success"><i class="fa-solid fa-circle-check fa-lg"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card custom-card p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-semibold text-uppercase">Integridad Bitácora</span>
                            <h2 class="fw-bold mb-0 mt-1 font-mono text-dark" style="font-size: 1.2rem;">SHA256 OK</h2>
                        </div>
                        <div class="icon-shape bg-warning bg-opacity-10 text-warning"><i class="fa-solid fa-database fa-lg"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN LAYOUT GRID -->
        <div class="row g-4">
            <!-- TABLE SECTION -->
            <div class="col-12 col-xl-8">
                <div class="card custom-card">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-shield-cat text-primary me-2"></i>Bitácora de Eventos y Auditoría</h5>
                    </div>

                    <!-- Filters Bar -->
                    <div class="p-3 bg-light border-bottom">
                        <form method="GET" action="{{ route('audit.index') }}" class="row g-2">
                            <div class="col-12 col-md-9">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    <input type="text" name="search" class="form-control" placeholder="Buscar por usuario, PID, acción o IP..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-12 col-md-3 d-grid">
                                <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-filter me-1"></i> Filtrar</button>
                            </div>
                        </form>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha y Hora</th>
                                    <th>Usuario & Rol</th>
                                    <th>IP</th>
                                    <th>Acción</th>
                                    <th>Objetivo</th>
                                    <th>Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td class="font-mono">#{{ $log->id }}</td>
                                    <td class="font-mono text-dark small">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $log->username }}</div>
                                        <span class="badge {{ $log->role === 'admin' ? 'badge-role-admin' : 'badge-role-observer' }} font-mono" style="font-size:0.65rem;">
                                            {{ strtoupper($log->role) }}
                                        </span>
                                    </td>
                                    <td class="font-mono text-muted small">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                                    <td><span class="badge bg-dark font-mono">{{ $log->action }}</span></td>
                                    <td class="font-mono text-primary">{{ $log->target }}</td>
                                    <td>
                                        @if($log->result === 'SUCCESS' || $log->result === 'ÉXITO')
                                            <span class="badge badge-success-soft font-mono">ÉXITO</span>
                                        @elseif($log->result === 'DENIED' || $log->result === 'DENEGADO')
                                            <span class="badge badge-warning-soft font-mono">DENEGADO</span>
                                        @else
                                            <span class="badge badge-danger-soft font-mono">FALLO</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No hay registros guardados en la bitácora.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer bg-white border-top py-3 px-4">
                        {{ $logs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

            <!-- SIDE PANEL: CONTROL DE PERMISOS -->
            <div class="col-12 col-xl-4">
                <div class="card custom-card">
                    <div class="card-header bg-white border-bottom py-3 px-4">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-sliders text-warning me-2"></i>Control de Permisos RBAC</h6>
                    </div>
                    <div class="card-body p-4">
                        @if(Auth::user()->role === 'admin')
                            <div class="alert alert-primary d-flex align-items-start gap-3 rounded-3 shadow-sm mb-3">
                                <i class="fa-solid fa-user-shield fa-2x text-primary mt-1"></i>
                                <div>
                                    <div class="fw-bold">Perfil: ADMINISTRADOR</div>
                                    <div class="small">Tienes permisos completos para enviar señales KILL, RENICE y administrar el sistema.</div>
                                </div>
                            </div>
                            <p class="small text-muted">Las acciones ejecutadas en el Módulo 1 (Procesos) quedarán registradas automáticamente en la bitácora SQLite.</p>
                        @else
                            <div class="alert alert-warning d-flex align-items-start gap-3 rounded-3 shadow-sm mb-3">
                                <i class="fa-solid fa-triangle-exclamation fa-2x text-warning mt-1"></i>
                                <div>
                                    <div class="fw-bold">Perfil: OBSERVADOR</div>
                                    <div class="small">Acceso de solo lectura. No puedes ejecutar acciones que alteren los procesos del sistema.</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
