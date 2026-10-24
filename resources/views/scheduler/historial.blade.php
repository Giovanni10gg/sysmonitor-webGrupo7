<!DOCTYPE html>
<html>
<head>
    <title>Historial de Simulaciones</title>
</head>
<body>

<h1>Historial de Simulaciones</h1>

<table border="1" cellpadding="5">

    <tr>
        <th>ID</th>
        <th>Algoritmo</th>
        <th>Promedio Espera</th>
        <th>Promedio Retorno</th>
        <th>Fecha</th>
    </tr>

    @foreach($simulaciones as $simulacion)

    <tr>
        <td>{{ $simulacion->id }}</td>
        <td>{{ $simulacion->algoritmo }}</td>
        <td>{{ number_format($simulacion->promedio_espera, 2) }}</td>
        <td>{{ number_format($simulacion->promedio_retorno, 2) }}</td>
        <td>{{ $simulacion->created_at }}</td>
    </tr>

    @endforeach

</table>

<br><br>

<a href="{{ route('scheduler.index') }}">
    <button type="button">
        Volver al Simulador
    </button>
</a>

</body>
</html>
