@extends('layouts.app')

@section('title', 'Módulo 3 - Planificación CPU')
@section('module_tag', 'Módulo 03')
@section('module_icon', 'fa-clock')
@section('module_description', 'Simulador interactivo de algoritmos de planificación de procesos (FCFS, SJF, Prioridades, RR y SRTF)')

@section('content')

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card custom-card">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fa-solid fa-sliders text-primary me-2"></i>Tabla de Procesos a Simular
                </h5>
                <button type="button" class="btn btn-sm btn-outline-success font-mono fw-semibold" id="btnAddProcess">
                    <i class="fa-solid fa-plus me-1"></i> Agregar Proceso
                </button>
            </div>

            <form id="schedulerForm" method="POST" action="{{ route('scheduler.fcfs') }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-custom mb-0" id="processesTable">
                        <thead>
                            <tr>
                                <th>Proceso</th>
                                <th>Tiempo Llegada (s)</th>
                                <th>Ráfaga CPU (s)</th>
                                <th>Prioridad</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-secondary font-mono proc-name">P1</span></td>
                                <td><input type="number" name="processes[0][arrival_time]" class="form-control form-control-sm font-mono" value="0" min="0" required></td>
                                <td><input type="number" name="processes[0][burst_time]" class="form-control form-control-sm font-mono" value="4" min="1" required></td>
                                <td><input type="number" name="processes[0][priority]" class="form-control form-control-sm font-mono" value="1" min="1" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove py-0 px-2" disabled><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-secondary font-mono proc-name">P2</span></td>
                                <td><input type="number" name="processes[1][arrival_time]" class="form-control form-control-sm font-mono" value="1" min="0" required></td>
                                <td><input type="number" name="processes[1][burst_time]" class="form-control form-control-sm font-mono" value="3" min="1" required></td>
                                <td><input type="number" name="processes[1][priority]" class="form-control form-control-sm font-mono" value="2" min="1" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove py-0 px-2"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-secondary font-mono proc-name">P3</span></td>
                                <td><input type="number" name="processes[2][arrival_time]" class="form-control form-control-sm font-mono" value="2" min="0" required></td>
                                <td><input type="number" name="processes[2][burst_time]" class="form-control form-control-sm font-mono" value="5" min="1" required></td>
                                <td><input type="number" name="processes[2][priority]" class="form-control form-control-sm font-mono" value="3" min="1" required></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove py-0 px-2"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- PANEL DE CONTROLES -->
                <div class="p-4 bg-light border-top">
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold text-dark">Quantum (Round Robin)</label>
                            <input type="number" name="quantum" class="form-control font-mono" value="2" min="1" required>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 pt-2">
                        <button type="submit" onclick="setAlgorithm('{{ route('scheduler.fcfs') }}')" class="btn btn-primary fw-semibold font-mono">
                            <i class="fa-solid fa-play me-1"></i> Simular FCFS
                        </button>
                        <button type="submit" onclick="setAlgorithm('{{ route('scheduler.sjf') }}')" class="btn btn-secondary fw-semibold font-mono">
                            <i class="fa-solid fa-bolt me-1"></i> Simular SJF
                        </button>
                        <button type="submit" onclick="setAlgorithm('{{ route('scheduler.prioridad') }}')" class="btn btn-warning fw-semibold font-mono text-dark">
                            <i class="fa-solid fa-layer-group me-1"></i> Prioridades
                        </button>
                        <button type="submit" onclick="setAlgorithm('{{ route('scheduler.roundrobin') }}')" class="btn btn-info fw-semibold font-mono text-white">
                            <i class="fa-solid fa-rotate me-1"></i> Round Robin
                        </button>
                        <button type="submit" onclick="setAlgorithm('{{ route('scheduler.srtf') }}')" class="btn btn-danger fw-semibold font-mono">
                            <i class="fa-solid fa-stopwatch me-1"></i> Simular SRTF
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- PANEL LATERAL -->
    <div class="col-12 col-xl-4">
        <div class="card custom-card mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-compass text-primary me-2"></i>Herramientas de Análisis</h6>
            </div>
            <div class="card-body p-4 d-grid gap-2">
                <a href="{{ route('scheduler.historial') }}" class="btn btn-outline-primary font-mono fw-semibold">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i> Ver Historial de Simulaciones
                </a>
                <a href="{{ route('scheduler.comparativa') }}" class="btn btn-outline-dark font-mono fw-semibold">
                    <i class="fa-solid fa-chart-column me-2"></i> Ver Comparativa de Algoritmos
                </a>
            </div>
        </div>

        <div class="card custom-card">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-circle-info text-info me-2"></i>Resumen de Algoritmos</h6>
            </div>
            <div class="card-body p-4 small text-muted">
                <ul class="mb-0 ps-3">
                    <li class="mb-2"><strong>FCFS:</strong> Primero en llegar, primero en ser atendido (No apropiativo).</li>
                    <li class="mb-2"><strong>SJF:</strong> Proceso con la ráfaga más corta primero (No apropiativo).</li>
                    <li class="mb-2"><strong>Prioridad:</strong> Ejecución basada en nivel (1 = Mayor prioridad).</li>
                    <li class="mb-2"><strong>Round Robin:</strong> Asignación equitativa por rodajas de tiempo (Quantum).</li>
                    <li><strong>SRTF:</strong> Tiempo restante más corto primero (Apropiativo).</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    function setAlgorithm(url) {
        document.getElementById('schedulerForm').action = url;
    }

    document.getElementById('btnAddProcess').addEventListener('click', function() {
        const tbody = document.querySelector('#processesTable tbody');
        const rowCount = tbody.children.length;
        const newIndex = rowCount;
        const pName = 'P' + (rowCount + 1);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><span class="badge bg-secondary font-mono proc-name">${pName}</span></td>
            <td><input type="number" name="processes[${newIndex}][arrival_time]" class="form-control form-control-sm font-mono" value="0" min="0" required></td>
            <td><input type="number" name="processes[${newIndex}][burst_time]" class="form-control form-control-sm font-mono" value="3" min="1" required></td>
            <td><input type="number" name="processes[${newIndex}][priority]" class="form-control form-control-sm font-mono" value="1" min="1" required></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove py-0 px-2"><i class="fa-solid fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        updateRemoveButtons();
    });

    document.querySelector('#processesTable').addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove')) {
            const btn = e.target.closest('.btn-remove');
            const tbody = document.querySelector('#processesTable tbody');
            if (tbody.children.length > 1) {
                btn.closest('tr').remove();
                reindexRows();
            }
        }
    });

    function reindexRows() {
        const rows = document.querySelectorAll('#processesTable tbody tr');
        rows.forEach((row, idx) => {
            row.querySelector('.proc-name').textContent = 'P' + (idx + 1);
            const inputs = row.querySelectorAll('input');
            inputs[0].name = `processes[${idx}][arrival_time]`;
            inputs[1].name = `processes[${idx}][burst_time]`;
            inputs[2].name = `processes[${idx}][priority]`;
        });
        updateRemoveButtons();
    }

    function updateRemoveButtons() {
        const buttons = document.querySelectorAll('#processesTable .btn-remove');
        if (buttons.length === 1) {
            buttons[0].disabled = true;
        } else {
            buttons.forEach(btn => btn.disabled = false);
        }
    }
</script>

@endsection
