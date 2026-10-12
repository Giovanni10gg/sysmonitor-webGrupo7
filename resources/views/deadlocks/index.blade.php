
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
#seccion-solicitud {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #d5dbe3;
}

#proceso-solicitud {
    padding: 8px;
    border: 1px solid #bbb;
    border-radius: 5px;
}

#resultado-solicitud:not(:empty) {
    margin-top: 15px;
    padding: 12px;
    border-radius: 6px;
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
    <div id="matriz-necesidad"></div>
    <div id="resultado-banquero"></div>

<section id="seccion-solicitud" hidden>

    <h2>Evaluar solicitud de recursos</h2>

    <p>
        Seleccione un proceso e indique los recursos
        adicionales que desea solicitar.
    </p>

    <form id="formulario-solicitud">

        <div class="configuracion">
            <label>
                Proceso
                <select id="proceso-solicitud" required></select>
            </label>
        </div>

        <div id="recursos-solicitados" class="disponible"></div>

        <button type="submit">
            Evaluar solicitud
        </button>

    </form>

    <div id="resultado-solicitud"
         role="status"
         aria-live="polite"></div>

    <p>
        Nota: la asignación de recursos es simulada.
        No modifica permanentemente las matrices.
    </p>

</section>

</main>

<script>
    const formulario = document.getElementById('formulario');
    const contenedor = document.getElementById('matrices');
    const resultado = document.getElementById('resultado');
    const contenedorNecesidad =
    document.getElementById('matriz-necesidad');
    const contenedorBanquero =
    document.getElementById('resultado-banquero');
const seccionSolicitud =
    document.getElementById('seccion-solicitud');

const formularioSolicitud =
    document.getElementById('formulario-solicitud');

const procesoSolicitud =
    document.getElementById('proceso-solicitud');

const recursosSolicitados =
    document.getElementById('recursos-solicitados');

const resultadoSolicitud =
    document.getElementById('resultado-solicitud');

let matricesValidadas = null;

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
limpiarSolicitud();
contenedorNecesidad.replaceChildren();
contenedorBanquero.replaceChildren();

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

function mostrarMatrizNecesidad(need) {
    contenedorNecesidad.replaceChildren();

    const titulo = document.createElement('h2');
    titulo.textContent = 'Matriz Necesidad Calculada';

    const contenedorTabla = document.createElement('div');
    contenedorTabla.className = 'tabla';

    const tabla = document.createElement('table');
    const encabezado = document.createElement('tr');

    const columnas = [
        'Proceso',
        ...need[0].map((_, j) => `R${j}`)
    ];

    columnas.forEach(nombre => {
        const th = document.createElement('th');
        th.textContent = nombre;
        encabezado.appendChild(th);
    });

    const thead = document.createElement('thead');
    thead.appendChild(encabezado);
    tabla.appendChild(thead);

    const tbody = document.createElement('tbody');

    need.forEach((fila, i) => {
        const tr = document.createElement('tr');

        const proceso = document.createElement('th');
        proceso.textContent = `P${i}`;
        tr.appendChild(proceso);

        fila.forEach(valor => {
            const td = document.createElement('td');
            td.textContent = valor;
            tr.appendChild(td);
        });

        tbody.appendChild(tr);
    });

    tabla.appendChild(tbody);
    contenedorTabla.appendChild(tabla);

    contenedorNecesidad.appendChild(titulo);
    contenedorNecesidad.appendChild(contenedorTabla);
}

function mostrarResultadoBanquero(datos) {

    // Limpiar resultados anteriores
    contenedorBanquero.replaceChildren();

    // Mostrar estado del sistema
    const estado = document.createElement('h2');

    if (datos.safe) {
        estado.textContent = 'Estado seguro';
        estado.className = 'ok';
    } else {
        estado.textContent = 'Estado inseguro';
        estado.className = 'error';
    }

    contenedorBanquero.appendChild(estado);

    // Explicar el resultado
    const explicacion = document.createElement('p');

    explicacion.textContent = datos.safe
        ? 'Todos los procesos pueden finalizar en un orden seguro.'
        : 'No existe una secuencia segura completa. Esto no significa necesariamente que ya exista un interbloqueo.';

    contenedorBanquero.appendChild(explicacion);

    // Mostrar secuencia de procesos
    const secuencia = document.createElement('p');

    const tituloSecuencia = datos.safe
        ? 'Secuencia segura: '
        : 'Procesos que pudieron finalizar: ';

    secuencia.textContent = tituloSecuencia +
        (datos.sequence.length > 0
            ? datos.sequence.join(' → ')
            : 'Ninguno');

    contenedorBanquero.appendChild(secuencia);

    // Titulo de los pasos
    const titulo = document.createElement('h2');
    titulo.textContent = 'Ejecución paso a paso';

    contenedorBanquero.appendChild(titulo);

    // Crear tabla
    const tabla = document.createElement('table');
    const thead = document.createElement('thead');
    const filaEncabezado = document.createElement('tr');

    const columnas = [
        'Paso',
        'Proceso',
        'Disponible antes',
        'Necesidad',
        'Recursos liberados',
        'Disponible después'
    ];

    columnas.forEach(nombre => {
        const th = document.createElement('th');
        th.textContent = nombre;
        filaEncabezado.appendChild(th);
    });

    thead.appendChild(filaEncabezado);
    tabla.appendChild(thead);

    const tbody = document.createElement('tbody');

    // Fila de recursos iniciales
    const inicial = document.createElement('tr');

    const valoresIniciales = [
        'Inicial',
        '—',
        JSON.stringify(
            datos.steps.length > 0
                ? datos.steps[0].work_before
                : datos.work_final
        ),
        '—',
        '—',
        '—'
    ];

    valoresIniciales.forEach(valor => {
        const td = document.createElement('td');
        td.textContent = valor;
        inicial.appendChild(td);
    });

    tbody.appendChild(inicial);

    // Registrar cada proceso que finaliza
    datos.steps.forEach((paso, indice) => {

        const fila = document.createElement('tr');

        const valores = [
            indice + 1,
            paso.process,
            JSON.stringify(paso.work_before),
            JSON.stringify(paso.need),
            JSON.stringify(paso.released),
            JSON.stringify(paso.work_after)
        ];

        valores.forEach(valor => {
            const td = document.createElement('td');
            td.textContent = valor;
            fila.appendChild(td);
        });

        tbody.appendChild(fila);
    });

    tabla.appendChild(tbody);

    const contenedorTabla = document.createElement('div');
    contenedorTabla.className = 'tabla';
    contenedorTabla.appendChild(tabla);

    contenedorBanquero.appendChild(contenedorTabla);

    // Mostrar procesos pendientes
    if (datos.pending.length > 0) {

        const pendientes = document.createElement('p');

        pendientes.textContent =
            'Procesos pendientes: ' +
            datos.pending.join(', ');

        contenedorBanquero.appendChild(pendientes);
    }
}


function limpiarSolicitud() {

    matricesValidadas = null;
    seccionSolicitud.hidden = true;

    resultadoSolicitud.textContent = '';
    resultadoSolicitud.className = '';
}

function prepararFormularioSolicitud(procesos, recursos) {

    procesoSolicitud.replaceChildren();
    recursosSolicitados.replaceChildren();

    // Crear las opciones P0, P1, P2...
    for (let i = 0; i < procesos; i++) {

        const opcion = document.createElement('option');

        opcion.value = i;
        opcion.textContent = `P${i}`;

        procesoSolicitud.appendChild(opcion);
    }

    // Crear una entrada por cada tipo de recurso
    for (let j = 0; j < recursos; j++) {

        const etiqueta = document.createElement('label');
        etiqueta.textContent = `Recurso R${j}`;

        const entrada = document.createElement('input');

        entrada.type = 'number';
        entrada.name = `requested[${j}]`;
        entrada.min = '0';
        entrada.step = '1';
        entrada.value = '0';
        entrada.required = true;

        etiqueta.appendChild(entrada);
        recursosSolicitados.appendChild(etiqueta);
    }

    resultadoSolicitud.textContent = '';
    resultadoSolicitud.className = '';

    seccionSolicitud.hidden = false;
}

    document.getElementById('generar')
        .addEventListener('click', generarMatrices);
formulario.addEventListener('input', function() {

    limpiarSolicitud();

    contenedorNecesidad.replaceChildren();
    contenedorBanquero.replaceChildren();

    resultado.textContent = '';
    resultado.className = '';
});

    formulario.addEventListener('submit', async function(evento) {
        evento.preventDefault();
limpiarSolicitud();
        contenedorBanquero.replaceChildren();
contenedorNecesidad.replaceChildren();
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
                   mostrarMatrizNecesidad(datos.need);
                   mostrarResultadoBanquero(datos);
matricesValidadas = {
    allocation,
    maximum,
    available
};

prepararFormularioSolicitud(datos.processes, datos.resources);
            }
	     else {
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
            contenedorNecesidad.replaceChildren();
        }
    });

formularioSolicitud.addEventListener('submit', async function(evento) {

    evento.preventDefault();

    if (!matricesValidadas) {
        return;
    }

    if (!formularioSolicitud.reportValidity()) {
        return;
    }

    // Obtener proceso seleccionado
    const process = Number(procesoSolicitud.value);

    // Obtener recursos solicitados
    const requested = Array.from(
        recursosSolicitados.querySelectorAll('input'),
        entrada => Number(entrada.value)
    );

    // Recuperar matrices validadas
    const { allocation, maximum, available } = matricesValidadas;

    const boton = formularioSolicitud.querySelector(
        'button[type="submit"]'
    );

    boton.disabled = true;

    resultadoSolicitud.className = '';
    resultadoSolicitud.textContent = 'Evaluando solicitud...';

    try {

        const respuesta = await fetch(
            "{{ route('deadlocks.request') }}",
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
                    available,
                    process,
                    requested
                })
            }
        );

        const datos = await respuesta.json();

        if (respuesta.ok) {

            resultadoSolicitud.className =
                datos.granted ? 'ok' : 'error';

            resultadoSolicitud.textContent = datos.reason;

            if (datos.granted) {

                resultadoSolicitud.textContent +=
                    ' Disponible después de la asignación simulada: [' +
                    datos.available_after.join(', ') + '].';

                resultadoSolicitud.textContent +=
                    ' Secuencia segura: ' +
                    datos.sequence.join(' → ') + '.';
            }

        } else {

            resultadoSolicitud.className = 'error';

            resultadoSolicitud.textContent = datos.errors
                ? Object.values(datos.errors).flat().join(' | ')
                : datos.message || 'No se pudo evaluar la solicitud.';
        }

    } catch (error) {

        resultadoSolicitud.className = 'error';

        resultadoSolicitud.textContent =
            'No fue posible comunicarse con Laravel.';

    } finally {

        boton.disabled = false;
    }
});

    generarMatrices();
    contenedorBanquero.replaceChildren();
</script>
</body>
</html>
