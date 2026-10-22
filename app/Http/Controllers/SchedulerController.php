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
}
