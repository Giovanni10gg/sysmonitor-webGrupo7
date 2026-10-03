<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Módulo 1: Gestión de Procesos - SysMonitor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f6f9; color: #333; }
        .nav { margin-bottom: 20px; }
        .nav a { text-decoration: none; color: #007bff; font-weight: bold; margin-right: 15px; }
        .card { background: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .alert-success { background: #d4edda; color: #155724; padding: 10px; margin-bottom: 10px; }
        .alert-danger { background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #343a40; color: white; }
        .badge-whitelisted { background: #28a745; color: white; padding: 3px 6px; border-radius: 3px; font-size: 12px; }
        .badge-system { background: #6c757d; color: white; padding: 3px 6px; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>

    <!-- Menú de navegación -->
    <div class="nav">
        <a href="{{ route('hardware.index') }}">→ Ir a Módulo 2 (CPU y Memoria)</a>
        <a href="{{ route('processes.index') }}">🔄 Recargar Procesos</a>
    </div>

    <h1>SysMonitor - Módulo 1: Procesos Linux</h1>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <h3>Lanzar Proceso de Prueba (Lista Blanca)</h3>
        <form action="{{ route('processes.spawn') }}" method="POST">
            @csrf
            <label>Duración (segundos):</label>
            <input type="number" name="seconds" value="300" min="10" required>
            <button type="submit">Iniciar Proceso (sleep)</button>
        </form>
    </div>

    <div class="card">
        <h3>Resumen de Estados</h3>
        <p>
            <strong>R (Running):</strong> {{ $summary['R'] }} |
            <strong>S (Sleeping):</strong> {{ $summary['S'] }} |
            <strong>D (Disk Sleep):</strong> {{ $summary['D'] }} |
            <strong>Z (Zombie):</strong> {{ $summary['Z'] }} |
            <strong>T (Stopped):</strong> {{ $summary['T'] }}
        </p>
    </div>

    <div class="card">
        <h3>Lista de Procesos del Sistema</h3>
        <table>
            <thead>
                <tr>
                    <th>PID</th>
                    <th>Usuario</th>
                    <th>Estado</th>
                    <th>Nice</th>
                    <th>CPU %</th>
                    <th>RAM %</th>
                    <th>Comando</th>
                    <th>Origen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($processes as $proc)
                <tr>
                    <td>{{ $proc['pid'] }}</td>
                    <td>{{ $proc['user'] }}</td>
                    <td>{{ $proc['state'] }}</td>
                    <td>{{ $proc['nice'] }}</td>
                    <td>{{ $proc['cpu'] }}%</td>
                    <td>{{ $proc['mem'] }}%</td>
                    <td><code>{{ $proc['cmd'] }}</code></td>
                    <td>
                        @if(in_array($proc['pid'], $whitelistedPids))
                            <span class="badge-whitelisted">App (Prueba)</span>
                        @else
                            <span class="badge-system">Sistema</span>
                        @endif
                    </td>
                    <td>
                        @if(in_array($proc['pid'], $whitelistedPids))
                            <form action="{{ route('processes.kill') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="pid" value="{{ $proc['pid'] }}">
                                <button type="submit" onclick="return confirm('¿Finalizar PID {{ $proc['pid'] }}?')">Kill</button>
                            </form>

                            <form action="{{ route('processes.renice') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="pid" value="{{ $proc['pid'] }}">
                                <input type="number" name="nice" min="-20" max="19" value="10" style="width:45px;">
                                <button type="submit">Renice</button>
                            </form>
                        @else
                            <span style="color: #888; font-size: 12px;">Protegido</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
