
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>M4 - Interbloqueos</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f5f9;
            color: #202938;
            margin: 0;
            padding: 30px;
        }

        main {
            max-width: 1050px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
        }

        h1 { color: #164e83; }

        .configuracion, .disponible {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: end;
            margin: 20px 0;
        }

        label {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        input {
            width: 75px;
            padding: 8px;
            border: 1px solid #bbb;
            border-radius: 5px;
        }

        button {
            background: #1766a3;
            color: white;
            padding: 10px 18px;
            border: 0;
            border-radius: 6px;
            cursor: pointer;
            margin: 8px 0;
        }

        button:hover { background: #114e7c; }

        .tabla { overflow-x: auto; }

        table {
            border-collapse: collapse;
            margin: 12px 0 25px;
            width: 100%;
        }

        th, td {
            border: 1px solid #d5dbe3;
            padding: 8px;
            text-align: center;
        }

        th { background: #e8f0fa; }

        td input { width: 55px; }

        #resultado {
            margin-top: 20px;
            padding: 12px;
            border-radius: 6px;
        }

        #resultado:empty { display: none; }

        .ok {
            background: #e4f5e8;
            color: #17622b;
        }

        .error {
            background: #ffe8e8;
            color: #9d2525;
        }
    </style>
</head>

<body>
<main>

    <h1>M4 - Simulador de Interbloqueos</h1>

    <p>
        Ingrese los procesos y recursos para analizar
        las matrices del sistema.
    </p>

    <form id="formulario">

        <div class="configuracion">
            <label>
                Número de procesos
                <input type="number" id="procesos"
                       min="1" max="10" value="2" required>
            </label>

            <label>
                Tipos de recursos
                <input type="number" id="recursos"
                       min="1" max="6" value="2" required>
            </label>

            <button type="button" id="generar">
                Generar matrices
            </button>
        </div>

        <div id="matrices"></div>

        <button type="submit">
            Validar matrices
        </button>

    </form>

    <div id="resultado" role="status" aria-live="polite"></div>

</main>

<script>
    const formulario = document.getElementById('formulario');
    const contenedor = document.getElementById('matrices');
    const resultado = document.getElementById('resultado');

    function crearTabla(titulo, nombre, procesos, recursos) {
        let html = `<h2>${titulo}</h2>`;
        html += '<div class="tabla"><table><thead><tr><th>Proceso</th>';

        for (let j = 0; j < recursos; j++) {
            html += `<th>R${j}</th>`;
        }

        html += '</tr></thead><tbody>';

        for (let i = 0; i < procesos; i++) {
            html += `<tr><th>P${i}</th>`;

            for (let j = 0; j < recursos; j++) {
                html += `<td>
                    <input type="number" min="0" step="1"
                    required value="0"
                    name="${nombre}[${i}][${j}]">
                </td>`;
            }

            html += '</tr>';
        }

        html += '</tbody></table></div>';
        return html;
    }

    function generarMatrices() {
        const n = Number(document.getElementById('procesos').value);
        const m = Number(document.getElementById('recursos').value);

        if (!Number.isInteger(n) || n < 1 || n > 10 ||
            !Number.isInteger(m) || m < 1 || m > 6) {
            resultado.className = 'error';
            resultado.textContent =
                'Ingrese entre 1 y 10 procesos y entre 1 y 6 recursos.';
            return;
        }

        let html = crearTabla('Matriz Asignación', 'allocation', n, m);
        html += crearTabla('Matriz Máximo', 'maximum', n, m);

        html += '<h2>Vector Disponible</h2>';
        html += '<div class="disponible">';

        for (let j = 0; j < m; j++) {
            html += `<label>R${j}
                <input type="number" min="0" step="1"
                required value="0" name="available[${j}]">
            </label>`;
        }

        html += '</div>';
        contenedor.innerHTML = html;

        resultado.textContent = '';
        resultado.className = '';
    }

    document.getElementById('generar')
        .addEventListener('click', generarMatrices);

    formulario.addEventListener('submit', async function(evento) {
        evento.preventDefault();

        if (!formulario.reportValidity()) return;

        const n = Number(document.getElementById('procesos').value);
        const m = Number(document.getElementById('recursos').value);

        const allocation = [];
        const maximum = [];
        const available = [];

        for (let i = 0; i < n; i++) {
            const filaAsignacion = [];
            const filaMaximo = [];

            for (let j = 0; j < m; j++) {
                filaAsignacion.push(Number(
                    formulario.elements.namedItem(
                        `allocation[${i}][${j}]`
                    ).value
                ));

                filaMaximo.push(Number(
                    formulario.elements.namedItem(
                        `maximum[${i}][${j}]`
                    ).value
                ));
            }

            allocation.push(filaAsignacion);
            maximum.push(filaMaximo);
        }

        for (let j = 0; j < m; j++) {
            available.push(Number(
                formulario.elements.namedItem(
                    `available[${j}]`
                ).value
            ));
        }

        try {
            const respuesta = await fetch(
                "{{ route('deadlocks.validate') }}",
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content
                    },
                    body: JSON.stringify({
                        allocation,
                        maximum,
                        available
                    })
                }
            );

            const datos = await respuesta.json();

            if (respuesta.ok) {
                resultado.className = 'ok';
                resultado.textContent =
                    `Matrices validadas correctamente. ` +
                    `Procesos: ${datos.processes}. ` +
                    `Recursos: ${datos.resources}.`;
            } else {
                resultado.className = 'error';

                const errores = datos.errors
                    ? Object.values(datos.errors).flat().join(' | ')
                    : datos.message || 'Error al validar las matrices.';

                resultado.textContent = errores;
            }

        } catch (error) {
            resultado.className = 'error';
            resultado.textContent =
                'No fue posible comunicarse con Laravel.';
        }
    });

    generarMatrices();
</script>
</body>
</html>
