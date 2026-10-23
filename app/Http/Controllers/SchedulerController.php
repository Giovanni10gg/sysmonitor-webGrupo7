<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchedulerController extends Controller
{
    public function index()
    {
        return view('scheduler.index');
    }

    public function fcfs(Request $request)
    {
        $datos = $request->input('processes');

        $procesos = [];

        foreach ($datos as $i => $proceso) {

            $procesos[] = [
                'nombre' => 'P' . ($i + 1),
                'llegada' => $proceso['arrival_time'],
                'rafaga' => $proceso['burst_time'],
                'prioridad' => $proceso['priority'],
            ];
        }

        $tiempoActual = 0;
$gantt = [];

foreach ($procesos as &$proceso) {

    if ($tiempoActual < $proceso['llegada']) {
        $tiempoActual = $proceso['llegada'];
    }
	$inicio = $tiempoActual;

    $proceso['espera'] =
        $tiempoActual - $proceso['llegada'];

    $tiempoActual += $proceso['rafaga'];

    $proceso['retorno'] =
        $tiempoActual - $proceso['llegada'];

    $gantt[] = [
        'proceso' => $proceso['nombre'],
        'inicio' => $inicio,
        'fin' => $tiempoActual
    ];
}
	unset($proceso);
	$totalEspera = 0;
$totalRetorno = 0;

foreach ($procesos as $proceso) {

    $totalEspera += $proceso['espera'];

    $totalRetorno += $proceso['retorno'];
}

$promedioEspera = $totalEspera / count($procesos);

$promedioRetorno = $totalRetorno / count($procesos);

        return view(
    'scheduler.resultado',
    [
        'procesos' => $procesos,
        'algoritmo' => 'FCFS',
        'promedioEspera' => $promedioEspera,
        'promedioRetorno' => $promedioRetorno,
	'gantt' => $gantt
    ]
  );
    }

    public function sjf(Request $request)
    {
        $datos = $request->input('processes');

        $procesos = [];

        foreach ($datos as $i => $proceso) {

            $procesos[] = [
                'nombre' => 'P' . ($i + 1),
                'llegada' => (int) $proceso['arrival_time'],
                'rafaga' => (int) $proceso['burst_time'],
                'prioridad' => (int) $proceso['priority'],
            ];
        }

        usort($procesos, function ($a, $b) {
            return $a['rafaga'] <=> $b['rafaga'];
        });

        $tiempoActual = 0;
	$gantt = [];

        foreach ($procesos as &$proceso) {

            $inicio = $tiempoActual;

            $proceso['espera'] = $tiempoActual;

            $tiempoActual += $proceso['rafaga'];

            $proceso['retorno'] = $tiempoActual;

	    $gantt[] = [
    'proceso' => $proceso['nombre'],
    'inicio' => $inicio,
    'fin' => $tiempoActual
     ];
        }
	unset($proceso);
	$totalEspera = 0;
$totalRetorno = 0;

foreach ($procesos as $proceso) {

    $totalEspera += $proceso['espera'];

    $totalRetorno += $proceso['retorno'];
}

$promedioEspera = $totalEspera / count($procesos);

$promedioRetorno = $totalRetorno / count($procesos);

        return view(
    'scheduler.resultado',
    [
        'procesos' => $procesos,
        'algoritmo' => 'SJF',
        'promedioEspera' => $promedioEspera,
        'promedioRetorno' => $promedioRetorno,
	'gantt' => $gantt
    ]
    );

    }
	public function prioridad(Request $request)
{
    $datos = $request->input('processes');

    $procesos = [];

    foreach ($datos as $i => $proceso) {

        $procesos[] = [
            'nombre' => 'P' . ($i + 1),
            'llegada' => (int) $proceso['arrival_time'],
            'rafaga' => (int) $proceso['burst_time'],
            'prioridad' => (int) $proceso['priority'],
        ];
    }

    usort($procesos, function ($a, $b) {
        return $a['prioridad'] <=> $b['prioridad'];
    });

    $tiempoActual = 0;
    $gantt = [];

    foreach ($procesos as &$proceso) {
	
	$inicio = $tiempoActual;	

        $proceso['espera'] = $tiempoActual;

        $tiempoActual += $proceso['rafaga'];

        $proceso['retorno'] = $tiempoActual;
	
	$gantt[] = [
    'proceso' => $proceso['nombre'],
    'inicio' => $inicio,
    'fin' => $tiempoActual
   ];
    }
        unset($proceso);
    $totalEspera = 0;
$totalRetorno = 0;

foreach ($procesos as $proceso) {

    $totalEspera += $proceso['espera'];

    $totalRetorno += $proceso['retorno'];
}

$promedioEspera = $totalEspera / count($procesos);

$promedioRetorno = $totalRetorno / count($procesos);

    return view(
    'scheduler.resultado',
    [
        'procesos' => $procesos,
        'algoritmo' => 'PRIORIDADES',
        'promedioEspera' => $promedioEspera,
        'promedioRetorno' => $promedioRetorno,
	'gantt' => $gantt
    ]
  );
 }
	public function roundRobin(Request $request)
{
    $datos = $request->input('processes');
    $quantum = (int) $request->input('quantum');

    $procesos = [];

    foreach ($datos as $i => $proceso) {

        $procesos[] = [
    'nombre' => 'P' . ($i + 1),
    'llegada' => (int) $proceso['arrival_time'],
    'rafaga' => (int) $proceso['burst_time'],
    'prioridad' => (int) $proceso['priority'],
    'restante' => (int) $proceso['burst_time'],
    'espera' => 0,
    'retorno' => 0,
    ];
    }

    $tiempo = 0;
    $completados = 0;
    $totalProcesos = count($procesos);

    while ($completados < $totalProcesos) {

        foreach ($procesos as &$proceso) {

            if ($proceso['restante'] > 0) {

                if ($proceso['restante'] > $quantum) {

                    $tiempo += $quantum;
                    $proceso['restante'] -= $quantum;

                } else {

                    $tiempo += $proceso['restante'];

                    $proceso['retorno'] = $tiempo;

                    $proceso['espera'] =
                        $proceso['retorno'] - $proceso['rafaga'];

                    $proceso['restante'] = 0;

                    $completados++;
                }
            }
        }
    }
	unset($proceso);
        $tiempo = 0;

    foreach ($procesos as &$proceso) {

        $proceso['espera'] = $tiempo;

        $tiempo += $proceso['rafaga'];

        $proceso['retorno'] = $tiempo;
    }
    unset($proceso);
    $totalEspera = 0;
$totalRetorno = 0;

foreach ($procesos as $proceso) {

    $totalEspera += $proceso['espera'];

    $totalRetorno += $proceso['retorno'];
}

$promedioEspera = $totalEspera / count($procesos);

$promedioRetorno = $totalRetorno / count($procesos);

   return view(
    'scheduler.resultado',
    [
        'procesos' => $procesos,
        'algoritmo' => 'ROUND ROBIN',
        'promedioEspera' => $promedioEspera,
        'promedioRetorno' => $promedioRetorno
    ]
);
}
	public function srtf(Request $request)
{
    $datos = $request->input('processes');

    $procesos = [];

    foreach ($datos as $i => $proceso) {

        $procesos[] = [
    'nombre' => 'P' . ($i + 1),
    'llegada' => (int) $proceso['arrival_time'],
    'rafaga' => (int) $proceso['burst_time'],
    'prioridad' => (int) $proceso['priority'],
    'restante' => (int) $proceso['burst_time'],
    'espera' => 0,
    'retorno' => 0,
    ];
    }

    usort($procesos, function ($a, $b) {
        return $a['rafaga'] <=> $b['rafaga'];
    });

    $tiempo = 0;

    foreach ($procesos as &$proceso) {

        $proceso['espera'] = $tiempo;

        $tiempo += $proceso['rafaga'];

        $proceso['retorno'] = $tiempo;
    }
    unset($proceso);
    $totalEspera = 0;
$totalRetorno = 0;

foreach ($procesos as $proceso) {

    $totalEspera += $proceso['espera'];

    $totalRetorno += $proceso['retorno'];
}

$promedioEspera = $totalEspera / count($procesos);

$promedioRetorno = $totalRetorno / count($procesos);

    return view(
    'scheduler.resultado',
    [
        'procesos' => $procesos,
        'algoritmo' => 'SRTF',
        'promedioEspera' => $promedioEspera,
        'promedioRetorno' => $promedioRetorno
    ]
);
}

}
