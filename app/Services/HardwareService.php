<?php

namespace App\Services;

class HardwareService
{
    public function getCpuInfo(): array
    {
        $cpuInfo = file_exists('/proc/cpuinfo') ? file_get_contents('/proc/cpuinfo') : '';
        preg_match_all('/model name\s+:\s+(.+)/', $cpuInfo, $models);
        
        $uptimeSeconds = file_exists('/proc/uptime') ? (float) explode(' ', file_get_contents('/proc/uptime'))[0] : 0;

        return [
            'model' => $models[1][0] ?? 'Procesador Virtual / Genérico',
            'cores' => count($models[1]) ?: 1,
            'uptime' => sprintf('%dd %dh %dm %ds', 
                $uptimeSeconds / 86400, 
                ($uptimeSeconds / 3600) % 24, 
                ($uptimeSeconds / 60) % 60, 
                $uptimeSeconds % 60
            ),
        ];
    }

    public function getCpuUsage(int $sampleIntervalMs = 100000): float
    {
        $stat1 = $this->readProcStat();
        usleep($sampleIntervalMs);
        $stat2 = $this->readProcStat();

        $totalDiff = $stat2['total'] - $stat1['total'];
        $idleDiff = $stat2['idle'] - $stat1['idle'];

        if ($totalDiff === 0) return 0.0;

        return round((1 - ($idleDiff / $totalDiff)) * 100, 2);
    }

    private function readProcStat(): array
    {
        if (!file_exists('/proc/stat')) return ['idle' => 0, 'total' => 0];

        $firstLine = explode("\n", file_get_contents('/proc/stat'))[0];
        $cols = preg_split('/\s+/', trim($firstLine));
        array_shift($cols);

        $idle = (int)($cols[3] ?? 0) + (int)($cols[4] ?? 0);
        $total = array_sum(array_map('intval', $cols));

        return ['idle' => $idle, 'total' => $total];
    }

    public function getMemoryInfo(): array
    {
        if (!file_exists('/proc/meminfo')) {
            return [
                'ram' => ['total_mb' => 0, 'used_mb' => 0, 'free_mb' => 0, 'cached_mb' => 0, 'used_pct' => 0],
                'swap' => ['total_mb' => 0, 'used_mb' => 0, 'free_mb' => 0, 'used_pct' => 0],
                'load_avg' => 'N/A'
            ];
        }

        $meminfo = file_get_contents('/proc/meminfo');
        $data = [];
        foreach (explode("\n", $meminfo) as $line) {
            if (preg_match('/^(\w+):\s+(\d+)/', $line, $matches)) {
                $data[$matches[1]] = (int)$matches[2];
            }
        }

        $memTotal = $data['MemTotal'] ?? 1;
        $memAvailable = $data['MemAvailable'] ?? 0;
        $memFree = $data['MemFree'] ?? 0;
        $cached = $data['Cached'] ?? 0;
        $swapTotal = $data['SwapTotal'] ?? 1;
        $swapFree = $data['SwapFree'] ?? 0;

        return [
            'ram' => [
                'total_mb' => round($memTotal / 1024, 2),
                'used_mb' => round(($memTotal - $memAvailable) / 1024, 2),
                'free_mb' => round($memFree / 1024, 2),
                'cached_mb' => round($cached / 1024, 2),
                'used_pct' => round((($memTotal - $memAvailable) / $memTotal) * 100, 2),
            ],
            'swap' => [
                'total_mb' => round($swapTotal / 1024, 2),
                'used_mb' => round(($swapTotal - $swapFree) / 1024, 2),
                'free_mb' => round($swapFree / 1024, 2),
                'used_pct' => round((($swapTotal - $swapFree) / max($swapTotal, 1)) * 100, 2),
            ],
            'load_avg' => file_exists('/proc/loadavg') ? trim(file_get_contents('/proc/loadavg')) : '0.00 0.00 0.00',
        ];
    }
}
