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
        <th>Llegada</th>
        <th>Ráfaga</th>
        <th>Prioridad</th>
        <th>Espera</th>
        <th>Retorno</th>
    </tr>

    @foreach($procesos as $proceso)
    <tr>
         <td>{{ $proceso['nombre'] }}</td>
         <td>{{ $proceso['llegada'] ?? '-' }}</td>
         <td>{{ $proceso['rafaga'] }}</td>
         <td>{{ $proceso['prioridad'] ?? '-' }}</td>
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

@if(isset($gantt) && count($gantt) > 0)

<h3>Diagrama de Gantt</h3>

<div style="display:flex;">

@foreach($gantt as $bloque)

<div style="
border:1px solid black;
padding:15px;
min-width:80px;
text-align:center;
">

{{ $bloque['proceso'] }}

</div>

@endforeach

</div>

<div style="display:flex;">

@foreach($gantt as $bloque)

<div style="min-width:80px;">
{{ $bloque['inicio'] }}
</div>

@endforeach

<div>
{{ end($gantt)['fin'] }}
</div>

</div>

@endif

<div class="mt-3">
    <a href="{{ route('scheduler.index') }}" class="btn btn-primary">
        Volver al formulario
    </a>

</div>
</div>
</body>
</html>
