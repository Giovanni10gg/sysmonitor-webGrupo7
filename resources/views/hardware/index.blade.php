<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5"> <!-- Autorrefresco cada 5 segundos -->
    <title>Módulo 2: CPU y Memoria - SysMonitor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f6f9; color: #333; }
        .nav { margin-bottom: 20px; }
        .nav a { text-decoration: none; color: #007bff; font-weight: bold; margin-right: 15px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .progress-bar-bg { background: #e9ecef; border-radius: 4px; height: 20px; width: 100%; margin: 10px 0; overflow: hidden; }
        .progress-bar-fill { height: 100%; background: #28a745; transition: width 0.3s; text-align: center; color: white; font-size: 12px; line-height: 20px; }
        .progress-warning { background: #ffc107; color: #000; }
        .progress-danger { background: #dc3545; }
        .metric-label { font-weight: bold; }
    </style>
</head>
<body>

    <div class="nav">
        <a href="{{ route('processes.index') }}">← Ir a Módulo 1 (Procesos)</a>
        <a href="{{ route('hardware.index') }}">🔄 Recargar Métricas</a>
    </div>

    <h1>SysMonitor - Módulo 2: CPU y Memoria</h1>
    <p><i>La página se actualiza automáticamente cada 5 segundos.</i></p>

    <div class="grid">
        <!-- Tarjeta de CPU -->
        <div class="card">
            <h3>Procesador (CPU)</h3>
            <p><span class="metric-label">Modelo:</span> {{ $cpuInfo['model'] }}</p>
            <p><span class="metric-label">Núcleos:</span> {{ $cpuInfo['cores'] }}</p>
            <p><span class="metric-label">Uptime:</span> {{ $cpuInfo['uptime'] }}</p>
            <hr>
            <p><span class="metric-label">Uso de CPU:</span> {{ $cpuUsage }}%</p>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill {{ $cpuUsage > 80 ? 'progress-danger' : ($cpuUsage > 50 ? 'progress-warning' : '') }}" 
                     style="width: {{ $cpuUsage }}%;">
                    {{ $cpuUsage }}%
                </div>
            </div>
        </div>

        <!-- Tarjeta de Memoria RAM -->
        <div class="card">
            <h3>Memoria RAM</h3>
            <p><span class="metric-label">Total:</span> {{ $memoryInfo['ram']['total_mb'] }} MB</p>
            <p><span class="metric-label">En uso:</span> {{ $memoryInfo['ram']['used_mb'] }} MB ({{ $memoryInfo['ram']['used_pct'] }}%)</p>
            <p><span class="metric-label">Libre:</span> {{ $memoryInfo['ram']['free_mb'] }} MB</p>
            <p><span class="metric-label">En Caché:</span> {{ $memoryInfo['ram']['cached_mb'] }} MB</p>
            
            <div class="progress-bar-bg">
                <div class="progress-bar-fill {{ $memoryInfo['ram']['used_pct'] > 85 ? 'progress-danger' : ($memoryInfo['ram']['used_pct'] > 60 ? 'progress-warning' : '') }}" 
                     style="width: {{ $memoryInfo['ram']['used_pct'] }}%;">
                    {{ $memoryInfo['ram']['used_pct'] }}%
                </div>
            </div>
        </div>

        <!-- Tarjeta de Memoria SWAP y Carga -->
        <div class="card">
            <h3>Memoria SWAP y Carga</h3>
            <p><span class="metric-label">SWAP Total:</span> {{ $memoryInfo['swap']['total_mb'] }} MB</p>
            <p><span class="metric-label">SWAP Usada:</span> {{ $memoryInfo['swap']['used_mb'] }} MB ({{ $memoryInfo['swap']['used_pct'] }}%)</p>
            
            <div class="progress-bar-bg">
                <div class="progress-bar-fill {{ $memoryInfo['swap']['used_pct'] > 50 ? 'progress-danger' : '' }}" 
                     style="width: {{ $memoryInfo['swap']['used_pct'] }}%;">
                    {{ $memoryInfo['swap']['used_pct'] }}%
                </div>
            </div>

            <hr>
            <p><span class="metric-label">Promedio de Carga (1m, 5m, 15m):</span></p>
            <code>{{ $memoryInfo['load_avg'] }}</code>
        </div>
    </div>

</body>
</html>
