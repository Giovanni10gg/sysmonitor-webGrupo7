<?php

namespace Tests\Unit;

use App\Services\BankerService;
use PHPUnit\Framework\TestCase;

class BankerRequestTest extends TestCase
{
    // PRUEBA 1: Solicitud que mantiene el estado seguro
    public function test_concede_solicitud_segura(): void
    {
        $service = new BankerService();

        $result = $service->evaluateRequest(
            [[1, 0], [0, 1]],
            [[1, 1], [1, 1]],
            [1, 1],
            0,
            [1, 0]
        );

        $this->assertTrue($result['granted']);
        $this->assertSame([0, 1], $result['available_after']);
        $this->assertSame(['P0', 'P1'], $result['sequence']);
    }

    // PRUEBA 2: Solicitud mayor que la necesidad
    public function test_rechaza_solicitud_mayor_que_necesidad(): void
    {
        $service = new BankerService();

        $result = $service->evaluateRequest(
            [[1, 0], [0, 1]],
            [[1, 1], [1, 1]],
            [1, 1],
            0,
            [2, 0]
        );

        $this->assertFalse($result['granted']);
    }

    // PRUEBA 3: Recursos insuficientes
    public function test_rechaza_solicitud_sin_recursos_disponibles(): void
    {
        $service = new BankerService();

        $result = $service->evaluateRequest(
            [[1, 0], [0, 1]],
            [[1, 1], [1, 1]],
            [0, 1],
            0,
            [1, 0]
        );

        $this->assertFalse($result['granted']);
    }

    // PRUEBA 4: La solicitud produce un estado inseguro
    public function test_rechaza_solicitud_que_produce_estado_inseguro(): void
    {
        $service = new BankerService();

        $result = $service->evaluateRequest(
            [[0], [1]],
            [[2], [1]],
            [1],
            0,
            [1]
        );

        $this->assertFalse($result['granted']);
        $this->assertSame([1], $result['available_after']);
    }
}
