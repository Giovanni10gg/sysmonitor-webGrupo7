<?php

namespace Tests\Unit;

use App\Services\ResourceGraphService;
use PHPUnit\Framework\TestCase;

class ResourceGraphTest extends TestCase
{
    // Verificar nodos y recursos disponibles
    public function test_crea_nodos_del_grafo(): void
    {
        $service = new ResourceGraphService();

        $graph = $service->build(
            [[1, 0], [0, 1]],
            [1, 1]
        );

        $this->assertCount(4, $graph['nodes']);

        $this->assertSame('P0', $graph['nodes'][0]['id']);
        $this->assertSame('P1', $graph['nodes'][1]['id']);

        $this->assertSame('R0', $graph['nodes'][2]['id']);
        $this->assertSame(1, $graph['nodes'][2]['available']);
        $this->assertSame(2, $graph['nodes'][2]['total']);
    }

    // Verificar las asignaciones existentes
    public function test_crea_flechas_de_asignacion(): void
    {
        $service = new ResourceGraphService();

        $graph = $service->build(
            [[1, 0], [0, 1]],
            [1, 1]
        );

        $this->assertCount(2, $graph['edges']);

        $this->assertSame([
            'from' => 'R0',
            'to' => 'P0',
            'type' => 'allocation',
            'amount' => 1,
        ], $graph['edges'][0]);
    }

    // Verificar una solicitud simulada
    public function test_crea_flechas_de_solicitud(): void
    {
        $service = new ResourceGraphService();

        $graph = $service->build(
            [[1, 0], [0, 1]],
            [1, 1],
            0,
            [1, 0]
        );

        $this->assertCount(3, $graph['edges']);

        $this->assertSame([
            'from' => 'P0',
            'to' => 'R0',
            'type' => 'request',
            'amount' => 1,
        ], $graph['edges'][2]);
    }
}
