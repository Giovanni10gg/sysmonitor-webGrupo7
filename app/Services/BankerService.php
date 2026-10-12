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

        // Devolver el resultado del algoritmo del banquero
        return [
            'safe' => count($pending) === 0,
            'sequence' => $sequence,
            'steps' => $steps,
            'pending' => $pending,
            'work_final' => $work,
        ];
    }


    // Evaluar una solicitud de recursos
    public function evaluateRequest(
        array $allocation,
        array $need,
        array $available,
        int $process,
        array $requested
    ): array {

        $resources = count($available);

        // Respuesta comun para solicitudes no concedidas
        $deny = fn (string $reason): array => [
            'granted' => false,
            'reason' => $reason,
            'sequence' => [],
            'available_after' => $available,
        ];

        // Validar proceso y cantidad de recursos
        if (
            !isset($allocation[$process], $need[$process]) ||
            !array_is_list($requested) ||
            count($requested) !== $resources
        ) {
            return $deny('Solicitud invalida.');
        }

        for ($j = 0; $j < $resources; $j++) {

            if (!is_int($requested[$j]) || $requested[$j] < 0) {
                return $deny(
                    'La solicitud debe contener enteros no negativos.'
                );
            }

            // Regla 1: Solicitud <= Necesidad
            if ($requested[$j] > $need[$process][$j]) {
                return $deny(
                    'La solicitud supera la necesidad del proceso.'
                );
            }

            // Regla 2: Solicitud <= Disponible
            if ($requested[$j] > $available[$j]) {
                return $deny(
                    'Recursos insuficientes. El proceso debe esperar.'
                );
            }
        }

        // Copias para simular sin alterar los datos originales
        $trialAllocation = $allocation;
        $trialNeed = $need;
        $trialAvailable = $available;

        // Asignacion provisional
        for ($j = 0; $j < $resources; $j++) {

            $trialAvailable[$j] -= $requested[$j];

            $trialAllocation[$process][$j] += $requested[$j];

            $trialNeed[$process][$j] -= $requested[$j];
        }

        // Regla 3: Comprobar que el estado siga siendo seguro
        $safety = $this->checkSafety(
            $trialAllocation,
            $trialNeed,
            $trialAvailable
        );

        if (!$safety['safe']) {
            return $deny(
                'Solicitud no concedida: produciria un estado inseguro.'
            );
        }

        return [
            'granted' => true,
            'reason' => 'Solicitud concedida. El estado sigue siendo seguro.',
            'sequence' => $safety['sequence'],
            'available_after' => $trialAvailable,
        ];
    }

  }
