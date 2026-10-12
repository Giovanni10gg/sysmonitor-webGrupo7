<?php

namespace App\Http\Controllers;

use App\Services\CycleDetectionService;
use App\Services\BankerService;
use App\Services\ResourceGraphService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DeadlockController extends Controller
{
    // Mostrar la pantalla principal de M4
    public function index()
    {
        return view('deadlocks.index');
    }

    // Recibir y validar las matrices
public function validateMatrices(
    Request $request,
    BankerService $banker,
    ResourceGraphService $graphService
)
    {
        $data = $request->validate([
            'allocation' => ['required', 'array', 'min:1'],
            'allocation.*' => ['required', 'array', 'min:1'],
            'allocation.*.*' => ['required', 'integer', 'min:0'],

            'maximum' => ['required', 'array', 'min:1'],
            'maximum.*' => ['required', 'array', 'min:1'],
            'maximum.*.*' => ['required', 'integer', 'min:0'],

            'available' => ['required', 'array', 'min:1'],
            'available.*' => ['required', 'integer', 'min:0'],
        ]);

        $allocation = $data['allocation'];
        $maximum = $data['maximum'];
        $available = $data['available'];

        $processes = count($allocation);
        $resources = count($available);

        if ($processes !== count($maximum)) {
            throw ValidationException::withMessages([
                'maximum' => 'La cantidad de procesos no coincide.',
            ]);
        }

        for ($i = 0; $i < $processes; $i++) {

            if (
                !isset($maximum[$i]) ||
                !array_is_list($allocation[$i]) ||
                !array_is_list($maximum[$i]) ||
                count($allocation[$i]) !== $resources ||
                count($maximum[$i]) !== $resources
            ) {
                throw ValidationException::withMessages([
                    'allocation' => 'Las dimensiones de las matrices no coinciden.',
                ]);
            }

            for ($j = 0; $j < $resources; $j++) {

                if ($allocation[$i][$j] > $maximum[$i][$j]) {
                    throw ValidationException::withMessages([
                        'allocation' => 'La asignacion no puede superar el maximo.',
                    ]);
                }
            }
        }

// Calcular la matriz Necesidad
// Necesidad = Maximo - Asignacion

$need = [];

for ($i = 0; $i < $processes; $i++) {

    $fila = [];

    for ($j = 0; $j < $resources; $j++) {

        $necesidad = $maximum[$i][$j] - $allocation[$i][$j];

        $fila[] = $necesidad;
    }

    $need[] = $fila;
}
// Ejecutar el algoritmo del banquero
$safety = $banker->checkSafety(
    $allocation,
    $need,
    $available
);
// Construir el grafo de asignacion
$graph = $graphService->build(
    $allocation,
    $available
);
 return response()->json([
    'success' => true,
    'message' => 'Matrices validadas correctamente',
    'processes' => $processes,
    'resources' => $resources,
    'need' => $need,
'safe' => $safety['safe'],
'sequence' => $safety['sequence'],
'steps' => $safety['steps'],
'pending' => $safety['pending'],
'work_final' => $safety['work_final'],
'graph' => $graph,
]);
    }

    // Evaluar una solicitud de recursos
    public function requestResources(
    Request $request,
    BankerService $banker,
    ResourceGraphService $graphService
)
 {
        // Validar los datos recibidos
        $data = $request->validate([
            'allocation' => ['required', 'array', 'min:1'],
            'allocation.*' => ['required', 'array', 'min:1'],
            'allocation.*.*' => ['required', 'integer', 'min:0'],

            'maximum' => ['required', 'array', 'min:1'],
            'maximum.*' => ['required', 'array', 'min:1'],
            'maximum.*.*' => ['required', 'integer', 'min:0'],

            'available' => ['required', 'array', 'min:1'],
            'available.*' => ['required', 'integer', 'min:0'],

            'process' => ['required', 'integer', 'min:0'],

            'requested' => ['required', 'array', 'min:1'],
            'requested.*' => ['required', 'integer', 'min:0'],
        ]);

        $allocation = $data['allocation'];
        $maximum = $data['maximum'];
        $available = $data['available'];
        $process = $data['process'];
        $requested = $data['requested'];

        $processes = count($allocation);
        $resources = count($available);

        // Verificar dimensiones y proceso seleccionado
        if (
            !array_is_list($allocation) ||
            !array_is_list($maximum) ||
            !array_is_list($available) ||
            !array_is_list($requested) ||
            count($maximum) !== $processes ||
            count($requested) !== $resources ||
            $process >= $processes
        ) {
            throw ValidationException::withMessages([
                'request' => 'Proceso o dimensiones incorrectas.',
            ]);
        }

        // Calcular la matriz Necesidad
        $need = [];

        for ($i = 0; $i < $processes; $i++) {

            if (
                !array_is_list($allocation[$i]) ||
                !array_is_list($maximum[$i]) ||
                count($allocation[$i]) !== $resources ||
                count($maximum[$i]) !== $resources
            ) {
                throw ValidationException::withMessages([
                    'allocation' => 'Las dimensiones no coinciden.',
                ]);
            }

            $row = [];

            for ($j = 0; $j < $resources; $j++) {

                if ($allocation[$i][$j] > $maximum[$i][$j]) {
                    throw ValidationException::withMessages([
                        'allocation' => 'La asignacion supera el maximo.',
                    ]);
                }

                $row[] = $maximum[$i][$j] - $allocation[$i][$j];
            }

            $need[] = $row;
        }

        // Evaluar la solicitud con el algoritmo del banquero
        $result = $banker->evaluateRequest(
            $allocation,
            $need,
            $available,
            $process,
            $requested
        );
// Construir el grafo incluyendo la solicitud
$graph = $graphService->build(
    $allocation,
    $available,
    $process,
    $requested
);

        // Enviar el resultado al navegador
        return response()->json([
            'success' => true,
            'granted' => $result['granted'],
            'reason' => $result['reason'],
            'sequence' => $result['sequence'],
            'available_after' => $result['available_after'],
	    'graph' => $graph,
        ]);
    }

    // Analizar ciclos con solicitudes simultaneas
    public function analyzeCycles(
        Request $request,
        ResourceGraphService $graphService,
        CycleDetectionService $cycleService
    ) {
        // Validar los datos recibidos
        $data = $request->validate([
            'allocation' => ['required', 'array', 'min:1', 'max:10'],
            'allocation.*' => ['required', 'array', 'min:1', 'max:6'],
            'allocation.*.*' => ['required', 'integer', 'min:0'],

            'maximum' => ['required', 'array', 'min:1', 'max:10'],
            'maximum.*' => ['required', 'array', 'min:1', 'max:6'],
            'maximum.*.*' => ['required', 'integer', 'min:0'],

            'available' => ['required', 'array', 'min:1', 'max:6'],
            'available.*' => ['required', 'integer', 'min:0'],

            'requests' => ['required', 'array', 'min:1', 'max:10'],
            'requests.*' => ['required', 'array', 'min:1', 'max:6'],
            'requests.*.*' => ['required', 'integer', 'min:0'],
        ]);

        $allocation = $data['allocation'];
        $maximum = $data['maximum'];
        $available = $data['available'];
        $requests = $data['requests'];

        $processes = count($allocation);
        $resources = count($available);

        // Validar cantidad de procesos y orden de las filas
        if (
            !array_is_list($allocation) ||
            !array_is_list($maximum) ||
            !array_is_list($available) ||
            !array_is_list($requests) ||
            count($maximum) !== $processes ||
            count($requests) !== $processes
        ) {
            throw ValidationException::withMessages([
                'requests' => 'Las dimensiones de las matrices no coinciden.',
            ]);
        }

        // Verificar todas las filas
        for ($i = 0; $i < $processes; $i++) {

            if (
                !array_is_list($allocation[$i]) ||
                !array_is_list($maximum[$i]) ||
                !array_is_list($requests[$i]) ||
                count($allocation[$i]) !== $resources ||
                count($maximum[$i]) !== $resources ||
                count($requests[$i]) !== $resources
            ) {
                throw ValidationException::withMessages([
                    'requests' => 'Las dimensiones de las filas no coinciden.',
                ]);
            }

            for ($j = 0; $j < $resources; $j++) {

                // La asignacion no debe superar el maximo
                if ($allocation[$i][$j] > $maximum[$i][$j]) {
                    throw ValidationException::withMessages([
                        'allocation' => 'La asignacion supera el maximo.',
                    ]);
                }

                // Calcular necesidad del proceso
                $need = $maximum[$i][$j] - $allocation[$i][$j];

                // Cada solicitud debe respetar la necesidad
                if ($requests[$i][$j] > $need) {
                    throw ValidationException::withMessages([
                        'requests' => 'Una solicitud supera la necesidad del proceso.',
                    ]);
                }
            }
        }

        // Construir el grafo con solicitudes simultaneas
        $graph = $graphService->buildWithRequests(
            $allocation,
            $available,
            $requests
        );

        // Buscar un ciclo mediante DFS
        $result = $cycleService->detect($graph);

        // Interpretar el resultado
        if ($result['has_cycle']) {
            $message = 'Se detecto un ciclo de dependencias. '
                . 'Con recursos de multiples instancias, '
                . 'un ciclo no confirma por si solo un interbloqueo.';
        } else {
            $message = 'No se detectaron ciclos en el grafo.';
        }

        return response()->json([
            'success' => true,
            'has_cycle' => $result['has_cycle'],
            'cycle' => $result['cycle'],
            'message' => $message,
            'graph' => $graph,
        ]);
    }

}
