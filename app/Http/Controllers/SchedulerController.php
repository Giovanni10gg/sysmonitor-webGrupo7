<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simulacion;

class SchedulerController extends Controller
{
    public function index()
    {
        return view('scheduler.index');
    }

    private function sanitizeProcesses(array $datos): array
    {
        $procesos = [];
        foreach ($datos as $i => $p) {
            $procesos[] = [
                'id' => $i,
                'nombre' => 'P' . ($i + 1),
                'llegada' => (int) ($p['arrival_time'] ?? 0),
                'rafaga' => max(1, (int) ($p['burst_time'] ?? 1)),
                'prioridad' => (int) ($p['priority'] ?? 1),
                'restante' => max(1, (int) ($p['burst_time'] ?? 1)),
                'espera' => 0,
                'retorno' => 0,
                'completado' => false,
            ];
        }
        return $procesos;
    }

    private function calcularPromediosYGuardar(string $algoritmo, array $procesos, array $gantt)
    {
        $totalEspera = 0;
        $totalRetorno = 0;
        $count = count($procesos);

        if ($count === 0) {
            return back()->with('error', 'Debe agregar al menos un proceso.');
        }

        foreach ($procesos as $p) {
            $totalEspera += $p['espera'];
            $totalRetorno += $p['retorno'];
        }

        $promedioEspera = round($totalEspera / $count, 2);
        $promedioRetorno = round($totalRetorno / $count, 2);

        Simulacion::create([
            'algoritmo' => $algoritmo,
            'promedio_espera' => $promedioEspera,
            'promedio_retorno' => $promedioRetorno,
        ]);

        return view('scheduler.resultado', compact('procesos', 'algoritmo', 'promedioEspera', 'promedioRetorno', 'gantt'));
    }

    public function fcfs(Request $request)
    {
        $datos = $request->input('processes', []);
        if (empty($datos)) return back()->with('error', 'Ingrese datos válidos.');

        $procesos = $this->sanitizeProcesses($datos);
        usort($procesos, fn($a, $b) => $a['llegada'] <=> $b['llegada']);

        $tiempoActual = 0;
        $gantt = [];

        foreach ($procesos as &$p) {
            if ($tiempoActual < $p['llegada']) {
                $tiempoActual = $p['llegada'];
            }

            $inicio = $tiempoActual;
            $p['espera'] = $tiempoActual - $p['llegada'];
            $tiempoActual += $p['rafaga'];
            $p['retorno'] = $tiempoActual - $p['llegada'];

            $gantt[] = [
                'proceso' => $p['nombre'],
                'inicio' => $inicio,
                'fin' => $tiempoActual,
            ];
        }
        unset($p);

        return $this->calcularPromediosYGuardar('FCFS', $procesos, $gantt);
    }

    public function sjf(Request $request)
    {
        $datos = $request->input('processes', []);
        if (empty($datos)) return back()->with('error', 'Ingrese datos válidos.');

        $procesos = $this->sanitizeProcesses($datos);
        $n = count($procesos);
        $completados = 0;
        $tiempoActual = 0;
        $gantt = [];

        while ($completados < $n) {
            $elegibles = array_filter($procesos, fn($p) => !$p['completado'] && $p['llegada'] <= $tiempoActual);

            if (empty($elegibles)) {
                $noCompletados = array_filter($procesos, fn($p) => !$p['completado']);
                $siguienteLlegada = min(array_column($noCompletados, 'llegada'));
                $tiempoActual = $siguienteLlegada;
                continue;
            }

            usort($elegibles, fn($a, $b) => $a['rafaga'] <=> $b['rafaga']);
            $pElegido = $elegibles[0];
            $idx = $pElegido['id'];

            $inicio = $tiempoActual;
            $procesos[$idx]['espera'] = $tiempoActual - $procesos[$idx]['llegada'];
            $tiempoActual += $procesos[$idx]['rafaga'];
            $procesos[$idx]['retorno'] = $tiempoActual - $procesos[$idx]['llegada'];
            $procesos[$idx]['completado'] = true;

            $gantt[] = [
                'proceso' => $procesos[$idx]['nombre'],
                'inicio' => $inicio,
                'fin' => $tiempoActual,
            ];

            $completados++;
        }

        return $this->calcularPromediosYGuardar('SJF', $procesos, $gantt);
    }

    public function prioridad(Request $request)
    {
        $datos = $request->input('processes', []);
        if (empty($datos)) return back()->with('error', 'Ingrese datos válidos.');

        $procesos = $this->sanitizeProcesses($datos);
        $n = count($procesos);
        $completados = 0;
        $tiempoActual = 0;
        $gantt = [];

        while ($completados < $n) {
            $elegibles = array_filter($procesos, fn($p) => !$p['completado'] && $p['llegada'] <= $tiempoActual);

            if (empty($elegibles)) {
                $noCompletados = array_filter($procesos, fn($p) => !$p['completado']);
                $siguienteLlegada = min(array_column($noCompletados, 'llegada'));
                $tiempoActual = $siguienteLlegada;
                continue;
            }

            usort($elegibles, fn($a, $b) => $a['prioridad'] <=> $b['prioridad']);
            $pElegido = $elegibles[0];
            $idx = $pElegido['id'];

            $inicio = $tiempoActual;
            $procesos[$idx]['espera'] = $tiempoActual - $procesos[$idx]['llegada'];
            $tiempoActual += $procesos[$idx]['rafaga'];
            $procesos[$idx]['retorno'] = $tiempoActual - $procesos[$idx]['llegada'];
            $procesos[$idx]['completado'] = true;

            $gantt[] = [
                'proceso' => $procesos[$idx]['nombre'],
                'inicio' => $inicio,
                'fin' => $tiempoActual,
            ];

            $completados++;
        }

        return $this->calcularPromediosYGuardar('PRIORIDADES', $procesos, $gantt);
    }

    public function roundRobin(Request $request)
    {
        $datos = $request->input('processes', []);
        $quantum = max(1, (int) $request->input('quantum', 2));
        if (empty($datos)) return back()->with('error', 'Ingrese datos válidos.');

        $procesos = $this->sanitizeProcesses($datos);
        $n = count($procesos);

        usort($procesos, fn($a, $b) => $a['llegada'] <=> $b['llegada']);

        $tiempoActual = 0;
        $completados = 0;
        $gantt = [];
        $cola = [];
        $enCola = array_fill(0, $n, false);

        foreach ($procesos as $i => $p) {
            if ($p['llegada'] <= $tiempoActual) {
                $cola[] = $i;
                $enCola[$i] = true;
            }
        }

        while ($completados < $n) {
            if (empty($cola)) {
                $noCompletados = array_filter($procesos, fn($p) => !$p['completado']);
                if (empty($noCompletados)) break;

                $minLlegada = min(array_column($noCompletados, 'llegada'));
                $tiempoActual = max($tiempoActual, $minLlegada);

                foreach ($procesos as $i => $p) {
                    if (!$p['completado'] && !$enCola[$i] && $p['llegada'] <= $tiempoActual) {
                        $cola[] = $i;
                        $enCola[$i] = true;
                    }
                }
            }

            $idx = array_shift($cola);
            $enCola[$idx] = false;

            $ejecucion = min($quantum, $procesos[$idx]['restante']);
            $inicio = $tiempoActual;
            $tiempoActual += $ejecucion;
            $procesos[$idx]['restante'] -= $ejecucion;

            $gantt[] = [
                'proceso' => $procesos[$idx]['nombre'],
                'inicio' => $inicio,
                'fin' => $tiempoActual,
            ];

            foreach ($procesos as $i => $p) {
                if (!$p['completado'] && $i !== $idx && !$enCola[$i] && $p['llegada'] <= $tiempoActual) {
                    $cola[] = $i;
                    $enCola[$i] = true;
                }
            }

            if ($procesos[$idx]['restante'] == 0) {
                $procesos[$idx]['completado'] = true;
                $procesos[$idx]['retorno'] = $tiempoActual - $procesos[$idx]['llegada'];
                $procesos[$idx]['espera'] = $procesos[$idx]['retorno'] - $procesos[$idx]['rafaga'];
                $completados++;
            } else {
                $cola[] = $idx;
                $enCola[$idx] = true;
            }
        }

        return $this->calcularPromediosYGuardar('ROUND ROBIN', $procesos, $gantt);
    }

    public function srtf(Request $request)
    {
        $datos = $request->input('processes', []);
        if (empty($datos)) return back()->with('error', 'Ingrese datos válidos.');

        $procesos = $this->sanitizeProcesses($datos);
        $n = count($procesos);
        $completados = 0;
        $tiempoActual = 0;
        $ganttRaw = [];

        while ($completados < $n) {
            $elegibles = array_filter($procesos, fn($p) => !$p['completado'] && $p['llegada'] <= $tiempoActual);

            if (empty($elegibles)) {
                $noCompletados = array_filter($procesos, fn($p) => !$p['completado']);
                $siguienteLlegada = min(array_column($noCompletados, 'llegada'));
                $tiempoActual = $siguienteLlegada;
                continue;
            }

            usort($elegibles, fn($a, $b) => $a['restante'] <=> $b['restante']);
            $pElegido = $elegibles[0];
            $idx = $pElegido['id'];

            $ganttRaw[] = [
                'proceso' => $procesos[$idx]['nombre'],
                'tiempo' => $tiempoActual,
            ];

            $procesos[$idx]['restante']--;
            $tiempoActual++;

            if ($procesos[$idx]['restante'] == 0) {
                $procesos[$idx]['completado'] = true;
                $procesos[$idx]['retorno'] = $tiempoActual - $procesos[$idx]['llegada'];
                $procesos[$idx]['espera'] = $procesos[$idx]['retorno'] - $procesos[$idx]['rafaga'];
                $completados++;
            }
        }

        $gantt = [];
        if (!empty($ganttRaw)) {
            $currentBlock = [
                'proceso' => $ganttRaw[0]['proceso'],
                'inicio' => $ganttRaw[0]['tiempo'],
                'fin' => $ganttRaw[0]['tiempo'] + 1,
            ];

            for ($i = 1; $i < count($ganttRaw); $i++) {
                if ($ganttRaw[$i]['proceso'] === $currentBlock['proceso'] && $ganttRaw[$i]['tiempo'] === $currentBlock['fin']) {
                    $currentBlock['fin']++;
                } else {
                    $gantt[] = $currentBlock;
                    $currentBlock = [
                        'proceso' => $ganttRaw[$i]['proceso'],
                        'inicio' => $ganttRaw[$i]['tiempo'],
                        'fin' => $ganttRaw[$i]['tiempo'] + 1,
                    ];
                }
            }
            $gantt[] = $currentBlock;
        }

        return $this->calcularPromediosYGuardar('SRTF', $procesos, $gantt);
    }

    public function historial()
    {
        $simulaciones = Simulacion::orderBy('id', 'desc')->get();
        return view('scheduler.historial', compact('simulaciones'));
    }

    public function comparativa()
    {
        $simulaciones = Simulacion::orderBy('id', 'desc')->get();

        $comparativaData = Simulacion::selectRaw('algoritmo, AVG(promedio_espera) as avg_espera, AVG(promedio_retorno) as avg_retorno, COUNT(*) as total_runs')
            ->groupBy('algoritmo')
            ->get();

        return view('scheduler.comparativa', compact('simulaciones', 'comparativaData'));
    }
}
