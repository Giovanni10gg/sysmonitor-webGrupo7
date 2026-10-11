<?php

namespace App\Http\Controllers;

use App\Services\BankerService;
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
    BankerService $banker
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
]);
    }
}
