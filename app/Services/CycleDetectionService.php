<?php

namespace App\Services;

class CycleDetectionService
{
    public function detect(array $graph): array
    {
        // Lista de conexiones entre nodos
        $adjacency = [];

        foreach ($graph['nodes'] as $node) {
            $adjacency[$node['id']] = [];
        }

        // Construir las conexiones dirigidas
        foreach ($graph['edges'] as $edge) {
            $from = $edge['from'];
            $to = $edge['to'];

            if (isset($adjacency[$from], $adjacency[$to])) {
                $adjacency[$from][] = $to;
            }
        }

        // 0 = no visitado
        // 1 = en exploracion
        // 2 = terminado
        $state = array_fill_keys(
            array_keys($adjacency),
            0
        );

        $stack = [];
        $cycle = [];

        // Explorar cada nodo no visitado
        foreach (array_keys($adjacency) as $node) {

            if ($state[$node] === 0) {

                $found = $this->visit(
                    $node,
                    $adjacency,
                    $state,
                    $stack,
                    $cycle
                );

                if ($found) {
                    break;
                }
            }
        }

        return [
            'has_cycle' => count($cycle) > 0,
            'cycle' => $cycle,
        ];
    }

    // Buscar un ciclo mediante recorrido en profundidad
    private function visit(
        string $node,
        array $adjacency,
        array &$state,
        array &$stack,
        array &$cycle
    ): bool {

        $state[$node] = 1;
        $stack[] = $node;

        foreach ($adjacency[$node] as $next) {

            // Nodo que aun no se ha visitado
            if ($state[$next] === 0) {

                if ($this->visit(
                    $next,
                    $adjacency,
                    $state,
                    $stack,
                    $cycle
                )) {
                    return true;
                }

            } elseif ($state[$next] === 1) {

                // Encontramos un nodo de la ruta actual
                $start = array_search(
                    $next,
                    $stack,
                    true
                );

                $cycle = array_slice($stack, $start);
                $cycle[] = $next;

                return true;
            }
        }

        array_pop($stack);
        $state[$node] = 2;

        return false;
    }
}
