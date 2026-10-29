<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysMonitor Web - @yield('title', 'Dashboard')</title>
    
    <!-- Bootstrap 5, FontAwesome & Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-main: #f4f6f9;
            --slate-header: #1e293b;
            --slate-header-hover: #334155;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-main);
            color: #0f172a;
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
        .badge-role-admin { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-role-observer { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .table-custom th {
            background-color: #f8fafc; color: #475569; font-size: 0.75rem;
            text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; padding: 12px 16px;
        }
        .table-custom td { padding: 14px 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }
    </style>
    @stack('styles')
</head>
<body>

    <!-- NAVBAR UNIFICADA -->
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
                    <li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('processes*') ? 'active' : '' }}" href="/processes"><i class="fa-solid fa-microchip me-1"></i> Procesos</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('hardware*') ? 'active' : '' }}" href="/hardware"><i class="fa-solid fa-gauge-high me-1"></i> CPU</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('scheduler*') ? 'active' : '' }}" href="/scheduler"><i class="fa-solid fa-clock me-1"></i> Scheduler</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('deadlocks*') ? 'active' : '' }}" href="/deadlocks"><i class="fa-solid fa-lock me-1"></i> Deadlocks</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->is('audit*') ? 'active' : '' }}" href="/audit"><i class="fa-solid fa-list-check me-1"></i> Bitácora</a></li>
                </ul>

                @auth
                <div class="d-flex align-items-center gap-3 p-2 rounded-3 ms-auto">
                    <div class="lh-sm text-end">
                        <div class="fw-bold text-white small">{{ Auth::user()->name }}</div>
                        <span class="badge {{ Auth::user()->role === 'admin' ? 'badge-role-admin' : 'badge-role-observer' }}">
                            Rol: {{ strtoupper(Auth::user()->role) }}
                        </span>
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

    <!-- BARRA DE ESTADO / TÍTULO DEL MÓDULO -->
    <div class="bg-white border-bottom py-2 shadow-sm mb-4">
        <div class="container-fluid px-lg-4 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-dark font-mono"><i class="fa-solid @yield('module_icon', 'fa-cube') text-info me-1"></i> @yield('module_tag', 'Módulo')</span>
                <span class="small fw-semibold text-secondary">@yield('module_description', 'Panel de Monitoreo del Sistema')</span>
            </div>
            <a href="/" class="btn btn-sm btn-outline-secondary rounded-2"><i class="fa-solid fa-arrow-left me-1"></i> Volver al Portal</a>
        </div>
    </div>

    <!-- CONTENIDO ESPECÍFICO -->
    <main class="container-fluid px-lg-4 pb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
