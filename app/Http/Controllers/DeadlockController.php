<?php

namespace App\Http\Controllers;

use App\Services\RecoveryService;
use App\Services\CycleDetectionService;
use App\Services\BankerService;
use App\Services\ResourceGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeadlockController extends Controller
{
    public function index()
    {
        return view('deadlocks.index');
    }

    public function validateMatrices(
        Request $request,
        BankerService $banker,
        ResourceGraphService $graphService
    ) {
        $validator = Validator::make($request->all(), [
            'allocation' => ['required', 'array', 'min:1', 'max:10'],
            'allocation.*' => ['required', 'array', 'min:1', 'max:6'],
            'allocation.*.*' => ['required', 'integer', 'min:0'],

            'maximum' => ['required', 'array', 'min:1', 'max:10'],
            'maximum.*' => ['required', 'array', 'min:1', 'max:6'],
            'maximum.*.*' => ['required', 'integer', 'min:0'],

            'available' => ['required', 'array', 'min:1', 'max:6'],
            'available.*' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación en las matrices ingresadas.',
                'errors' => $validator->errors()
            ], 422);
        }

        $allocation = $request->input('allocation');
        $maximum = $request->input('maximum');
        $available = $request->input('available');

        $processes = count($allocation);
        $resources = count($available);

        if ($processes !== count($maximum)) {
            return response()->json([
                'success' => false,
                'message' => 'La cantidad de procesos en Asignación y Máximo no coincide.'
            ], 422);
        }

        for ($i = 0; $i < $processes; $i++) {
            if (
                !isset($maximum[$i]) ||
                count($allocation[$i]) !== $resources ||
                count($maximum[$i]) !== $resources
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las dimensiones de las matrices no coinciden con las dimensiones declaradas.'
                ], 422);
            }

            for ($j = 0; $j < $resources; $j++) {
                if ($allocation[$i][$j] > $maximum[$i][$j]) {
                    return response()->json([
                        'success' => false,
                        'message' => "La asignación no puede superar el máximo en P{$i} - R{$j} (Asignación: {$allocation[$i][$j]} > Máximo: {$maximum[$i][$j]})."
                    ], 422);
                }
            }
        }

        // Calcular la matriz Necesidad (Need = Maximo - Asignacion)
        $need = [];
        for ($i = 0; $i < $processes; $i++) {
            $fila = [];
            for ($j = 0; $j < $resources; $j++) {
                $fila[] = $maximum[$i][$j] - $allocation[$i][$j];
            }
            $need[] = $fila;
        }

        // Ejecutar el algoritmo del banquero
        $safety = $banker->checkSafety($allocation, $need, $available);

        // Construir el grafo de asignacion
        $graph = $graphService->build($allocation, $available);

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

    public function requestResources(
        Request $request,
        BankerService $banker,
        ResourceGraphService $graphService
    ) {
        $validator = Validator::make($request->all(), [
            'allocation' => ['required', 'array', 'min:1', 'max:10'],
            'allocation.*' => ['required', 'array', 'min:1', 'max:6'],
            'allocation.*.*' => ['required', 'integer', 'min:0'],

            'maximum' => ['required', 'array', 'min:1', 'max:10'],
            'maximum.*' => ['required', 'array', 'min:1', 'max:6'],
            'maximum.*.*' => ['required', 'integer', 'min:0'],

            'available' => ['required', 'array', 'min:1', 'max:6'],
            'available.*' => ['required', 'integer', 'min:0'],

            'process' => ['required', 'integer', 'min:0'],

            'requested' => ['required', 'array', 'min:1', 'max:6'],
            'requested.*' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de solicitud inválidos.',
                'errors' => $validator->errors()
            ], 422);
        }

        $allocation = $request->input('allocation');
        $maximum = $request->input('maximum');
        $available = $request->input('available');
        $process = $request->input('process');
        $requested = $request->input('requested');

        $processes = count($allocation);
        $resources = count($available);

        if ($process >= $processes || count($requested) !== $resources) {
            return response()->json([
                'success' => false,
                'message' => 'El proceso seleccionado o las dimensiones del vector son incorrectos.'
            ], 422);
        }

        $need = [];
        for ($i = 0; $i < $processes; $i++) {
            $row = [];
            for ($j = 0; $j < $resources; $j++) {
                if ($allocation[$i][$j] > $maximum[$i][$j]) {
                    return response()->json([
                        'success' => false,
                        'message' => "La asignación supera el máximo en P{$i} - R{$j}."
                    ], 422);
                }
                $row[] = $maximum[$i][$j] - $allocation[$i][$j];
            }
            $need[] = $row;
        }

        $result = $banker->evaluateRequest($allocation, $need, $available, $process, $requested);
        $graph = $graphService->build($allocation, $available, $process, $requested);

        return response()->json([
            'success' => true,
            'granted' => $result['granted'],
            'reason' => $result['reason'],
            'sequence' => $result['sequence'] ?? [],
            'available_after' => $result['available_after'] ?? $available,
            'graph' => $graph,
        ]);
    }

    public function analyzeCycles(
        Request $request,
        ResourceGraphService $graphService,
        CycleDetectionService $cycleService,
        RecoveryService $recoveryService
    ) {
        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Matriz de solicitudes pendientes inválida.',
                'errors' => $validator->errors()
            ], 422);
        }

        $allocation = $request->input('allocation');
        $maximum = $request->input('maximum');
        $available = $request->input('available');
        $requests = $request->input('requests');

        $processes = count($allocation);
        $resources = count($available);

        for ($i = 0; $i < $processes; $i++) {
            for ($j = 0; $j < $resources; $j++) {
                if ($allocation[$i][$j] > $maximum[$i][$j]) {
                    return response()->json([
                        'success' => false,
                        'message' => "La asignación supera el máximo en P{$i} - R{$j}."
                    ], 422);
                }

                $need = $maximum[$i][$j] - $allocation[$i][$j];
                if ($requests[$i][$j] > $need) {
                    return response()->json([
                        'success' => false,
                        'message' => "La solicitud en P{$i} - R{$j} ({$requests[$i][$j]}) supera la necesidad ({$need})."
                    ], 422);
                }
            }
        }

        $graph = $graphService->buildWithRequests($allocation, $available, $requests);
        $result = $cycleService->detect($graph);
        $recovery = $recoveryService->analyze($allocation, $available, $requests, $result['cycle']);

        $message = $result['has_cycle']
            ? 'Se detectó un ciclo de dependencias en el grafo.'
            : 'No se detectaron ciclos en el grafo.';

        return response()->json([
            'success' => true,
            'has_cycle' => $result['has_cycle'],
            'cycle' => $result['cycle'],
            'message' => $message,
            'graph' => $graph,
            'recovery' => $recovery,
        ]);
    }
}
