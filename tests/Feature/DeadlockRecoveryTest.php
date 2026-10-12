<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeadlockRecoveryTest extends TestCase
{
    // PRUEBA 1: Recomendar finalizar P0
    public function test_recomienda_recuperacion_simulada(): void
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

        $response->assertJsonPath(
            'recovery.recovery_needed',
            true
        );

        $response->assertJsonPath(
            'recovery.recommended.process',
            'P0'
        );

        $response->assertJsonPath(
            'recovery.recommended.released',
            [1, 0]
        );

        $response->assertJsonPath(
            'recovery.recommended.pending_after',
            []
        );

        $response->assertJsonPath(
            'recovery.recommended.steps.0.action',
            'abort'
        );

        $response->assertJsonPath(
            'recovery.recommended.steps.1.process',
            'P1'
        );
    }

    // PRUEBA 2: Un ciclo no siempre requiere recuperacion
    public function test_no_recupera_si_hay_recursos_suficientes(): void
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
            'available' => [1, 0],
            'requests' => [
                [0, 1],
                [1, 0],
            ],
        ]);

        $response->assertStatus(200);

        $response->assertJsonPath('has_cycle', true);

        $response->assertJsonPath(
            'recovery.recovery_needed',
            false
        );

        $response->assertJsonPath(
            'recovery.recommended',
            null
        );
    }

    // PRUEBA 3: Sin ciclo no hay recomendacion
    public function test_no_recomienda_recuperacion_sin_ciclo(): void
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
                [0, 0],
            ],
        ]);

        $response->assertStatus(200);

        $response->assertJsonPath('has_cycle', false);

        $response->assertJsonPath(
            'recovery.recovery_needed',
            false
        );

        $response->assertJsonPath(
            'recovery.recommended',
            null
        );
    }
}
