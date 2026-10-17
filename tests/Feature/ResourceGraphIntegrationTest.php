<?php

namespace Tests\Feature;

use Tests\TestCase;

class ResourceGraphIntegrationTest extends TestCase
{
    // Comprobar que la validacion devuelve un grafo
    public function test_validacion_devuelve_grafo(): void
    {
        $response = $this->postJson('/deadlocks/validate', [
            'allocation' => [[1, 0], [0, 1]],
            'maximum' => [[2, 1], [1, 2]],
            'available' => [1, 1],
        ]);

        $response->assertStatus(200);

        // Dos procesos y dos recursos
        $response->assertJsonCount(4, 'graph.nodes');

        // Dos asignaciones existentes
        $response->assertJsonCount(2, 'graph.edges');

        $response->assertJsonPath(
            'graph.edges.0.from',
            'R0'
        );

        $response->assertJsonPath(
            'graph.edges.0.to',
            'P0'
        );

        $response->assertJsonPath(
            'graph.nodes.2.total',
            2
        );
    }

    // Comprobar que aparece una flecha de solicitud
    public function test_solicitud_devuelve_grafo(): void
    {
        $response = $this->postJson('/deadlocks/request', [
            'allocation' => [[1, 0], [0, 1]],
            'maximum' => [[2, 1], [1, 2]],
            'available' => [1, 1],
            'process' => 0,
            'requested' => [1, 0],
        ]);

        $response->assertStatus(200);

        // Dos asignaciones y una solicitud
        $response->assertJsonCount(3, 'graph.edges');

        $response->assertJsonPath(
            'graph.edges.2.from',
            'P0'
        );

        $response->assertJsonPath(
            'graph.edges.2.to',
            'R0'
        );

        $response->assertJsonPath(
            'graph.edges.2.type',
            'request'
        );

        $response->assertJsonPath(
            'graph.edges.2.amount',
            1
        );
    }
}
