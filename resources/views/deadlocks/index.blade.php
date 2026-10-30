@extends('layouts.app')

@section('title', 'Módulo 4 - Interbloqueos (Deadlocks)')
@section('module_tag', 'Módulo 04')
@section('module_icon', 'fa-diagram-project')
@section('module_description', 'Simulador interactivo para prevención, detección y recuperación de interbloqueos mediante el Algoritmo del Banquero y Grafos de Asignación (RAG)')

@section('content')

<style>
    .tabla-m4 table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 1rem;
    }
    .tabla-m4 th, .tabla-m4 td {
        padding: 0.5rem;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }
    .tabla-m4 th {
        background-color: #f8fafc;
        font-weight: 600;
        font-size: 0.85rem;
        color: #334155;
    }
    .grafo-scroll {
        overflow-x: auto;
        padding: 1rem;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
    }
    .grafo-scroll svg {
        display: block;
        margin: 0 auto;
    }
</style>

<!-- CONFIGURACIÓN DE DIMENSIONES -->
<div class="row g-4 mb-4">
    <div class="col-12 col-xl-4">
        <div class="card custom-card h-100">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-sliders text-primary me-2"></i>Dimensión del Sistema
                </h6>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">Defina la cantidad de procesos y tipos de recursos para generar las matrices de trabajo.</p>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-dark">Número de Procesos</label>
                        <input type="number" id="procesos" class="form-control font-mono" min="1" max="10" value="2" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-dark">Tipos de Recursos</label>
                        <input type="number" id="recursos" class="form-control font-mono" min="1" max="6" value="2" required>
                    </div>
                </div>
                <button type="button" id="generar" class="btn btn-outline-primary w-100 mt-3 font-mono fw-semibold">
                    <i class="fa-solid fa-table-cells me-1"></i> Generar Matrices
                </button>
            </div>
        </div>
    </div>

    <!-- MATRICES DE ENTRADA Y DISPONIBLES -->
    <div class="col-12 col-xl-8">
        <div class="card custom-card h-100">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-matrix text-info me-2"></i>Entrada de Matrices y Vector Disponible
                </h6>
            </div>
            <div class="card-body p-4">
                <form id="formulario">
                    <div id="matrices" class="tabla-m4"></div>
                    
                    <button type="submit" class="btn btn-primary font-mono fw-semibold mt-2">
                        <i class="fa-solid fa-shield-halved me-1"></i> Validar Matrices y Ejecutar Banquero
                    </button>
                </form>

                <div id="resultado" class="mt-3" role="status" aria-live="polite"></div>
            </div>
        </div>
    </div>
</div>

<!-- RESULTADOS DEL ALGORITMO DEL BANQUERO -->
<div class="row g-4 mb-4">
    <div class="col-12 col-xl-6">
        <div id="matriz-necesidad" class="tabla-m4"></div>
    </div>
    <div class="col-12 col-xl-6">
        <div id="resultado-banquero"></div>
    </div>
</div>

<!-- EVALUAR SOLICITUD DE RECURSOS -->
<section id="seccion-solicitud" class="card custom-card mb-4" hidden>
    <div class="card-header bg-white border-bottom py-3 px-4">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-code-pull-request text-primary me-2"></i>Evaluar Solicitud de Recursos (Simulado)
        </h5>
    </div>
    <div class="card-body p-4">
        <p class="text-muted small mb-3">
            Seleccione un proceso e indique los recursos adicionales requeridos para verificar si la solicitud mantendría al sistema en un estado seguro.
        </p>

        <form id="formulario-solicitud">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold text-dark">Proceso Solicitante</label>
                    <select id="proceso-solicitud" class="form-select font-mono" required></select>
                </div>
            </div>

            <label class="form-label small fw-semibold text-dark mb-2">Recursos Solicitados</label>
            <div id="recursos-solicitados" class="d-flex flex-wrap gap-3 mb-3"></div>

            <button type="submit" class="btn btn-primary font-mono fw-semibold">
                <i class="fa-solid fa-paper-plane me-1"></i> Evaluar Solicitud
            </button>
        </form>

        <div id="resultado-solicitud" class="mt-3" role="status" aria-live="polite"></div>
    </div>
</section>

<!-- GRAFO DE ASIGNACIÓN DE RECURSOS (RAG) -->
<section id="seccion-grafo" class="card custom-card mb-4" hidden>
    <div class="card-header bg-white border-bottom py-3 px-4">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-diagram-project text-info me-2"></i>Grafo de Asignación de Recursos (RAG)
        </h5>
    </div>
    <div class="card-body p-4">
        <p id="descripcion-grafo" class="text-muted small mb-3"></p>
        <div id="contenedor-grafo" class="grafo-scroll"></div>
        <div class="mt-3 p-3 bg-light rounded text-muted small d-flex flex-wrap gap-4">
            <div><i class="fa-solid fa-arrow-right text-primary me-1"></i> <strong>Flechas azules:</strong> Recurso asignado a proceso.</div>
            <div><i class="fa-solid fa-arrow-right text-warning me-1"></i> <strong>Flechas naranjas discontinuas:</strong> Recurso solicitado.</div>
            <div><i class="fa-solid fa-arrow-right text-danger me-1"></i> <strong>Líneas rojas:</strong> Nodos y bordes en interbloqueo/ciclo.</div>
        </div>
    </div>
</section>

<!-- DETECCIÓN DE CICLOS -->
<section id="seccion-ciclos" class="card custom-card mb-4" hidden>
    <div class="card-header bg-white border-bottom py-3 px-4">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-rotate text-danger me-2"></i>Detección de Ciclos
        </h5>
    </div>
    <div class="card-body p-4">
        <p class="text-muted small mb-3">
            Ingrese la matriz de recursos pendientes para evaluar la presencia de ciclos de dependencia en el grafo mediante DFS.
        </p>

        <form id="formulario-ciclos">
            <div id="matriz-solicitudes-ciclos" class="tabla-m4 mb-3"></div>

            <button type="submit" class="btn btn-danger font-mono fw-semibold">
                <i class="fa-solid fa-magnifying-glass me-1"></i> Detectar Ciclos
            </button>
        </form>

        <div id="resultado-ciclos" class="mt-3" role="status" aria-live="polite"></div>
    </div>
</section>

<!-- RECUPERACIÓN ANTE INTERBLOQUEOS -->
<section id="seccion-recuperacion" class="card custom-card mb-4" hidden>
    <div class="card-header bg-white border-bottom py-3 px-4">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-kit-medical text-success me-2"></i>Estrategias de Recuperación Ante Interbloqueos
        </h5>
    </div>
    <div class="card-body p-4">
        <p class="text-muted small mb-3">
            Análisis de las mejores alternativas de terminación simulada de procesos para liberar recursos y resolver el estado de interbloqueo.
        </p>
        <div id="resultado-recuperacion" role="status" aria-live="polite"></div>
    </div>
</section>

<script>
    const formulario = document.getElementById('formulario');
    const contenedor = document.getElementById('matrices');
    const resultado = document.getElementById('resultado');
    const contenedorNecesidad = document.getElementById('matriz-necesidad');
    const contenedorBanquero = document.getElementById('resultado-banquero');
    const seccionGrafo = document.getElementById('seccion-grafo');
    const seccionCiclos = document.getElementById('seccion-ciclos');
    const formularioCiclos = document.getElementById('formulario-ciclos');
    const matrizSolicitudesCiclos = document.getElementById('matriz-solicitudes-ciclos');
    const resultadoCiclos = document.getElementById('resultado-ciclos');
    const seccionRecuperacion = document.getElementById('seccion-recuperacion');
    const resultadoRecuperacion = document.getElementById('resultado-recuperacion');
    const contenedorGrafo = document.getElementById('contenedor-grafo');
    const descripcionGrafo = document.getElementById('descripcion-grafo');
    const seccionSolicitud = document.getElementById('seccion-solicitud');
    const formularioSolicitud = document.getElementById('formulario-solicitud');
    const procesoSolicitud = document.getElementById('proceso-solicitud');
    const recursosSolicitados = document.getElementById('recursos-solicitados');
    const resultadoSolicitud = document.getElementById('resultado-solicitud');

    let matricesValidadas = null;

    function crearTabla(titulo, nombre, procesos, recursos) {
        let html = `<h6 class="fw-bold text-dark mt-3 mb-2">${titulo}</h6>`;
        html += '<div class="table-responsive"><table class="table table-custom table-sm mb-3"><thead><tr><th>Proceso</th>';

        for (let j = 0; j < recursos; j++) {
            html += `<th>R${j}</th>`;
        }

        html += '</tr></thead><tbody>';

        for (let i = 0; i < procesos; i++) {
            html += `<tr><td><span class="badge bg-secondary font-mono">P${i}</span></td>`;

            for (let j = 0; j < recursos; j++) {
                html += `<td>
                    <input type="number" min="0" step="1" required value="0"
                           class="form-control form-control-sm font-mono text-center mx-auto" style="max-width: 70px;"
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

        if (!Number.isInteger(n) || n < 1 || n > 10 || !Number.isInteger(m) || m < 1 || m > 6) {
            resultado.className = 'alert alert-danger small';
            resultado.textContent = 'Ingrese entre 1 y 10 procesos y entre 1 y 6 recursos.';
            return;
        }

        let html = crearTabla('Matriz de Asignación (Allocation)', 'allocation', n, m);
        html += crearTabla('Matriz Máxima (Maximum)', 'maximum', n, m);

        html += '<h6 class="fw-bold text-dark mt-3 mb-2">Vector de Recursos Disponibles (Available)</h6>';
        html += '<div class="d-flex flex-wrap gap-3 p-3 bg-light rounded border mb-3">';

        for (let j = 0; j < m; j++) {
            html += `<div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark font-mono">R${j}</span>
                <input type="number" min="0" step="1" required value="0"
                       class="form-control form-control-sm font-mono" style="width: 70px;"
                       name="available[${j}]">
            </div>`;
        }

        html += '</div>';
        contenedor.innerHTML = html;

        resultado.textContent = '';
        resultado.className = '';
    }

    function mostrarMatrizNecesidad(need) {
        contenedorNecesidad.replaceChildren();

        const card = document.createElement('div');
        card.className = 'card custom-card';

        const header = document.createElement('div');
        header.className = 'card-header bg-white border-bottom py-3 px-4';
        header.innerHTML = '<h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-table me-2 text-primary"></i>Matriz Necesidad Calculada (Need)</h6>';

        const body = document.createElement('div');
        body.className = 'card-body p-3';

        const contenedorTabla = document.createElement('div');
        contenedorTabla.className = 'table-responsive';

        const tabla = document.createElement('table');
        tabla.className = 'table table-custom mb-0';
        const encabezado = document.createElement('tr');

        const columnas = ['Proceso', ...need[0].map((_, j) => `R${j}`)];

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

            const proceso = document.createElement('td');
            proceso.innerHTML = `<span class="badge bg-secondary font-mono">P${i}</span>`;
            tr.appendChild(proceso);

            fila.forEach(valor => {
                const td = document.createElement('td');
                td.className = 'font-mono fw-bold text-dark';
                td.textContent = valor;
                tr.appendChild(td);
            });

            tbody.appendChild(tr);
        });

        tabla.appendChild(tbody);
        contenedorTabla.appendChild(tabla);
        body.appendChild(contenedorTabla);

        card.appendChild(header);
        card.appendChild(body);

        contenedorNecesidad.appendChild(card);
    }

    function mostrarResultadoBanquero(datos) {
        contenedorBanquero.replaceChildren();

        const card = document.createElement('div');
        card.className = 'card custom-card';

        const header = document.createElement('div');
        header.className = 'card-header bg-white border-bottom py-3 px-4';
        header.innerHTML = `<h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-shield-halved me-2 ${datos.safe ? 'text-success' : 'text-danger'}"></i>Estado de Seguridad del Sistema</h6>`;

        const body = document.createElement('div');
        body.className = 'card-body p-4';

        const estado = document.createElement('div');
        estado.className = datos.safe ? 'alert alert-success fw-semibold mb-3' : 'alert alert-danger fw-semibold mb-3';
        estado.innerHTML = datos.safe 
            ? '<i class="fa-solid fa-circle-check me-2"></i>Estado Seguro (Safe State)'
            : '<i class="fa-solid fa-triangle-exclamation me-2"></i>Estado Inseguro (Unsafe State)';
        body.appendChild(estado);

        const explicacion = document.createElement('p');
        explicacion.className = 'text-muted small mb-3';
        explicacion.textContent = datos.safe
            ? 'Existe una secuencia de ejecución que permite finalizar todos los procesos sin interbloqueos.'
            : 'No existe una secuencia segura completa. El sistema presenta riesgo potencial de interbloqueo.';
        body.appendChild(explicacion);

        const secuencia = document.createElement('div');
        secuencia.className = 'mb-3';
        const tituloSecuencia = datos.safe ? 'Secuencia segura: ' : 'Procesos completados: ';
        secuencia.innerHTML = `<span class="fw-semibold text-dark">${tituloSecuencia}</span> <span class="badge bg-dark font-mono fs-6">${datos.sequence.length > 0 ? datos.sequence.join(' ➔ ') : 'Ninguno'}</span>`;
        body.appendChild(secuencia);

        const titulo = document.createElement('h6');
        titulo.className = 'fw-bold text-dark mt-4 mb-2';
        titulo.textContent = 'Ejecución Paso a Paso';
        body.appendChild(titulo);

        const tabla = document.createElement('table');
        tabla.className = 'table table-custom table-sm mb-0';
        const thead = document.createElement('thead');
        const filaEncabezado = document.createElement('tr');

        const columnas = ['Paso', 'Proceso', 'Disponible Antes', 'Necesidad', 'Liberados', 'Disponible Después'];

        columnas.forEach(nombre => {
            const th = document.createElement('th');
            th.textContent = nombre;
            filaEncabezado.appendChild(th);
        });

        thead.appendChild(filaEncabezado);
        tabla.appendChild(thead);

        const tbody = document.createElement('tbody');

        const inicial = document.createElement('tr');
        const valoresIniciales = [
            'Inicial', '—',
            JSON.stringify(datos.steps.length > 0 ? datos.steps[0].work_before : datos.work_final),
            '—', '—', '—'
        ];

        valoresIniciales.forEach(valor => {
            const td = document.createElement('td');
            td.className = 'font-mono small text-muted';
            td.textContent = valor;
            inicial.appendChild(td);
        });

        tbody.appendChild(inicial);

        datos.steps.forEach((paso, indice) => {
            const fila = document.createElement('tr');
            const valores = [
                indice + 1, paso.process,
                JSON.stringify(paso.work_before),
                JSON.stringify(paso.need),
                JSON.stringify(paso.released),
                JSON.stringify(paso.work_after)
            ];

            valores.forEach(valor => {
                const td = document.createElement('td');
                td.className = 'font-mono small';
                td.textContent = valor;
                fila.appendChild(td);
            });

            tbody.appendChild(fila);
        });

        tabla.appendChild(tbody);

        const contenedorTabla = document.createElement('div');
        contenedorTabla.className = 'table-responsive';
        contenedorTabla.appendChild(tabla);
        body.appendChild(contenedorTabla);

        if (datos.pending.length > 0) {
            const pendientes = document.createElement('p');
            pendientes.className = 'text-danger small fw-semibold mt-3 mb-0';
            pendientes.textContent = 'Procesos pendientes: ' + datos.pending.join(', ');
            body.appendChild(pendientes);
        }

        card.appendChild(header);
        card.appendChild(body);
        contenedorBanquero.appendChild(card);
    }

    function limpiarGrafo() {
        contenedorGrafo.replaceChildren();
        descripcionGrafo.textContent = '';
        seccionGrafo.hidden = true;
    }

    function mostrarGrafo(grafo, conSolicitud = false, ciclo = []) {
        limpiarGrafo();

        if (!grafo || !Array.isArray(grafo.nodes) || !Array.isArray(grafo.edges)) {
            return;
        }

        const nodosDelCiclo = new Set(ciclo);
        const aristasDelCiclo = new Set();

        for (let i = 0; i < ciclo.length - 1; i++) {
            aristasDelCiclo.add(`${ciclo[i]}->${ciclo[i + 1]}`);
        }

        const recursos = grafo.nodes.filter(nodo => nodo.type === 'resource');
        const procesos = grafo.nodes.filter(nodo => nodo.type === 'process');

        const ancho = Math.max(760, (Math.max(recursos.length, procesos.length) + 1) * 135);
        const alto = 330;
        const ns = 'http://www.w3.org/2000/svg';

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

        const defs = crearElemento('defs');

        function crearMarcador(id, color) {
            const marcador = crearElemento('marker', {
                id: id, viewBox: '0 0 10 10',
                refX: 9, refY: 5,
                markerWidth: 7, markerHeight: 7,
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

        grafo.edges.forEach(arista => {
            const origen = posiciones[arista.from];
            const destino = posiciones[arista.to];

            if (!origen || !destino) return;

            const asignacion = arista.type === 'allocation';
            const esAristaDelCiclo = aristasDelCiclo.has(`${arista.from}->${arista.to}`);
            const desplazamiento = asignacion ? -9 : 9;

            const x1 = origen.x + desplazamiento;
            const x2 = destino.x + desplazamiento;
            const y1 = asignacion ? 108 : 231;
            const y2 = asignacion ? 231 : 108;

            const color = esAristaDelCiclo ? '#dc2626' : (asignacion ? '#2563eb' : '#d97706');

            const linea = crearElemento('line', {
                x1: x1, y1: y1, x2: x2, y2: y2,
                stroke: color,
                'stroke-width': esAristaDelCiclo ? 4 : 2.5,
                'marker-end': esAristaDelCiclo ? 'url(#flecha-m4-ciclo)' : (asignacion ? 'url(#flecha-m4-asignacion)' : 'url(#flecha-m4-solicitud)')
            });

            if (!asignacion) {
                linea.setAttribute('stroke-dasharray', '7 5');
            }

            svg.appendChild(linea);

            const cantidad = crearElemento('text', {
                x: (x1 + x2) / 2,
                y: (y1 + y2) / 2 + (asignacion ? -10 : 16),
                'text-anchor': 'middle',
                'font-size': 14,
                'font-weight': 'bold',
                fill: color,
                stroke: '#ffffff',
                'stroke-width': 4,
                'paint-order': 'stroke'
            });

            cantidad.textContent = arista.amount;
            svg.appendChild(cantidad);
        });

        recursos.forEach(nodo => {
            const punto = posiciones[nodo.id];

            const rectangulo = crearElemento('rect', {
                x: punto.x - 60, y: punto.y - 25,
                width: 120, height: 50, rx: 7,
                fill: '#dbeafe',
                stroke: nodosDelCiclo.has(nodo.id) ? '#dc2626' : '#2563eb',
                'stroke-width': nodosDelCiclo.has(nodo.id) ? 4 : 2
            });

            svg.appendChild(rectangulo);

            const nombre = crearElemento('text', {
                x: punto.x, y: punto.y - 4,
                'text-anchor': 'middle',
                'font-size': 15,
                'font-weight': 'bold',
                fill: '#1e40af'
            });

            nombre.textContent = nodo.label;
            svg.appendChild(nombre);

            const informacion = crearElemento('text', {
                x: punto.x, y: punto.y + 14,
                'text-anchor': 'middle',
                'font-size': 11,
                fill: '#1e40af'
            });

            informacion.textContent = `${nodo.available} libres / ${nodo.total} total`;
            svg.appendChild(informacion);
        });

        procesos.forEach(nodo => {
            const punto = posiciones[nodo.id];

            const circulo = crearElemento('circle', {
                cx: punto.x, cy: punto.y, r: 27,
                fill: '#dcfce7',
                stroke: nodosDelCiclo.has(nodo.id) ? '#dc2626' : '#16a34a',
                'stroke-width': nodosDelCiclo.has(nodo.id) ? 4 : 2
            });

            svg.appendChild(circulo);

            const nombre = crearElemento('text', {
                x: punto.x, y: punto.y + 5,
                'text-anchor': 'middle',
                'font-size': 15,
                'font-weight': 'bold',
                fill: '#166534'
            });

            nombre.textContent = nodo.label;
            svg.appendChild(nombre);
        });

        contenedorGrafo.appendChild(svg);

        if (ciclo.length > 0) {
            descripcionGrafo.textContent = 'Se detectó un ciclo de dependencias. Las conexiones y nodos resaltados en rojo forman parte del ciclo.';
        } else if (conSolicitud) {
            descripcionGrafo.textContent = 'Grafo con solicitudes simuladas. Las flechas de solicitud representan demandas pendientes.';
        } else {
            descripcionGrafo.textContent = 'Grafo actual del sistema basado en las matrices validadas.';
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

        for (let i = 0; i < procesos; i++) {
            const opcion = document.createElement('option');
            opcion.value = i;
            opcion.textContent = `P${i}`;
            procesoSolicitud.appendChild(opcion);
        }

        for (let j = 0; j < recursos; j++) {
            const div = document.createElement('div');
            div.className = 'd-flex align-items-center gap-2';

            const span = document.createElement('span');
            span.className = 'badge bg-secondary font-mono';
            span.textContent = `R${j}`;

            const entrada = document.createElement('input');
            entrada.type = 'number';
            entrada.className = 'form-control form-control-sm font-mono';
            entrada.style.width = '70px';
            entrada.name = `requested[${j}]`;
            entrada.min = '0';
            entrada.step = '1';
            entrada.value = '0';
            entrada.required = true;

            div.appendChild(span);
            div.appendChild(entrada);
            recursosSolicitados.appendChild(div);
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
        if (!recuperacion) return;

        seccionRecuperacion.hidden = false;

        if (!recuperacion.recovery_needed) {
            const mensaje = document.createElement('div');
            mensaje.className = 'alert alert-success fw-semibold mb-0';
            mensaje.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i>No se requiere recuperación por terminación de procesos en este escenario.';
            resultadoRecuperacion.appendChild(mensaje);
            return;
        }

        const pendientes = document.createElement('p');
        pendientes.className = 'text-muted small fw-semibold mb-2';
        pendientes.textContent = 'Procesos pendientes antes de recuperar: ' + recuperacion.pending_before.join(', ');
        resultadoRecuperacion.appendChild(pendientes);

        if (!recuperacion.recommended) {
            const mensaje = document.createElement('div');
            mensaje.className = 'alert alert-warning small mb-0';
            mensaje.textContent = 'No se encontró una terminación individual que libere a todos los demás procesos. Se requieren múltiples acciones de recuperación.';
            resultadoRecuperacion.appendChild(mensaje);
            return;
        }

        const recomendacion = recuperacion.recommended;

        const titulo = document.createElement('h6');
        titulo.className = 'fw-bold text-dark mt-3 mb-2';
        titulo.textContent = 'Recomendación: Terminar ' + recomendacion.process + ' (Simulado)';
        resultadoRecuperacion.appendChild(titulo);

        const recursos = document.createElement('p');
        recursos.className = 'text-muted small font-mono mb-2';
        recursos.textContent = 'Recursos liberados inicialmente: [' + recomendacion.released.join(', ') + ']';
        resultadoRecuperacion.appendChild(recursos);

        const lista = document.createElement('ol');
        lista.className = 'small ps-3 text-muted';

        recomendacion.steps.forEach(paso => {
            const elemento = document.createElement('li');
            elemento.className = 'mb-1';

            if (paso.action === 'abort') {
                elemento.innerHTML = `<strong>Terminar ${paso.process}</strong> (simulado). Libera [${paso.released.join(', ')}]. Disponible: [${paso.available_after.join(', ')}].`;
            } else if (paso.action === 'finish') {
                elemento.innerHTML = `<strong>${paso.process}</strong> finaliza exitosamente. Libera [${paso.released.join(', ')}]. Disponible: [${paso.available_after.join(', ')}].`;
            }

            lista.appendChild(elemento);
        });

        resultadoRecuperacion.appendChild(lista);

        const resultadoFinal = document.createElement('div');
        if (recomendacion.resolved) {
            resultadoFinal.className = 'alert alert-success small mb-0 mt-3 fw-semibold';
            resultadoFinal.innerHTML = `<i class="fa-solid fa-check me-1"></i> Recuperación exitosa. Sin procesos pendientes. Disponible final: [${recomendacion.available_after.join(', ')}].`;
        } else {
            resultadoFinal.className = 'alert alert-danger small mb-0 mt-3 fw-semibold';
            resultadoFinal.textContent = 'Procesos pendientes restantes: ' + recomendacion.pending_after.join(', ');
        }

        resultadoRecuperacion.appendChild(resultadoFinal);
    }

    function limpiarCiclos() {
        limpiarRecuperacion();
        seccionCiclos.hidden = true;
        matrizSolicitudesCiclos.replaceChildren();
        resultadoCiclos.textContent = '';
        resultadoCiclos.className = '';
    }

    function prepararFormularioCiclos(procesos, recursos) {
        let html = '<div class="table-responsive"><table class="table table-custom table-sm mb-0"><thead><tr><th>Proceso</th>';

        for (let j = 0; j < recursos; j++) {
            html += `<th>R${j}</th>`;
        }

        html += '</tr></thead><tbody>';

        for (let i = 0; i < procesos; i++) {
            html += `<tr><td><span class="badge bg-secondary font-mono">P${i}</span></td>`;

            for (let j = 0; j < recursos; j++) {
                html += `<td>
                    <input type="number" name="cycle_request[${i}][${j}]"
                           class="form-control form-control-sm font-mono text-center mx-auto" style="max-width:70px;"
                           min="0" step="1" value="0" required>
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

    document.getElementById('generar').addEventListener('click', generarMatrices);

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

        try {
            for (let i = 0; i < n; i++) {
                const filaAsignacion = [];
                const filaMaximo = [];

                for (let j = 0; j < m; j++) {
                    const inputAlloc = formulario.querySelector(`[name="allocation[${i}][${j}]"]`);
                    const inputMax = formulario.querySelector(`[name="maximum[${i}][${j}]"]`);

                    if (!inputAlloc || !inputMax) {
                        throw new Error(`Campo no encontrado para P${i}-R${j}`);
                    }

                    filaAsignacion.push(Number(inputAlloc.value));
                    filaMaximo.push(Number(inputMax.value));
                }

                allocation.push(filaAsignacion);
                maximum.push(filaMaximo);
            }

            for (let j = 0; j < m; j++) {
                const inputAvail = formulario.querySelector(`[name="available[${j}]"]`);
                if (!inputAvail) {
                    throw new Error(`Campo no encontrado para Disponible R${j}`);
                }
                available.push(Number(inputAvail.value));
            }

            const respuesta = await fetch("{{ route('deadlocks.validate') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ allocation, maximum, available })
            });

            const resText = await respuesta.text();
            let datos;
            try {
                datos = JSON.parse(resText);
            } catch (jsonErr) {
                resultado.className = 'alert alert-danger small mb-3';
                resultado.textContent = `Error ${respuesta.status} del servidor: ${resText.replace(/<[^>]*>?/gm, '').substring(0, 180)}`;
                return;
            }

            if (respuesta.ok && datos.success) {
                resultado.className = 'alert alert-success small mb-3';
                resultado.textContent = `Matrices validadas correctamente. Procesos: ${datos.processes}, Recursos: ${datos.resources}.`;

                mostrarMatrizNecesidad(datos.need);
                mostrarResultadoBanquero(datos);
                mostrarGrafo(datos.graph);

                matricesValidadas = { allocation, maximum, available };
                prepararFormularioSolicitud(datos.processes, datos.resources);
                prepararFormularioCiclos(datos.processes, datos.resources);
            } else {
                resultado.className = 'alert alert-warning small mb-3';
                let msgError = 'Error de validación en matrices.';
                if (datos.errors) {
                    msgError = Object.values(datos.errors).flat().join(' | ');
                } else if (datos.message) {
                    msgError = datos.message;
                }
                resultado.textContent = msgError;
            }
        } catch (error) {
            resultado.className = 'alert alert-danger small mb-3';
            resultado.textContent = 'Error JS: ' + error.message;
            contenedorNecesidad.replaceChildren();
        }
    });

    formularioSolicitud.addEventListener('submit', async function(evento) {
        evento.preventDefault();
        limpiarRecuperacion();

        if (!matricesValidadas || !formularioSolicitud.reportValidity()) return;

        const process = Number(procesoSolicitud.value);
        const requested = Array.from(recursosSolicitados.querySelectorAll('input'), entrada => Number(entrada.value));
        const { allocation, maximum, available } = matricesValidadas;

        const boton = formularioSolicitud.querySelector('button[type="submit"]');
        boton.disabled = true;

        resultadoSolicitud.className = 'alert alert-info small';
        resultadoSolicitud.textContent = 'Evaluando solicitud...';

        try {
            const respuesta = await fetch("{{ route('deadlocks.request') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ allocation, maximum, available, process, requested })
            });

            const resText = await respuesta.text();
            let datos;
            try {
                datos = JSON.parse(resText);
            } catch (jsonErr) {
                resultadoSolicitud.className = 'alert alert-danger small';
                resultadoSolicitud.textContent = `Error ${respuesta.status}: ${resText.replace(/<[^>]*>?/gm, '').substring(0, 150)}`;
                return;
            }

            if (respuesta.ok) {
                resultadoSolicitud.className = datos.granted ? 'alert alert-success small' : 'alert alert-warning small';
                mostrarGrafo(datos.graph, true);
                resultadoSolicitud.textContent = datos.reason;

                if (datos.granted) {
                    resultadoSolicitud.textContent += ' Disponible después: [' + datos.available_after.join(', ') + '].';
                    resultadoSolicitud.textContent += ' Secuencia segura: ' + datos.sequence.join(' ➔ ') + '.';
                }
            } else {
                resultadoSolicitud.className = 'alert alert-danger small';
                resultadoSolicitud.textContent = datos.errors ? Object.values(datos.errors).flat().join(' | ') : datos.message || 'No se pudo evaluar la solicitud.';
            }
        } catch (error) {
            resultadoSolicitud.className = 'alert alert-danger small';
            resultadoSolicitud.textContent = 'Error JS: ' + error.message;
        } finally {
            boton.disabled = false;
        }
    });

    formularioCiclos.addEventListener('submit', async function(evento) {
        evento.preventDefault();
        limpiarRecuperacion();

        if (!matricesValidadas || !formularioCiclos.reportValidity()) return;

        const { allocation, maximum, available } = matricesValidadas;
        const processes = allocation.length;
        const resources = available.length;

        const requests = [];

        try {
            for (let i = 0; i < processes; i++) {
                const fila = [];
                for (let j = 0; j < resources; j++) {
                    const entrada = formularioCiclos.querySelector(`[name="cycle_request[${i}][${j}]"]`);
                    if (!entrada) throw new Error(`Campo no encontrado para cycle_request[${i}][${j}]`);
                    fila.push(Number(entrada.value));
                }
                requests.push(fila);
            }

            const boton = formularioCiclos.querySelector('button[type="submit"]');
            boton.disabled = true;

            resultadoCiclos.className = 'alert alert-info small';
            resultadoCiclos.textContent = 'Analizando grafo...';

            const respuesta = await fetch("{{ route('deadlocks.cycles') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ allocation, maximum, available, requests })
            });

            const resText = await respuesta.text();
            let datos;
            try {
                datos = JSON.parse(resText);
            } catch (jsonErr) {
                resultadoCiclos.className = 'alert alert-danger small';
                resultadoCiclos.textContent = `Error ${respuesta.status}: ${resText.replace(/<[^>]*>?/gm, '').substring(0, 150)}`;
                return;
            }

            if (respuesta.ok) {
                if (datos.has_cycle) {
                    resultadoCiclos.className = 'alert alert-danger small';
                    resultadoCiclos.textContent = datos.message + ' Ciclo encontrado: ' + datos.cycle.join(' ➔ ');
                } else {
                    resultadoCiclos.className = 'alert alert-success small';
                    resultadoCiclos.textContent = datos.message;
                }

                mostrarGrafo(datos.graph, true, datos.cycle);
                mostrarRecuperacion(datos.recovery);
            } else {
                resultadoCiclos.className = 'alert alert-danger small';
                resultadoCiclos.textContent = datos.errors ? Object.values(datos.errors).flat().join(' | ') : datos.message || 'Error al analizar ciclos.';
            }
        } catch (error) {
            resultadoCiclos.className = 'alert alert-danger small';
            resultadoCiclos.textContent = 'Error JS: ' + error.message;
        } finally {
            const boton = formularioCiclos.querySelector('button[type="submit"]');
            if (boton) boton.disabled = false;
        }
    });

    generarMatrices();
    contenedorBanquero.replaceChildren();
</script>

@endsection
