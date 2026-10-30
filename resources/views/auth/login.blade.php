<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SysMonitor Web - Iniciar Sesión</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .login-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem 2rem;
        }
        .form-control {
            background-color: #0f172a;
            border: 1px solid #334155;
            color: #ffffff;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }
        .form-control:focus {
            background-color: #0f172a;
            border-color: #3b82f6;
            color: #ffffff;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
        }
        .form-control::placeholder { color: #64748b; }
        .btn-primary-custom {
            background-color: #2563eb;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 8px;
            transition: background-color 0.2s;
        }
        .btn-primary-custom:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- Logo y Título -->
        <div class="text-center mb-4">
            <div class="bg-primary bg-opacity-20 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; background: rgba(59, 130, 246, 0.15);">
                <i class="fa-solid fa-server fa-xl text-primary"></i>
            </div>
            <h1 class="h4 fw-bold text-white mb-1">SysMonitor Web</h1>
            <p class="text-secondary small">Acceso al sistema de monitoreo</p>
        </div>

        <!-- Errores de Validación -->
        @if ($errors->any())
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger small rounded-3 mb-4">
                <i class="fa-solid fa-circle-exclamation me-1"></i> Credenciales incorrectas. Por favor, intente de nuevo.
            </div>
        @endif

        <!-- Formulario de Inicio de Sesión -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Campo Usuario / Email (Sin valor quemado) -->
            <div class="mb-3">
                <label for="email" class="form-label small text-secondary font-mono">Correo o Usuario</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary"><i class="fa-solid fa-user"></i></span>
                    <input type="text" id="email" name="email" class="form-control" placeholder="Ingrese su usuario o correo" value="{{ old('email') }}" required autofocus autocomplete="username">
                </div>
            </div>

            <!-- Campo Contraseña (Sin valor quemado) -->
            <div class="mb-4">
                <label for="password" class="form-label small text-secondary font-mono">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                </div>
            </div>

            <!-- Botón de Inicio de Sesión -->
            <button type="submit" class="btn btn-primary-custom w-100 text-white mb-3">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Iniciar Sesión
            </button>
        </form>

        <div class="text-center border-top border-secondary border-opacity-25 pt-3 mt-3">
            <span class="text-secondary extra-small font-mono" style="font-size: 0.75rem;">
                <i class="fa-solid fa-shield-halved text-info me-1"></i> SysMonitor Web v1.0 — Autenticación Segura
            </span>
        </div>
    </div>

</body>
</html>
