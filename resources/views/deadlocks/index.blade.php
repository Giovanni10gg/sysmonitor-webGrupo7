
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

#seccion-grafo {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #d5dbe3;
}

.grafo-scroll {
    overflow-x: auto;
    padding: 10px;
    background: #f8fafc;
    border: 1px solid #d5dbe3;
    border-radius: 8px;
}

.grafo-scroll svg {
    display: block;
    max-width: none;
}

#seccion-ciclos {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #d5dbe3;
}

#resultado-ciclos:not(:empty) {
    margin-top: 15px;
    padding: 12px;
    border-radius: 6px;
}
#seccion-recuperacion {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #d5dbe3;
}

#resultado-recuperacion {
    line-height: 1.6;
}

#resultado-recuperacion li {
    margin-bottom: 10px;
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
<section id="seccion-grafo" hidden>

    <h2>Grafo de asignación de recursos</h2>

    <p id="descripcion-grafo"></p>

    <div id="contenedor-grafo" class="grafo-scroll"></div>

    <p>
        Flechas azules: recurso asignado a un proceso.
        Flechas naranjas discontinuas: recurso solicitado.
        Los números indican las cantidades.
    </p>

</section>

<section id="seccion-ciclos" hidden>

    <h2>Detección de ciclos</h2>

    <p>
        Ingrese los recursos que cada proceso tiene
        pendientes de recibir.
    </p>

    <p>
        Las solicitudes no modifican las asignaciones
        actuales del sistema.
    </p>

    <form id="formulario-ciclos">

        <div id="matriz-solicitudes-ciclos"></div>

        <button type="submit">
            Detectar ciclos
        </button>

    </form>

    <div id="resultado-ciclos"
         role="status"
         aria-live="polite"></div>

</section>


<section id="seccion-recuperacion" hidden>

    <h2>Recuperación ante interbloqueos</h2>

    <p>
        Análisis de posibles acciones para liberar recursos
        y permitir que los procesos continúen.
    </p>

    <p>
        Esta recuperación es una simulación académica.
        No termina procesos reales del sistema operativo.
    </p>

    <div id="resultado-recuperacion"
         role="status"
         aria-live="polite"></div>

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
const seccionGrafo =
    document.getElementById('seccion-grafo');
const seccionCiclos =
    document.getElementById('seccion-ciclos');

const formularioCiclos =
    document.getElementById('formulario-ciclos');

const matrizSolicitudesCiclos =
    document.getElementById('matriz-solicitudes-ciclos');

const resultadoCiclos =
    document.getElementById('resultado-ciclos');
const seccionRecuperacion =
    document.getElementById('seccion-recuperacion');

const resultadoRecuperacion =
    document.getElementById('resultado-recuperacion');
const contenedorGrafo =
    document.getElementById('contenedor-grafo');

const descripcionGrafo =
    document.getElementById('descripcion-grafo');
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
limpiarCiclos();
limpiarGrafo();
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

function limpiarGrafo() {
    contenedorGrafo.replaceChildren();
    descripcionGrafo.textContent = '';
    seccionGrafo.hidden = true;
}

function mostrarGrafo(grafo, conSolicitud = false, ciclo = []) {

    limpiarGrafo();

    if (!grafo || !Array.isArray(grafo.nodes) ||
        !Array.isArray(grafo.edges)) {
        return;
    }

// Identificar nodos y conexiones del ciclo
const nodosDelCiclo = new Set(ciclo);
const aristasDelCiclo = new Set();

for (let i = 0; i < ciclo.length - 1; i++) {
    aristasDelCiclo.add(
        `${ciclo[i]}->${ciclo[i + 1]}`
    );
}
    const recursos = grafo.nodes.filter(
        nodo => nodo.type === 'resource'
    );

    const procesos = grafo.nodes.filter(
        nodo => nodo.type === 'process'
    );

    const ancho = Math.max(
        760,
        (Math.max(recursos.length, procesos.length) + 1) * 135
    );

    const alto = 330;
    const ns = 'http://www.w3.org/2000/svg';

    // Crear elementos SVG
    function crearElemento(nombre, atributos = {}) {

        const elemento = document.createElementNS(ns, nombre);

        Object.entries(atributos).forEach(([clave, valor]) => {
            elemento.setAttribute(clave, valor);
        });

        return elemento;
    }

    const svg = crearElemento('svg', {
        width: ancho,
        height: alto,
        viewBox: `0 0 ${ancho} ${alto}`,
        role: 'img',
        'aria-label': 'Grafo de procesos y recursos'
    });

    const titulo = crearElemento('title');
    titulo.textContent = 'Grafo de asignación de recursos';
    svg.appendChild(titulo);

    // Crear las puntas de las flechas
    const defs = crearElemento('defs');

    function crearMarcador(id, color) {

        const marcador = crearElemento('marker', {
            id: id,
            viewBox: '0 0 10 10',
            refX: 9,
            refY: 5,
            markerWidth: 7,
            markerHeight: 7,
            orient: 'auto'
        });

        const punta = crearElemento('path', {
            d: 'M 0 0 L 10 5 L 0 10 Z',
            fill: color
        });

        marcador.appendChild(punta);
        defs.appendChild(marcador);
    }

    crearMarcador('flecha-m4-asignacion', '#2563eb');
    crearMarcador('flecha-m4-solicitud', '#d97706');
    crearMarcador('flecha-m4-ciclo', '#dc2626');

    svg.appendChild(defs);

    // Calcular posiciones
    const posiciones = {};

    recursos.forEach((nodo, indice) => {
        posiciones[nodo.id] = {
            x: (indice + 1) * ancho / (recursos.length + 1),
            y: 80
        };
    });

    procesos.forEach((nodo, indice) => {
        posiciones[nodo.id] = {
            x: (indice + 1) * ancho / (procesos.length + 1),
            y: 260
        };
    });

    // Dibujar flechas antes de los nodos
    grafo.edges.forEach(arista => {

        const origen = posiciones[arista.from];
        const destino = posiciones[arista.to];

        if (!origen || !destino) {
            return;
        }

        const asignacion = arista.type === 'allocation';

const esAristaDelCiclo = aristasDelCiclo.has(
    `${arista.from}->${arista.to}`
);
       const desplazamiento = asignacion ? -9 : 9;

        const x1 = origen.x + desplazamiento;
        const x2 = destino.x + desplazamiento;

        const y1 = asignacion ? 108 : 231;
        const y2 = asignacion ? 231 : 108;

const color = esAristaDelCiclo
    ? '#dc2626'
    : (asignacion ? '#2563eb' : '#d97706');


const linea = crearElemento('line', {
    x1: x1,
    y1: y1,
    x2: x2,
    y2: y2,
    stroke: color,
    'stroke-width': esAristaDelCiclo ? 4 : 2.5,
    'marker-end': esAristaDelCiclo
        ? 'url(#flecha-m4-ciclo)'
        : (asignacion
            ? 'url(#flecha-m4-asignacion)'
            : 'url(#flecha-m4-solicitud)')
});


        if (!asignacion) {
            linea.setAttribute('stroke-dasharray', '7 5');
        }

        svg.appendChild(linea);

        // Mostrar la cantidad de recursos
        const cantidad = crearElemento('text', {
            x: (x1 + x2) / 2,
            y: (y1 + y2) / 2 + (asignacion ? -10 : 16),
            'text-anchor': 'middle',
            'font-size': 14,
            'font-weight': 'bold',
            fill: color,
            stroke: '#f8fafc',
            'stroke-width': 4,
            'paint-order': 'stroke'
        });

        cantidad.textContent = arista.amount;
        svg.appendChild(cantidad);
    });

    // Dibujar los recursos como rectangulos
    recursos.forEach(nodo => {

        const punto = posiciones[nodo.id];

        const rectangulo = crearElemento('rect', {
            x: punto.x - 60,
            y: punto.y - 25,
            width: 120,
            height: 50,
            rx: 7,
            fill: '#dbeafe',
stroke: nodosDelCiclo.has(nodo.id)
    ? '#dc2626' : '#2563eb',
'stroke-width': nodosDelCiclo.has(nodo.id) ? 4 : 2
        });

        svg.appendChild(rectangulo);

       const nombre = crearElemento('text', {
            x: punto.x,
            y: punto.y - 4,
            'text-anchor': 'middle',
            'font-size': 16,
            'font-weight': 'bold',
            fill: '#1e40af'
        });

        nombre.textContent = nodo.label;
        svg.appendChild(nombre);

        const informacion = crearElemento('text', {
            x: punto.x,
            y: punto.y + 14,
            'text-anchor': 'middle',
            'font-size': 11,
            fill: '#1e40af'
        });

        informacion.textContent =
            `${nodo.available} libres / ${nodo.total} total`;

        svg.appendChild(informacion);
    });

    // Dibujar los procesos como circulos
    procesos.forEach(nodo => {

        const punto = posiciones[nodo.id];

        const circulo = crearElemento('circle', {
            cx: punto.x,
            cy: punto.y,
            r: 27,
            fill: '#dcfce7',
            stroke: nodosDelCiclo.has(nodo.id)
    ? '#dc2626' : '#16a34a',
'stroke-width': nodosDelCiclo.has(nodo.id) ? 4 : 2

        });

        svg.appendChild(circulo);

        const nombre = crearElemento('text', {
            x: punto.x,
            y: punto.y + 5,
            'text-anchor': 'middle',
            'font-size': 16,
            'font-weight': 'bold',
            fill: '#166534'
        });

        nombre.textContent = nodo.label;
        svg.appendChild(nombre);
    });

    contenedorGrafo.appendChild(svg);

if (ciclo.length > 0) {

    descripcionGrafo.textContent =
        'Se detectó un ciclo. Las conexiones y los nodos ' +
        'resaltados en rojo forman parte del ciclo encontrado.';

} else if (conSolicitud) {

    descripcionGrafo.textContent =
        'Grafo con solicitudes simuladas. Las flechas de solicitud ' +
        'no representan recursos que ya fueron concedidos.';

} else {

    descripcionGrafo.textContent =
        'Grafo del estado actual de las matrices validadas.';
}



    seccionGrafo.hidden = false;
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


function limpiarRecuperacion() {

    seccionRecuperacion.hidden = true;
    resultadoRecuperacion.replaceChildren();
}

function mostrarRecuperacion(recuperacion) {

    limpiarRecuperacion();

    if (!recuperacion) {
        return;
    }

    seccionRecuperacion.hidden = false;

    const mensaje = document.createElement('p');

    // Caso 1: No es necesario recuperar
    if (!recuperacion.recovery_needed) {

        mensaje.className = 'ok';
        mensaje.textContent =
            'No se requiere recuperación mediante terminación ' +
            'de procesos en este escenario.';

        resultadoRecuperacion.appendChild(mensaje);
        return;
    }

    // Mostrar procesos pendientes
    const pendientes = document.createElement('p');

    pendientes.textContent =
        'Procesos pendientes antes de recuperar: ' +
        recuperacion.pending_before.join(', ');

    resultadoRecuperacion.appendChild(pendientes);

    // Caso 2: No existe una terminacion individual suficiente
    if (!recuperacion.recommended) {

        mensaje.className = 'error';

        mensaje.textContent =
            'No se encontró una terminación individual que ' +
            'permita finalizar a todos los demás procesos. ' +
            'Podrían requerirse varias acciones de recuperación.';

        resultadoRecuperacion.appendChild(mensaje);

        return;
    }

    // Caso 3: Existe una recomendacion
    const recomendacion = recuperacion.recommended;

    const titulo = document.createElement('h3');

    titulo.textContent =
        'Recomendación: terminar ' +
        recomendacion.process +
        ' (simulado)';

    resultadoRecuperacion.appendChild(titulo);

    const recursos = document.createElement('p');

    recursos.textContent =
        'Recursos liberados inicialmente: [' +
        recomendacion.released.join(', ') +
        ']';

    resultadoRecuperacion.appendChild(recursos);

    // Mostrar los pasos de recuperacion
    const tituloPasos = document.createElement('h3');
    tituloPasos.textContent = 'Recuperación paso a paso';

    resultadoRecuperacion.appendChild(tituloPasos);

    const lista = document.createElement('ol');

    recomendacion.steps.forEach(paso => {

        const elemento = document.createElement('li');

        if (paso.action === 'abort') {

            elemento.textContent =
                'Terminar ' + paso.process +
                ' de forma simulada. Libera [' +
                paso.released.join(', ') +
                ']. Disponible: [' +
                paso.available_after.join(', ') + '].';

        } else if (paso.action === 'finish') {

            elemento.textContent =
                paso.process +
                ' puede continuar y finalizar. Libera [' +
                paso.released.join(', ') +
                ']. Disponible: [' +
                paso.available_after.join(', ') + '].';
        }

        lista.appendChild(elemento);
    });

    resultadoRecuperacion.appendChild(lista);

    // Comprobar el resultado final
    const resultadoFinal = document.createElement('p');

    if (recomendacion.resolved) {

        resultadoFinal.className = 'ok';

        resultadoFinal.textContent =
            'Recuperación simulada exitosa. ' +
            'No quedan procesos pendientes. ' +
            'Recursos disponibles al finalizar: [' +
            recomendacion.available_after.join(', ') + '].';

    } else {

        resultadoFinal.className = 'error';

        resultadoFinal.textContent =
            'Todavía quedan procesos pendientes: ' +
            recomendacion.pending_after.join(', ');
    }

    resultadoRecuperacion.appendChild(resultadoFinal);

    // Mostrar otras alternativas evaluadas
    if (recuperacion.options.length > 1) {

        const alternativas = document.createElement('p');

        alternativas.textContent =
            'Alternativas de terminación individual evaluadas: ' +
            recuperacion.options.map(opcion =>
                opcion.process +
                (opcion.resolved
                    ? ' (resuelve)'
                    : ' (insuficiente)')
            ).join(', ');

        resultadoRecuperacion.appendChild(alternativas);
    }
}

function limpiarCiclos() {
limpiarRecuperacion();

    seccionCiclos.hidden = true;

    matrizSolicitudesCiclos.replaceChildren();

    resultadoCiclos.textContent = '';
    resultadoCiclos.className = '';
}

function prepararFormularioCiclos(procesos, recursos) {

    let html = '<div class="tabla">';
    html += '<table><thead><tr><th>Proceso</th>';

    // Encabezados R0, R1, R2...
    for (let j = 0; j < recursos; j++) {
        html += `<th>R${j}</th>`;
    }

    html += '</tr></thead><tbody>';

    // Crear una fila por proceso
    for (let i = 0; i < procesos; i++) {

        html += `<tr><th>P${i}</th>`;

        for (let j = 0; j < recursos; j++) {

            html += `<td>
                <input
                    type="number"
                    name="cycle_request[${i}][${j}]"
                    min="0"
                    step="1"
                    value="0"
                    required
                >
            </td>`;
        }

        html += '</tr>';
    }

    html += '</tbody></table></div>';

    matrizSolicitudesCiclos.innerHTML = html;

    resultadoCiclos.textContent = '';
    resultadoCiclos.className = '';

    seccionCiclos.hidden = false;
}

    document.getElementById('generar')
        .addEventListener('click', generarMatrices);
formulario.addEventListener('input', function() {

    limpiarCiclos();
    limpiarGrafo();
    limpiarSolicitud();

    contenedorNecesidad.replaceChildren();
    contenedorBanquero.replaceChildren();

    resultado.textContent = '';
    resultado.className = '';
});

    formulario.addEventListener('submit', async function(evento) {
        evento.preventDefault();
limpiarCiclos();
limpiarGrafo();
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
	           mostrarGrafo(datos.graph);
matricesValidadas = {
    allocation,
    maximum,
    available
};

prepararFormularioSolicitud(datos.processes, datos.resources);
prepararFormularioCiclos(datos.processes, datos.resources);
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
limpiarRecuperacion();

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
	        mostrarGrafo(datos.graph, true);
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


formularioCiclos.addEventListener('submit', async function(evento) {

    evento.preventDefault();
limpiarRecuperacion();

    if (!matricesValidadas) {
        return;
    }

    if (!formularioCiclos.reportValidity()) {
        return;
    }

    const { allocation, maximum, available } = matricesValidadas;

    const processes = allocation.length;
    const resources = available.length;

    // Construir la matriz de solicitudes pendientes
    const requests = [];

    for (let i = 0; i < processes; i++) {

        const fila = [];

        for (let j = 0; j < resources; j++) {

            const entrada = formularioCiclos.elements.namedItem(
                `cycle_request[${i}][${j}]`
            );

            fila.push(Number(entrada.value));
        }

        requests.push(fila);
    }

    const boton = formularioCiclos.querySelector(
        'button[type="submit"]'
    );

    boton.disabled = true;

    resultadoCiclos.className = '';
    resultadoCiclos.textContent = 'Analizando el grafo...';

    try {

        const respuesta = await fetch(
            "{{ route('deadlocks.cycles') }}",
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
                    requests
                })
            }
        );

        const datos = await respuesta.json();

        if (respuesta.ok) {

            if (datos.has_cycle) {

                resultadoCiclos.className = 'error';

                resultadoCiclos.textContent =
                    datos.message +
                    ' Ciclo encontrado: ' +
                    datos.cycle.join(' → ');

            } else {

                resultadoCiclos.className = 'ok';

                resultadoCiclos.textContent =
                    datos.message;
            }

            // Mostrar el grafo con todas las solicitudes
mostrarGrafo(datos.graph, true, datos.cycle);
mostrarRecuperacion(datos.recovery);
        } else {

            resultadoCiclos.className = 'error';

            resultadoCiclos.textContent = datos.errors
                ? Object.values(datos.errors).flat().join(' | ')
                : datos.message || 'Error al analizar ciclos.';
        }

    } catch (error) {

        resultadoCiclos.className = 'error';

        resultadoCiclos.textContent =
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
