<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HardwareService;

class HardwareController extends Controller
{
    protected HardwareService $hardwareService;

    public function __construct(HardwareService $hardwareService)
    {
        $this->hardwareService = $hardwareService;
    }

    public function index()
    {
        $cpuInfo = $this->hardwareService->getCpuInfo();
        // Muestra de 100,000 microsegundos (0.1s) para no demorar la carga de la página
        $cpuUsage = $this->hardwareService->getCpuUsage(100000); 
        $memoryInfo = $this->hardwareService->getMemoryInfo();

        return view('hardware.index', compact('cpuInfo', 'cpuUsage', 'memoryInfo'));
    }
}
