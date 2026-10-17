<!DOCTYPE html>
<html>
<head>
    <title>Resultado FCFS</title>
</head>
<body>

<h1>Resultado FCFS</h1>

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

</body>
</html>
