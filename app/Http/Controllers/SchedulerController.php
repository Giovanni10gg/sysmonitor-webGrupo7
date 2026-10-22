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

        foreach ($procesos as &$proceso) {

            if ($tiempoActual < $proceso['llegada']) {
                $tiempoActual = $proceso['llegada'];
            }

            $proceso['espera'] = $tiempoActual - $proceso['llegada'];

            $tiempoActual += $proceso['rafaga'];

            $proceso['retorno'] =
                $tiempoActual - $proceso['llegada'];
        }

        return view(
    'scheduler.resultado',
    [
        'procesos' => $procesos,
        'algoritmo' => 'FCFS'
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

        foreach ($procesos as &$proceso) {

            $proceso['espera'] = $tiempoActual;

            $tiempoActual += $proceso['rafaga'];

            $proceso['retorno'] = $tiempoActual;
        }

        return view(
    'scheduler.resultado',
    [
        'procesos' => $procesos,
        'algoritmo' => 'SJF'
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

    foreach ($procesos as &$proceso) {

        $proceso['espera'] = $tiempoActual;

        $tiempoActual += $proceso['rafaga'];

        $proceso['retorno'] = $tiempoActual;
    }

    return view(
        'scheduler.resultado',
        [
            'procesos' => $procesos,
            'algoritmo' => 'PRIORIDADES'
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
            'rafaga' => (int) $proceso['burst_time'],
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

   return view(
    'scheduler.resultado',
    [
        'procesos' => $procesos,
        'algoritmo' => 'ROUND ROBIN'
    ]
);
}
	public function srtf(Request $request)
{
    dd($request->all());
}

}
