@extends('layouts.app')

@section('title', 'SysMonitor Web - Módulo 5: Almacenamiento y Usuarios')
@section('module_tag', 'Módulo 05')
@section('module_icon', 'fa-hard-drive')
@section('module_description', 'Monitoreo de sistemas de archivos, particiones de disco, usuarios /etc/passwd, sesiones activas y explorador de logs /var/log')

@section('content')

<style>
    .card-header-clean {
        background-color: transparent;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-header-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .badge-soft-success { background-color: #dcfce7; color: #15803d; font-weight: 600; border: 1px solid rgba(34, 197, 94, 0.2); }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; font-weight: 600; border: 1px solid rgba(100, 116, 139, 0.2); }
    .badge-soft-primary { background-color: #e0f2fe; color: #0369a1; font-weight: 600; border: 1px solid rgba(14, 165, 233, 0.2); }
    
    .table-custom th {
        font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 1rem;
    }
    .table-custom td { padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; vertical-align: middle; }
    
    .explorer-breadcrumb { background-color: #f1f5f9; border-radius: 8px; padding: 0.5rem 1rem; margin-bottom: 1rem; }
    .terminal-modal-content { background-color: #0f172a; color: #e2e8f0; border-radius: 12px; border: 1px solid #334155; overflow: hidden; }
    .terminal-header { background-color: #1e293b; padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #334155; }
    .terminal-dots { display: flex; gap: 6px; }
    .terminal-dot { width: 12px; height: 12px; border-radius: 50%; }
    .dot-red { background-color: #ef4444; } .dot-yellow { background-color: #f59e0b; } .dot-green { background-color: #10b981; }
    .terminal-body { font-family: 'JetBrains Mono', monospace; padding: 1.25rem; max-height: 480px; overflow-y: auto; font-size: 0.85rem; line-height: 1.6; }
    .terminal-line { display: flex; margin-bottom: 4px; }
    .log-time { color: #64748b; margin-right: 12px; flex-shrink: 0; }
    .log-level-info { color: #38bdf8; font-weight: 600; margin-right: 10px; flex-shrink: 0; }
    .log-level-warn { color: #fbbf24; font-weight: 600; margin-right: 10px; flex-shrink: 0; }
    .log-level-error { color: #f87171; font-weight: 600; margin-right: 10px; flex-shrink: 0; }
    .log-level-success { color: #4ade80; font-weight: 600; margin-right: 10px; flex-shrink: 0; }
    .log-msg { color: #f1f5f9; word-break: break-all; }
    .pulse-dot { width: 8px; height: 8px; background-color: #22c55e; border-radius: 50%; display: inline-block; box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); animation: pulse-green 2s infinite; }
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
</style>

<!-- BANNER DE NOTIFICACIÓN EN TIEMPO REAL -->
<div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center justify-content-between" role="alert" style="background-color: #e0f2fe; color: #0369a1;">
    <div class="d-flex align-items-center gap-2">
        <i class="fa-solid fa-circle-info fa-lg text-info me-1"></i>
        <div>
            <strong>Sincronización activa:</strong> Monitoreo de discos, cuentas <code>/etc/passwd</code> y registros en <code>/var/log</code>.
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-dark font-mono me-2" id="lastSyncText">Última sincr: Hace 1s</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<!-- ==================== FILA 1: DISCOS Y ESPACIO ==================== -->
<div class="row g-4 mb-4">
    <!-- USO DE SISTEMAS DE ARCHIVOS (df -h) -->
    <div class="col-12 col-xl-6">
        <div class="card custom-card h-100">
            <div class="card-header-clean">
                <h2 class="card-header-title">
                    <i class="fa-solid fa-hard-drive text-primary"></i>
                    Uso de Sistemas de Archivos <span class="badge bg-light text-dark font-mono fw-normal ms-1 border">df -h</span>
                </h2>
                <span class="badge badge-soft-primary rounded-pill">{{ count($disks) }} Montajes</span>
            </div>
            <div class="card-body p-3 p-md-4">
                <p class="text-muted small mb-4">Monitoreo de particiones de disco activas y capacidad utilizada.</p>

                @foreach($disks as $disk)
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="fw-bold font-mono text-dark me-2">{{ $disk['device'] }}</span>
                            <span class="badge bg-light text-secondary border font-mono">{{ $disk['mount'] }}</span>
                            <span class="text-muted small ms-2">{{ $disk['type'] }}</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold {{ $disk['use_percent'] > 90 ? 'text-danger' : ($disk['use_percent'] > 75 ? 'text-warning' : 'text-success') }}">
                                {{ $disk['use_percent'] }}%
                            </span>
                            <span class="text-muted small ms-1">({{ $disk['used'] }} / {{ $disk['size'] }})</span>
                        </div>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 6px;">
                        <div class="progress-bar {{ $disk['use_percent'] > 90 ? 'bg-danger' : ($disk['use_percent'] > 75 ? 'bg-warning' : 'bg-success') }} progress-bar-striped progress-bar-animated"
                             role="progressbar" style="width: {{ $disk['use_percent'] }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1 text-muted extra-small" style="font-size: 0.78rem;">
                        <span>Disponible: {{ $disk['avail'] }}</span>
                        <span>Inodos: OK</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ESTRUCTURA DE PARTICIONES (lsblk & findmnt) -->
    <div class="col-12 col-xl-6">
        <div class="card custom-card h-100">
            <div class="card-header-clean">
                <h2 class="card-header-title">
                    <i class="fa-solid fa-sitemap text-indigo" style="color: #6366f1;"></i>
                    Estructura de Particiones <span class="badge bg-light text-dark font-mono fw-normal ms-1 border">lsblk & findmnt</span>
                </h2>
                <span class="badge badge-soft-secondary rounded-pill">{{ count($partitions) }} Bloques</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Dispositivo</th>
                                <th>Tabla</th>
                                <th>Punto Montaje</th>
                                <th>Formato</th>
                                <th>Tamaño</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($partitions as $part)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-hard-drive text-secondary"></i>
                                        <span class="fw-semibold font-mono">{{ $part['name'] }}</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-mono">{{ $part['table'] }}</span></td>
                                <td><span class="font-mono fw-bold text-dark">{{ $part['mount'] }}</span></td>
                                <td><span class="badge bg-light text-dark border font-mono">{{ $part['type'] }}</span></td>
                                <td class="font-mono">{{ $part['size'] }}</td>
                                <td><span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">{{ $part['status'] }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== FILA 2: USUARIOS Y SESIONES ==================== -->
<div class="row g-4 mb-4">
    <!-- USUARIOS DEL SISTEMA (/etc/passwd) -->
    <div class="col-12 col-xl-7">
        <div class="card custom-card h-100">
            <div class="card-header-clean flex-wrap gap-2">
                <h2 class="card-header-title">
                    <i class="fa-solid fa-users text-success"></i>
                    Usuarios del Sistema <span class="badge bg-light text-dark font-mono fw-normal ms-1 border">/etc/passwd</span>
                </h2>
                <div class="input-group input-group-sm" style="max-width: 200px;">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="userSearchInput" class="form-control border-start-0 bg-light" placeholder="Buscar usuario..." onkeyup="filterUsers()">
                </div>
            </div>

            <div class="px-3 pt-3 pb-2 border-bottom bg-light bg-opacity-50 d-flex gap-2">
                <button class="btn btn-sm btn-dark rounded-pill px-3 filter-user-btn active" onclick="filterUserType('all', this)">
                    Todos <span class="badge bg-secondary ms-1">{{ count($users) }}</span>
                </button>
                <button class="btn btn-sm btn-outline-success rounded-pill px-3 filter-user-btn" onclick="filterUserType('human', this)">
                    <i class="fa-solid fa-user me-1"></i> Humanos
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-user-btn" onclick="filterUserType('system', this)">
                    <i class="fa-solid fa-gear me-1"></i> Sistema
                </button>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-custom mb-0" id="usersTable">
                        <thead class="sticky-top">
                            <tr>
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>UID</th>
                                <th>Shell</th>
                                <th>Directorio Home</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr data-type="{{ $user['is_human'] ? 'human' : 'system' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle {{ $user['is_human'] ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                                            <i class="fa-solid {{ $user['is_human'] ? 'fa-user' : 'fa-microchip' }}"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark font-mono">{{ $user['username'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user['is_human'])
                                        <span class="badge badge-soft-success"><i class="fa-solid fa-check me-1"></i>Humano</span>
                                    @else
                                        <span class="badge badge-soft-secondary"><i class="fa-solid fa-microchip me-1"></i>Sistema</span>
                                    @endif
                                </td>
                                <td><span class="font-mono fw-semibold">{{ $user['uid'] }}</span></td>
                                <td><span class="badge bg-light text-dark border font-mono">{{ $user['shell'] }}</span></td>
                                <td><span class="font-mono text-muted">{{ $user['home'] }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay registros de usuarios disponibles.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SESIONES ACTIVAS (who / w) -->
    <div class="col-12 col-xl-5">
        <div class="card custom-card h-100">
            <div class="card-header-clean">
                <h2 class="card-header-title">
                    <i class="fa-solid fa-terminal text-warning"></i>
                    Sesiones Activas <span class="badge bg-light text-dark font-mono fw-normal ms-1 border">who / w</span>
                </h2>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">
                    <i class="fa-solid fa-circle fa-2xs me-1"></i> {{ count($sessions) }} Conectados
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Terminal</th>
                                <th>IP Origen</th>
                                <th>Inicio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $sess)
                            <tr>
                                <td><div class="d-flex align-items-center gap-2"><span class="pulse-dot"></span><span class="fw-bold text-dark font-mono">{{ $sess['user'] }}</span></div></td>
                                <td><span class="badge bg-dark text-white font-mono">{{ $sess['tty'] }}</span></td>
                                <td><span class="font-mono text-primary">{{ $sess['ip'] }}</span></td>
                                <td><span class="font-mono text-dark small">{{ $sess['time'] }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Sin sesiones SSH activas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== FILA 3: EXPLORADOR DE ARCHIVOS ==================== -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card custom-card">
            <div class="card-header-clean flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <h2 class="card-header-title">
                        <i class="fa-regular fa-folder-open text-primary"></i>
                        Explorador de Archivos / Visor de Logs
                    </h2>
                    <span class="badge badge-soft-primary"><i class="fa-solid fa-lock me-1"></i> Modo Solo Lectura</span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="min-width: 230px;">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-filter text-muted"></i></span>
                        <input type="text" id="fileSearchInput" class="form-control border-start-0 bg-light" placeholder="Filtrar archivos..." onkeyup="filterFiles()">
                    </div>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb explorer-breadcrumb align-items-center mb-3">
                        <li class="breadcrumb-item"><a href="javascript:void(0)"><i class="fa-solid fa-server me-1"></i> / (root)</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">var</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><i class="fa-regular fa-folder-open me-1 text-warning"></i> log</li>
                    </ol>
                </nav>

                <div class="table-responsive">
                    <table class="table table-custom mb-0" id="fileExplorerTable">
                        <thead>
                            <tr>
                                <th>Nombre del Archivo</th>
                                <th>Permisos</th>
                                <th>Propietario:Grupo</th>
                                <th>Tamaño</th>
                                <th>Última Modificación</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-name="syslog">
                                <td><div class="d-flex align-items-center gap-2"><i class="fa-solid fa-file-lines text-primary fa-lg"></i><span class="fw-bold font-mono text-dark">syslog</span><span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small ms-1">Crítico</span></div></td>
                                <td><span class="font-mono text-muted">-rw-r-----</span></td>
                                <td><span class="badge bg-light text-dark border font-mono">syslog:adm</span></td>
                                <td class="font-mono">1.4 MB</td>
                                <td class="font-mono text-muted">2026-10-24 11:53:02</td>
                                <td class="text-end"><button class="btn btn-sm btn-dark font-mono rounded-2 px-3" onclick="openLogViewer('syslog', '/var/log/syslog')"><i class="fa-solid fa-terminal me-1 text-info"></i> Ver Contenido</button></td>
                            </tr>
                            <tr data-name="auth.log">
                                <td><div class="d-flex align-items-center gap-2"><i class="fa-solid fa-shield-cat text-warning fa-lg"></i><span class="fw-bold font-mono text-dark">auth.log</span><span class="badge bg-warning-subtle text-warning border border-warning-subtle extra-small ms-1">Seguridad</span></div></td>
                                <td><span class="font-mono text-muted">-rw-r-----</span></td>
                                <td><span class="badge bg-light text-dark border font-mono">root:adm</span></td>
                                <td class="font-mono">850 KB</td>
                                <td class="font-mono text-muted">2026-10-24 11:40:15</td>
                                <td class="text-end"><button class="btn btn-sm btn-dark font-mono rounded-2 px-3" onclick="openLogViewer('auth.log', '/var/log/auth.log')"><i class="fa-solid fa-terminal me-1 text-info"></i> Ver Contenido</button></td>
                            </tr>
                            <tr data-name="sysmonitor.log">
                                <td><div class="d-flex align-items-center gap-2"><i class="fa-solid fa-chart-line text-success fa-lg"></i><span class="fw-bold font-mono text-dark">sysmonitor.log</span><span class="badge bg-success-subtle text-success border border-success-subtle extra-small ms-1">App</span></div></td>
                                <td><span class="font-mono text-muted">-rw-rw-r--</span></td>
                                <td><span class="badge bg-light text-dark border font-mono">admin:admin</span></td>
                                <td class="font-mono">128 KB</td>
                                <td class="font-mono text-muted">2026-10-24 11:53:50</td>
                                <td class="text-end"><button class="btn btn-sm btn-dark font-mono rounded-2 px-3" onclick="openLogViewer('sysmonitor.log', '/var/log/sysmonitor.log')"><i class="fa-solid fa-terminal me-1 text-info"></i> Ver Contenido</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL VISOR DE LOGS (TERMINAL BASH) -->
<div class="modal fade" id="logViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content terminal-modal-content">
            <div class="terminal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="terminal-dots">
                        <div class="terminal-dot dot-red" data-bs-dismiss="modal" style="cursor:pointer;"></div>
                        <div class="terminal-dot dot-yellow"></div>
                        <div class="terminal-dot dot-green"></div>
                    </div>
                    <span class="font-mono text-white fw-semibold small" id="modalFilePath">
                        <i class="fa-solid fa-terminal text-info me-2"></i>/var/log/syslog
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary text-white font-mono extra-small">tail -n 50</span>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="px-3 py-2 bg-dark bg-opacity-50 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted extra-small font-mono">Filtrar Log:</span>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary btn-sm text-light active" onclick="filterLogEntries('ALL', this)">TODOS</button>
                        <button class="btn btn-outline-danger btn-sm text-light" onclick="filterLogEntries('ERROR', this)">ERROR</button>
                        <button class="btn btn-outline-warning btn-sm text-light" onclick="filterLogEntries('WARN', this)">WARN</button>
                        <button class="btn btn-outline-info btn-sm text-light" onclick="filterLogEntries('INFO', this)">INFO</button>
                    </div>
                </div>
                <span class="text-muted extra-small font-mono" id="logLineCount">Mostrando líneas</span>
            </div>

            <div class="terminal-body" id="terminalOutput"></div>

            <div class="p-3 bg-dark bg-opacity-75 border-top border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                <span class="text-muted extra-small font-mono"><i class="fa-solid fa-lock text-info me-1"></i> Modo Solo Lectura</span>
                <button class="btn btn-secondary btn-sm rounded-2 font-mono" data-bs-dismiss="modal">Cerrar Terminal</button>
            </div>
        </div>
    </div>
</div>

<script>
    const logDatabase = {
        'syslog': [
            { time: '11:53:02', level: 'INFO', msg: 'systemd[1]: Started SysMonitor Agent Service.' },
            { time: '11:52:45', level: 'SUCCESS', msg: 'kernel: ext4 filesystem mounted /dev/sda1 (rw,relatime).' },
            { time: '11:48:12', level: 'ERROR', msg: 'disk-monitor: Partition /dev/nvme0n1p2 usage exceeds threshold (93%).' },
            { time: '11:42:19', level: 'WARN', msg: 'kernel: High I/O wait detected on block device /dev/sdb1' }
        ],
        'auth.log': [
            { time: '11:40:15', level: 'SUCCESS', msg: 'pam_unix(sshd:session): session opened for user admin' },
            { time: '11:35:10', level: 'ERROR', msg: 'sshd: Failed password for invalid user guest from 185.220.101.4' }
        ],
        'sysmonitor.log': [
            { time: '11:53:50', level: 'INFO', msg: '[MODULE 5] Storage and Users metrics fetched successfully.' }
        ]
    };

    let currentActiveLogKey = 'syslog';

    function filterUsers() {
        const query = document.getElementById('userSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#usersTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    function filterUserType(type, btnElement) {
        document.querySelectorAll('.filter-user-btn').forEach(b => b.classList.remove('active', 'btn-dark'));
        btnElement.classList.add('active', 'btn-dark');

        const rows = document.querySelectorAll('#usersTable tbody tr');
        rows.forEach(row => {
            if (type === 'all') row.style.display = '';
            else row.style.display = (row.getAttribute('data-type') === type) ? '' : 'none';
        });
    }

    function filterFiles() {
        const query = document.getElementById('fileSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#fileExplorerTable tbody tr');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(query) ? '' : 'none';
        });
    }

    function openLogViewer(logKey, fullPath) {
        currentActiveLogKey = logKey;
        document.getElementById('modalFilePath').innerHTML = `<i class="fa-solid fa-terminal text-info me-2"></i>${fullPath}`;
        renderLogOutput('ALL');
        const modal = new bootstrap.Modal(document.getElementById('logViewerModal'));
        modal.show();
    }

    function renderLogOutput(filterLevel) {
        const outputContainer = document.getElementById('terminalOutput');
        const logEntries = logDatabase[currentActiveLogKey] || [];
        let html = '';
        let count = 0;

        logEntries.forEach(entry => {
            if (filterLevel !== 'ALL' && entry.level !== filterLevel) return;
            count++;
            let levelClass = 'log-level-info';
            if (entry.level === 'WARN') levelClass = 'log-level-warn';
            if (entry.level === 'ERROR') levelClass = 'log-level-error';
            if (entry.level === 'SUCCESS') levelClass = 'log-level-success';

            html += `<div class="terminal-line"><span class="log-time">[${entry.time}]</span><span class="${levelClass}">[${entry.level}]</span><span class="log-msg">${entry.msg}</span></div>`;
        });

        outputContainer.innerHTML = html || '<div class="text-muted p-2">Sin registros para el filtro seleccionado.</div>';
        document.getElementById('logLineCount').textContent = `Mostrando ${count} líneas`;
    }

    function filterLogEntries(level, btnElement) {
        btnElement.parentElement.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
        btnElement.classList.add('active');
        renderLogOutput(level);
    }
</script>

@endsection
