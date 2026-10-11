<?php

namespace App\Services;

class BankerService
{
    public function checkSafety(
        array $allocation,
        array $need,
        array $available
    ): array {

        $processes = count($allocation);
        $resources = count($available);

        // Recursos disponibles para la simulacion
        $work = $available;

        // Estado inicial de cada proceso
        $finish = array_fill(0, $processes, false);

        // Orden de finalizacion
        $sequence = [];

        // Registro de cada paso
        $steps = [];

        while (count($sequence) < $processes) {

            $progress = false;

            for ($i = 0; $i < $processes; $i++) {

                if ($finish[$i]) {
                    continue;
                }

                $canFinish = true;

                // Comprobar si el proceso puede terminar
                for ($j = 0; $j < $resources; $j++) {

                    if ($need[$i][$j] > $work[$j]) {
                        $canFinish = false;
                        break;
                    }
                }

                if (!$canFinish) {
                    continue;
                }

                // Guardar recursos antes de finalizar
                $workBefore = $work;

                // Liberar recursos asignados al proceso
                for ($j = 0; $j < $resources; $j++) {
                    $work[$j] += $allocation[$i][$j];
                }

                $finish[$i] = true;
                $sequence[] = "P{$i}";
                $progress = true;

                // Registrar el paso realizado
                $steps[] = [
                    'process' => "P{$i}",
                    'work_before' => $workBefore,
                    'need' => $need[$i],
                    'released' => $allocation[$i],
                    'work_after' => $work,
                ];
            }

            // Si ninguno puede avanzar, detener
            if (!$progress) {
                break;
            }
        }

        // Identificar los procesos pendientes
        $pending = [];

        for ($i = 0; $i < $processes; $i++) {
            if (!$finish[$i]) {
                $pending[] = "P{$i}";
            }
        }

        return [
            'safe' => count($pending) === 0,
            'sequence' => $sequence,
            'steps' => $steps,
            'pending' => $pending,
            'work_final' => $work,
        ];
    }
}

