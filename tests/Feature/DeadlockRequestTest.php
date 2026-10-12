<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeadlockRequestTest extends TestCase
{
    // Solicitud segura
    public function test_ruta_concede_solicitud_segura(): void
    {
        $response = $this->postJson('/deadlocks/request', [
            'allocation' => [[1, 0], [0, 1]],
            'maximum' => [[2, 1], [1, 2]],
            'available' => [1, 1],
            'process' => 0,
            'requested' => [1, 0],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('granted', true);
        $response->assertJsonPath('available_after', [0, 1]);
        $response->assertJsonPath('sequence', ['P0', 'P1']);
    }

    // Solicitud que deja el sistema inseguro
    public function test_ruta_rechaza_solicitud_insegura(): void
    {
        $response = $this->postJson('/deadlocks/request', [
            'allocation' => [[0], [1]],
            'maximum' => [[2], [2]],
            'available' => [1],
            'process' => 0,
            'requested' => [1],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('granted', false);
        $response->assertJsonPath('available_after', [1]);
    }

    // Proceso inexistente
    public function test_ruta_rechaza_proceso_inexistente(): void
    {
        $response = $this->postJson('/deadlocks/request', [
            'allocation' => [[1, 0], [0, 1]],
            'maximum' => [[2, 1], [1, 2]],
            'available' => [1, 1],
            'process' => 5,
            'requested' => [1, 0],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['request']);
    }
}
