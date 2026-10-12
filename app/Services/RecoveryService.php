<?php

namespace App\Services;

class RecoveryService
{
    // Analizar posibles terminaciones de procesos
    public function analyze(
        array $allocation,
        array $available,
        array $requests,
        array $cycle
    ): array {

        // Comprobar el estado antes de recuperar
        $initial = $this->simulate(
            $allocation,
            $available,
            $requests,
            null
        );

        // Si no hay ciclo o los procesos pueden terminar
        if (count($cycle) === 0 ||
            count($initial['pending']) === 0) {

            return [
                'recovery_needed' => false,
                'pending_before' => $initial['pending'],
                'options' => [],
                'recommended' => null,
            ];
        }

        // Identificar los procesos que forman el ciclo
        $participants = [];

        foreach ($cycle as $node) {

            if (preg_match('/^P([0-9]+)$/', $node, $matches)) {

                $index = (int) $matches[1];

                if (
                    isset($allocation[$index]) &&
                    in_array("P{$index}", $initial['pending'], true) &&
                    !in_array($index, $participants, true)
                ) {
                    $participants[] = $index;
                }
            }
        }

        $options = [];
        $recommended = null;

        // Simular la terminacion de cada proceso participante
        foreach ($participants as $process) {

            $released = $allocation[$process];

            // No tiene recursos asignados para liberar
            if (array_sum($released) === 0) {
                continue;
            }

            $simulation = $this->simulate(
                $allocation,
                $available,
                $requests,
                $process
            );

            $resolved = count($simulation['pending']) === 0;

            $option = [
                'process' => "P{$process}",
                'released' => $released,
                'resolved' => $resolved,
                'pending_after' => $simulation['pending'],
                'available_after' => $simulation['available_after'],
                'steps' => $simulation['steps'],
            ];

            $options[] = $option;

            // Preferir una solucion que libere menos recursos
            if ($resolved) {

                if (
                    $recommended === null ||
                    array_sum($released) <
                    array_sum($recommended['released'])
                ) {
                    $recommended = $option;
                }
            }
        }

        return [
            'recovery_needed' => true,
            'pending_before' => $initial['pending'],
            'options' => $options,
            'recommended' => $recommended,
        ];
    }

    // Simular la finalizacion y liberacion de recursos
    private function simulate(
        array $allocation,
        array $available,
        array $requests,
        ?int $aborted
    ): array {

        $processes = count($allocation);
        $resources = count($available);

        $work = $available;
        $finish = array_fill(0, $processes, false);
        $steps = [];

        // Terminacion simulada del proceso seleccionado
        if ($aborted !== null) {

            $finish[$aborted] = true;

            for ($j = 0; $j < $resources; $j++) {
                $work[$j] += $allocation[$aborted][$j];
            }

            $steps[] = [
                'action' => 'abort',
                'process' => "P{$aborted}",
                'released' => $allocation[$aborted],
                'available_after' => $work,
            ];
        }

        // Comprobar que procesos pueden continuar
        do {

            $progress = false;

            for ($i = 0; $i < $processes; $i++) {

                if ($finish[$i]) {
                    continue;
                }

                $canFinish = true;

                for ($j = 0; $j < $resources; $j++) {

                    if ($requests[$i][$j] > $work[$j]) {
                        $canFinish = false;
                        break;
                    }
                }

                if (!$canFinish) {
                    continue;
                }

                // Simular que el proceso termina y libera recursos
                for ($j = 0; $j < $resources; $j++) {
                    $work[$j] += $allocation[$i][$j];
                }

                $finish[$i] = true;
                $progress = true;

                $steps[] = [
                    'action' => 'finish',
                    'process' => "P{$i}",
                    'released' => $allocation[$i],
                    'available_after' => $work,
                ];
            }

        } while ($progress);

        $pending = [];

        for ($i = 0; $i < $processes; $i++) {

            if (!$finish[$i]) {
                $pending[] = "P{$i}";
            }
        }

        return [
            'pending' => $pending,
            'available_after' => $work,
            'steps' => $steps,
        ];
    }
}
