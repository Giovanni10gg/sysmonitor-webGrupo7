<?php

namespace Tests\Unit;

use App\Services\CycleDetectionService;
use App\Services\ResourceGraphService;
use PHPUnit\Framework\TestCase;

class CycleDetectionTest extends TestCase
{
    // PRUEBA 1: Un grafo sin solicitudes no tiene ciclos
    public function test_grafo_sin_ciclos(): void
    {
        $graphService = new ResourceGraphService();
        $cycleService = new CycleDetectionService();

        $graph = $graphService->build(
            [[1, 0], [0, 1]],
            [1, 1]
        );

        $result = $cycleService->detect($graph);

        $this->assertFalse($result['has_cycle']);
        $this->assertSame([], $result['cycle']);
    }

    // PRUEBA 2: Ciclo entre P0 y P1
    public function test_detecta_ciclo_entre_procesos(): void
    {
        $graphService = new ResourceGraphService();
        $cycleService = new CycleDetectionService();

        // R0 asignado a P0 y R1 asignado a P1
        $graph = $graphService->build(
            [[1, 0], [0, 1]],
            [0, 0]
        );

        // P0 solicita R1
        $graph['edges'][] = [
            'from' => 'P0',
            'to' => 'R1',
            'type' => 'request',
            'amount' => 1,
        ];

        // P1 solicita R0
        $graph['edges'][] = [
            'from' => 'P1',
            'to' => 'R0',
            'type' => 'request',
            'amount' => 1,
        ];

        $result = $cycleService->detect($graph);

        $this->assertTrue($result['has_cycle']);

        $this->assertSame(
            ['P0', 'R1', 'P1', 'R0', 'P0'],
            $result['cycle']
        );
    }

    // PRUEBA 3: Un ciclo no siempre implica interbloqueo
    public function test_detecta_ciclo_con_multiples_instancias(): void
    {
        $graphService = new ResourceGraphService();
        $cycleService = new CycleDetectionService();

        // Dos instancias de R0:
        // una asignada y otra disponible
        $graph = $graphService->build(
            [[1]],
            [1],
            0,
            [1]
        );

        $result = $cycleService->detect($graph);

        $this->assertTrue($result['has_cycle']);

        $this->assertSame(
            ['P0', 'R0', 'P0'],
            $result['cycle']
        );

        // La presencia del ciclo es estructural.
        // No afirma que exista un interbloqueo.
        $this->assertSame(
            1,
            $graph['nodes'][1]['available']
        );
    }
}
