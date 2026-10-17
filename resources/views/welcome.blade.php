<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysMonitor Web - Portal de Módulos</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            padding: 3.5rem 1rem;
            border-bottom: 4px solid #3b82f6;
        }
        .card-module {
            border: none;
            border-radius: 12px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .card-module:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .badge-status {
            font-size: 0.8rem;
            padding: 0.4em 0.8em;
            border-radius: 20px;
        }
    </style>
</head>
<body>

    <!-- Header / Banner de Bienvenida -->
    <header class="hero-banner text-center mb-5">
        <div class="container">
            <span class="badge bg-primary mb-3 px-3 py-2 text-uppercase tracking-wider">Sistema de Monitoreo de Servidores</span>
            <h1 class="display-4 fw-bold">SysMonitor Web</h1>
            <p class="lead text-light opacity-75 max-w-2xl mx-auto">
                Plataforma centralizada para el control, registro de métricas y rendimiento en tiempo real del servidor.
            </p>
            <div class="mt-4">
                <span class="badge bg-secondary px-3 py-2">
                    <i class="bi bi-people-fill me-1"></i> Desarrollo por: <strong>Grupo 7</strong>
                </span>
            </div>
        </div>
    </header>

    <!-- Contenido Principal / Grilla de Módulos -->
    <main class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark m-0">
                <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Módulos del Sistema
            </h3>
            <span class="text-muted small">Selecciona un módulo para acceder</span>
        </div>

        <div class="row g-4">

            <!-- Módulo 1: Procesos -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-module h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle badge-status">
                                <i class="bi bi-check-circle-fill me-1"></i>Activo
                            </span>
                            <span class="text-muted fw-bold">Módulo 01</span>
                        </div>
                        <h5 class="card-title fw-bold text-dark">Procesos</h5>
                        <p class="card-text text-secondary flex-grow-1">
                            Listado y administración de procesos reales del sistema.
                        </p>
                        <a href="/processes" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-gear-wide-connected me-1"></i> Abrir Módulo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo 2: CPU y Memoria -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-module h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle badge-status">
                                <i class="bi bi-check-circle-fill me-1"></i>Activo
                            </span>
                            <span class="text-muted fw-bold">Módulo 02</span>
                        </div>
                        <h5 class="card-title fw-bold text-dark">CPU y Memoria</h5>
                        <p class="card-text text-secondary flex-grow-1">
                            Monitoreo de uso de CPU, memoria RAM, swap y carga del sistema.
                        </p>
                        <a href="/hardware" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-cpu me-1"></i> Abrir Módulo
                        </a>
                    </div>
                </div>
            </div>

            <!-- Módulo 3: En desarrollo -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-module h-100 shadow-sm bg-light text-muted">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-warning bg-opacity-20 text-dark border border-warning-subtle badge-status">
                                <i class="bi bi-clock-history me-1"></i>En Desarrollo
                            </span>
                            <span class="text-muted fw-bold">Módulo 03</span>
                        </div>
                        <h5 class="card-title fw-bold text-secondary">Módulo 3</h5>
                        <p class="card-text text-muted flex-grow-1">
                            Módulo en fase de preparación e integración por el equipo de desarrollo.
                        </p>
                        <button class="btn btn-outline-secondary w-100 mt-3" disabled>Próximamente</button>
                    </div>
                </div>
            </div>

            <!-- Módulo 4: En desarrollo -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-module h-100 shadow-sm bg-light text-muted">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-warning bg-opacity-20 text-dark border border-warning-subtle badge-status">
                                <i class="bi bi-clock-history me-1"></i>En Desarrollo
                            </span>
                            <span class="text-muted fw-bold">Módulo 04</span>
                        </div>
                        <h5 class="card-title fw-bold text-secondary">Módulo 4</h5>
                        <p class="card-text text-muted flex-grow-1">
                            Módulo en fase de preparación e integración por el equipo de desarrollo.
                        </p>
                        <button class="btn btn-outline-secondary w-100 mt-3" disabled>Próximamente</button>
                    </div>
                </div>
            </div>

            <!-- Módulo 5: En desarrollo -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-module h-100 shadow-sm bg-light text-muted">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-warning bg-opacity-20 text-dark border border-warning-subtle badge-status">
                                <i class="bi bi-clock-history me-1"></i>En Desarrollo
                            </span>
                            <span class="text-muted fw-bold">Módulo 05</span>
                        </div>
                        <h5 class="card-title fw-bold text-secondary">Módulo 5</h5>
                        <p class="card-text text-muted flex-grow-1">
                            Módulo en fase de preparación e integración por el equipo de desarrollo.
                        </p>
                        <button class="btn btn-outline-secondary w-100 mt-3" disabled>Próximamente</button>
                    </div>
                </div>
            </div>

            <!-- Módulo 6: En desarrollo -->
            <div class="col-md-6 col-lg-4">
                <div class="card card-module h-100 shadow-sm bg-light text-muted">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-warning bg-opacity-20 text-dark border border-warning-subtle badge-status">
                                <i class="bi bi-clock-history me-1"></i>En Desarrollo
                            </span>
                            <span class="text-muted fw-bold">Módulo 06</span>
                        </div>
                        <h5 class="card-title fw-bold text-secondary">Módulo 6</h5>
                        <p class="card-text text-muted flex-grow-1">
                            Módulo en fase de preparación e integración por el equipo de desarrollo.
                        </p>
                        <button class="btn btn-outline-secondary w-100 mt-3" disabled>Próximamente</button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <footer class="bg-white border-top py-4 text-center text-muted">
        <div class="container">
            <small>&copy; {{ date('Y') }} SysMonitor Web - Grupo 7. Todos los derechos reservados.</small>
        </div>
    </footer>

</body>
</html>
