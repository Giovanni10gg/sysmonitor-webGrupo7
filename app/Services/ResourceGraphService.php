<?php

namespace App\Services;

class ResourceGraphService
{
    public function build(
        array $allocation,
        array $available,
        ?int $process = null,
        ?array $requested = null
    ): array {

        $processes = count($allocation);
        $resources = count($available);

        $nodes = [];
        $edges = [];

        // Crear nodos de procesos
        for ($i = 0; $i < $processes; $i++) {
            $nodes[] = [
                'id' => "P{$i}",
                'type' => 'process',
                'label' => "P{$i}",
            ];
        }

        // Crear nodos de recursos
        for ($j = 0; $j < $resources; $j++) {

            $assigned = 0;

            for ($i = 0; $i < $processes; $i++) {
                $assigned += $allocation[$i][$j];
            }

            $nodes[] = [
                'id' => "R{$j}",
                'type' => 'resource',
                'label' => "R{$j}",
                'available' => $available[$j],
                'total' => $assigned + $available[$j],
            ];
        }

        // Flechas de asignacion: Recurso -> Proceso
        for ($i = 0; $i < $processes; $i++) {

            for ($j = 0; $j < $resources; $j++) {

                if ($allocation[$i][$j] > 0) {

                    $edges[] = [
                        'from' => "R{$j}",
                        'to' => "P{$i}",
                        'type' => 'allocation',
                        'amount' => $allocation[$i][$j],
                    ];
                }
            }
        }

        // Flechas de solicitud simulada: Proceso -> Recurso
        if ($process !== null && $requested !== null) {

            for ($j = 0; $j < $resources; $j++) {

                if ($requested[$j] > 0) {

                    $edges[] = [
                        'from' => "P{$process}",
                        'to' => "R{$j}",
                        'type' => 'request',
                        'amount' => $requested[$j],
                    ];
                }
            }
        }

        return [
            'nodes' => $nodes,
            'edges' => $edges,
        ];
    }

    // Construir un grafo con solicitudes de varios procesos
    public function buildWithRequests(
        array $allocation,
        array $available,
        array $requests
    ): array {

        // Crear el grafo con las asignaciones actuales
        $graph = $this->build($allocation, $available);

        $processes = count($allocation);
        $resources = count($available);

        // Recorrer las solicitudes de cada proceso
        for ($i = 0; $i < $processes; $i++) {

            for ($j = 0; $j < $resources; $j++) {

                $amount = $requests[$i][$j];

                if ($amount > 0) {

                    // Flecha: Proceso -> Recurso
                    $graph['edges'][] = [
                        'from' => "P{$i}",
                        'to' => "R{$j}",
                        'type' => 'request',
                        'amount' => $amount,
                    ];
                }
            }
        }

        return $graph;
    }

}
