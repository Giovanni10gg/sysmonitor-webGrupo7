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

        // Recursos de trabajo disponibles
        $work = $available;

        // Inicialmente ningun proceso ha terminado
        $finish = array_fill(0, $processes, false);

        // Secuencia de procesos que pueden finalizar
        $sequence = [];

        while (count($sequence) < $processes) {

            $progress = false;

            for ($i = 0; $i < $processes; $i++) {

                if ($finish[$i]) {
                    continue;
                }

                $canFinish = true;

                // Verificar si hay suficientes recursos
                for ($j = 0; $j < $resources; $j++) {

                    if ($need[$i][$j] > $work[$j]) {
                        $canFinish = false;
                        break;
                    }
                }

                if (!$canFinish) {
                    continue;
                }

                // El proceso termina y libera sus recursos
                for ($j = 0; $j < $resources; $j++) {
                    $work[$j] += $allocation[$i][$j];
                }

                $finish[$i] = true;
                $sequence[] = "P{$i}";
                $progress = true;
            }

            // No existe otro proceso que pueda avanzar
            if (!$progress) {
                break;
            }
        }

        return [
            'safe' => count($sequence) === $processes,
            'sequence' => $sequence,
        ];
    }
}
