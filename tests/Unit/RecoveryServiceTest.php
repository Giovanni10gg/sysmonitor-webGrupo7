<?php

namespace Tests\Unit;

use App\Services\RecoveryService;
use PHPUnit\Framework\TestCase;

class RecoveryServiceTest extends TestCase
{
    // PRUEBA 1: Terminar P0 permite finalizar P1
    public function test_recupera_interbloqueo(): void
    {
        $service = new RecoveryService();

        $result = $service->analyze(
            [[1, 0], [0, 1]],
            [0, 0],
            [[0, 1], [1, 0]],
            ['P0', 'R1', 'P1', 'R0', 'P0']
        );

        $this->assertTrue($result['recovery_needed']);
        $this->assertSame(
            ['P0', 'P1'],
            $result['pending_before']
        );

        $this->assertSame(
            'P0',
            $result['recommended']['process']
        );

        $this->assertTrue(
            $result['recommended']['resolved']
        );

        $this->assertSame(
            [1, 0],
            $result['recommended']['released']
        );

        $this->assertSame(
            [],
            $result['recommended']['pending_after']
        );

        $this->assertSame(
            [1, 1],
            $result['recommended']['available_after']
        );
    }

    // PRUEBA 2: Con recursos suficientes no hace falta recuperar
    public function test_no_recupera_si_pueden_terminar(): void
    {
        $service = new RecoveryService();

        $result = $service->analyze(
            [[1, 0], [0, 1]],
            [1, 0],
            [[0, 1], [1, 0]],
            ['P0', 'R1', 'P1', 'R0', 'P0']
        );

        $this->assertFalse($result['recovery_needed']);
        $this->assertNull($result['recommended']);
    }

    // PRUEBA 3: No recomendar sin ciclo
    public function test_no_recupera_sin_ciclo(): void
    {
        $service = new RecoveryService();

        $result = $service->analyze(
            [[1, 0], [0, 1]],
            [1, 1],
            [[0, 0], [0, 0]],
            []
        );

        $this->assertFalse($result['recovery_needed']);
        $this->assertSame([], $result['options']);
    }

    // PRUEBA 4: Una sola terminacion no siempre es suficiente
    public function test_no_promete_recuperacion_insuficiente(): void
    {
        $service = new RecoveryService();

        $result = $service->analyze(
            [
                [1, 0, 0, 0],
                [0, 1, 0, 0],
                [0, 0, 1, 0],
                [0, 0, 0, 1],
            ],
            [0, 0, 0, 0],
            [
                [0, 1, 0, 0],
                [1, 0, 0, 0],
                [0, 0, 0, 1],
                [0, 0, 1, 0],
            ],
            ['P0', 'R1', 'P1', 'R0', 'P0']
        );

        $this->assertTrue($result['recovery_needed']);
        $this->assertCount(2, $result['options']);
        $this->assertNull($result['recommended']);
    }
}
