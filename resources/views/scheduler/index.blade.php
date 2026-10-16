<!DOCTYPE html>
<html>
<head>
    <title>Planificación CPU</title>
</head>
<body>

<h1>Módulo 3 - Planificación de CPU</h1>

<h2>Simulador FCFS</h2>

<form>
    <table border="1">
        <tr>
            <th>Proceso</th>
            <th>Tiempo de Llegada</th>
            <th>Ráfaga CPU</th>
            <th>Prioridad</th>
        </tr>
        <tr>
            <td>P1</td>
            <td><input type="number" value="0"></td>
            <td><input type="number" value="5"></td>
            <td><input type="number" value="1"></td>
        </tr>
        <tr>
            <td>P2</td>
            <td><input type="number" value="1"></td>
            <td><input type="number" value="3"></td>
            <td><input type="number" value="2"></td>
        </tr>
        <tr>
            <td>P3</td>
            <td><input type="number" value="2"></td>
            <td><input type="number" value="4"></td>
            <td><input type="number" value="3"></td>
        </tr>
    </table>

    <br>

    <button type="submit">
        Simular FCFS
    </button>
</form>

</body>
</html>
