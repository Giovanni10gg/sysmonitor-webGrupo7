<?php

namespace Tests\Unit;

use App\Services\ResourceGraphService;
use App\Services\CycleDetectionService;
use PHPUnit\Framework\TestCase;

class ResourceGraphMultipleRequestsTest extends TestCase
{
    // PRUEBA 1: Dos procesos forman un ciclo
    public function test_detecta_ciclo_con_dos_solicitudes(): void
    {
        $graphService = new ResourceGraphService();
        $cycleService = new CycleDetectionService();

        // R0 asignado a P0 y R1 asignado a P1
        $allocation = [
            [1, 0],
            [0, 1],
        ];

        // No quedan recursos disponibles
        $available = [0, 0];

        // P0 solicita R1 y P1 solicita R0
        $requests = [
            [0, 1],
            [1, 0],
        ];

        $graph = $graphService->buildWithRequests(
            $allocation,
            $available,
            $requests
        );

        // Dos procesos y dos recursos
        $this->assertCount(4, $graph['nodes']);

        // Dos asignaciones y dos solicitudes
        $this->assertCount(4, $graph['edges']);

        $this->assertSame([
            'from' => 'P0',
            'to' => 'R1',
            'type' => 'request',
            'amount' => 1,
        ], $graph['edges'][2]);

        $this->assertSame([
            'from' => 'P1',
            'to' => 'R0',
            'type' => 'request',
            'amount' => 1,
        ], $graph['edges'][3]);

        // Detectar el ciclo
        $result = $cycleService->detect($graph);

        $this->assertTrue($result['has_cycle']);

        $this->assertSame(
            ['P0', 'R1', 'P1', 'R0', 'P0'],
            $result['cycle']
        );
    }

    // PRUEBA 2: Una solicitud aislada no forma ciclo
    public function test_solicitud_sin_ciclo(): void
    {
        $graphService = new ResourceGraphService();
        $cycleService = new CycleDetectionService();

        $allocation = [
            [1, 0],
            [0, 1],
        ];

        $available = [1, 1];

        $requests = [
            [0, 0],
            [1, 0],
        ];

        $graph = $graphService->buildWithRequests(
            $allocation,
            $available,
            $requests
        );

        $result = $cycleService->detect($graph);

        $this->assertFalse($result['has_cycle']);
        $this->assertSame([], $result['cycle']);
    }
}
