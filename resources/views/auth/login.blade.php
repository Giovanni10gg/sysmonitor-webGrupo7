<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysMonitor Web - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center vh-100">
    <div class="card p-4 shadow-lg border-0" style="width: 100%; max-width: 400px; border-radius: 12px;">
        <div class="text-center mb-4">
            <h4 class="fw-bold">SysMonitor Web</h4>
            <p class="text-muted small">Módulo 6: Autenticación y Bitácora</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small p-2 mb-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required placeholder="admin@sysmonitor.net">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Contraseña</label>
                <input type="password" name="password" class="form-control" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Iniciar Sesión</button>
        </form>

        <div class="mt-4 alert alert-secondary small p-2 mb-0">
            <strong>Credenciales de Prueba:</strong><br>
            • Admin: <code>admin@sysmonitor.net</code> / <code>Admin123!</code><br>
            • Observador: <code>observador@sysmonitor.net</code> / <code>Observer123!</code>
        </div>
    </div>
</body>
</html>
