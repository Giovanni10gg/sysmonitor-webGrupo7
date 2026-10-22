<!DOCTYPE html>
<html>
<head>
    <title>Resultado FCFS</title>
</head>
<body>

<h2>Resultado {{ $algoritmo }}</h2>

<table border="1">
    <tr>
        <th>Proceso</th>
        <th>Espera</th>
        <th>Retorno</th>
    </tr>

    @foreach($procesos as $proceso)
    <tr>
        <td>{{ $proceso['nombre'] }}</td>
        <td>{{ $proceso['espera'] }}</td>
        <td>{{ $proceso['retorno'] }}</td>
    </tr>
    @endforeach

</table>
<hr>

<h3>Promedios</h3>

<p>
    <strong>Promedio de espera:</strong>
    {{ number_format($promedioEspera, 2) }}
</p>

<p>
    <strong>Promedio de retorno:</strong>
    {{ number_format($promedioRetorno, 2) }}
</p>

<div class="mt-3">
    <a href="{{ route('scheduler.index') }}" class="btn btn-primary">
        Volver al formulario
    </a>

</div>
</div>
</body>
</html>
