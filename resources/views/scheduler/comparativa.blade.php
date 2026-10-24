<!DOCTYPE html>
<html>
<head>
    <title>Tabla Comparativa</title>
</head>
<body>

<h1>Comparativa de Algoritmos</h1>

<table border="1" cellpadding="5">

    <tr>
        <th>Algoritmo</th>
        <th>Promedio Espera</th>
        <th>Promedio Retorno</th>
    </tr>

    @foreach($simulaciones as $simulacion)

    <tr>
        <td>{{ $simulacion->algoritmo }}</td>

        <td>
            {{ number_format($simulacion->promedio_espera, 2) }}
        </td>

        <td>
            {{ number_format($simulacion->promedio_retorno, 2) }}
        </td>

    </tr>

    @endforeach

</table>

<br><br>

<a href="{{ route('scheduler.index') }}">
    <button>
        Volver al simulador
    </button>
</a>

</body>
</html>
