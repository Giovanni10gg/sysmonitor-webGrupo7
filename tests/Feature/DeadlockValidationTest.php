<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeadlockValidationTest extends TestCase
{
    // PRUEBA 1: Matrices correctas
    public function test_acepta_matrices_validas(): void
    {
        $response = $this->postJson('/deadlocks/validate', [
            'allocation' => [
                [0, 1, 0],
                [2, 0, 0],
            ],
            'maximum' => [
                [7, 5, 3],
                [3, 2, 2],
            ],
            'available' => [3, 3, 2],
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'success' => true,
            'processes' => 2,
            'resources' => 3,
        ]);
    }

    // PRUEBA 2: Asignacion mayor al maximo
    public function test_rechaza_asignacion_incorrecta(): void
    {
        $response = $this->postJson('/deadlocks/validate', [
            'allocation' => [
                [5, 1],
            ],
            'maximum' => [
                [3, 2],
            ],
            'available' => [2, 1],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'allocation',
        ]);
    }

    // PRUEBA 3: Dimensiones incorrectas
    public function test_rechaza_dimensiones_incorrectas(): void
    {
        $response = $this->postJson('/deadlocks/validate', [
            'allocation' => [
                [1, 0, 2],
            ],
            'maximum' => [
                [3, 2],
            ],
            'available' => [2, 1, 1],
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'allocation',
        ]);
    }
// PRUEBA 4: Calculo de la matriz Necesidad
public function test_calcula_matriz_necesidad(): void
{
    $response = $this->postJson('/deadlocks/validate', [
        'allocation' => [
            [0, 1, 0],
            [2, 0, 0],
        ],
        'maximum' => [
            [7, 5, 3],
            [3, 2, 2],
        ],
        'available' => [3, 3, 2],
    ]);

    $response->assertStatus(200);

    $response->assertJson([
        'need' => [
            [7, 4, 3],
            [1, 2, 2],
        ],
    ]);
  }

    // PRUEBA 5: El sistema se encuentra en estado seguro
    public function test_detecta_estado_seguro(): void
    {
        $response = $this->postJson('/deadlocks/validate', [
            'allocation' => [
                [1, 0],
                [0, 1],
            ],
            'maximum' => [
                [2, 1],
                [1, 2],
            ],
            'available' => [1, 1],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('safe', true);
        $response->assertJsonPath('sequence', ['P0', 'P1']);
    }

    // PRUEBA 6: El sistema se encuentra en estado inseguro
    public function test_detecta_estado_inseguro(): void
    {
        $response = $this->postJson('/deadlocks/validate', [
            'allocation' => [
                [1, 0],
                [0, 1],
            ],
            'maximum' => [
                [2, 1],
                [1, 2],
            ],
            'available' => [0, 0],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('safe', false);
        $response->assertJsonPath('sequence', []);
    }
}

