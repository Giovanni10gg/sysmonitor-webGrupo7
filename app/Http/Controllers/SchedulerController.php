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
            compact('procesos')
        );
    }
}
