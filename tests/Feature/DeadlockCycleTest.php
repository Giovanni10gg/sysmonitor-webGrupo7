<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeadlockCycleTest extends TestCase
{
    // PRUEBA 1: Detectar un ciclo con dos solicitudes
    public function test_detecta_ciclo_con_dos_procesos(): void
    {
        $response = $this->postJson('/deadlocks/cycles', [
            'allocation' => [
                [1, 0],
                [0, 1],
            ],
            'maximum' => [
                [1, 1],
                [1, 1],
            ],
            'available' => [0, 0],
            'requests' => [
                [0, 1],
                [1, 0],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('has_cycle', true);

        $response->assertJsonPath('cycle', [
            'P0', 'R1', 'P1', 'R0', 'P0',
        ]);

        $response->assertJsonCount(4, 'graph.edges');
    }

    // PRUEBA 2: Analizar un grafo sin ciclo
    public function test_grafo_sin_ciclo(): void
    {
        $response = $this->postJson('/deadlocks/cycles', [
            'allocation' => [
                [1, 0],
                [0, 1],
            ],
            'maximum' => [
                [1, 1],
                [1, 1],
            ],
            'available' => [0, 0],
            'requests' => [
                [0, 0],
                [1, 0],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('has_cycle', false);
        $response->assertJsonPath('cycle', []);
    }

    // PRUEBA 3: Rechazar solicitudes incorrectas
    public function test_rechaza_solicitud_superior_a_necesidad(): void
    {
        $response = $this->postJson('/deadlocks/cycles', [
            'allocation' => [
                [1, 0],
                [0, 1],
            ],
            'maximum' => [
                [1, 1],
                [1, 1],
            ],
            'available' => [0, 0],
            'requests' => [
                [0, 2],
                [1, 0],
            ],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'requests',
        ]);
    }
}
