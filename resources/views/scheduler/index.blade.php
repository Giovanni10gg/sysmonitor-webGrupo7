<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador FCFS - Planificación de CPU</title>
    <!-- Bootstrap CSS para los estilos de la tabla y botones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h2 class="mb-3">Módulo 3 - Planificación de CPU</h2>
        <h4 class="text-muted mb-4">Simulador FCFS</h4>

        <form action="/scheduler/fcfs" method="POST">
            @csrf

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Proceso</th>
                        <th>Tiempo de Llegada</th>
                        <th>Ráfaga CPU</th>
                        <th>Prioridad</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Proceso 1 -->
                    <tr>
                        <td><strong>P1</strong></td>
                        <td><input type="number" name="processes[0][arrival_time]" class="form-control" value="0" min="0" required></td>
                        <td><input type="number" name="processes[0][burst_time]" class="form-control" value="4" min="1" required></td>
                        <td><input type="number" name="processes[0][priority]" class="form-control" value="1"></td>
                    </tr>
                    <!-- Proceso 2 -->
                    <tr>
                        <td><strong>P2</strong></td>
                        <td><input type="number" name="processes[1][arrival_time]" class="form-control" value="1" min="0" required></td>
                        <td><input type="number" name="processes[1][burst_time]" class="form-control" value="3" min="1" required></td>
                        <td><input type="number" name="processes[1][priority]" class="form-control" value="2"></td>
                    </tr>
                    <!-- Proceso 3 -->
                    <tr>
                        <td><strong>P3</strong></td>
                        <td><input type="number" name="processes[2][arrival_time]" class="form-control" value="2" min="0" required></td>
                        <td><input type="number" name="processes[2][burst_time]" class="form-control" value="5" min="1" required></td>
                        <td><input type="number" name="processes[2][priority]" class="form-control" value="3"></td>
                    </tr>
                </tbody>
            </table>
	<div class="mt-3">
 
<button type="submit"
formaction="{{ route('scheduler.fcfs') }}"
class="btn btn-primary">
Simular FCFS
</button>
 
<button type="submit"
formaction="{{ route('scheduler.sjf') }}"
class="btn btn-secondary">
Simular SJF
</button>
 
</div>
 
</form>
    <button type="submit"
            formaction="{{ sdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap JS (Opcional, por si requieres componentes interactivos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
