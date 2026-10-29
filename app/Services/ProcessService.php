<?php

namespace App\Services;

class ProcessService
{
    public function getProcessList(): array
    {
        // Obtenemos 'rss' (Resident Set Size en KB) para calcular la memoria real consumida en MB
        $output = shell_exec('ps -eo pid,ppid,user,stat,ni,rss,%cpu,%mem,args --no-headers');
        if (!$output) return [];

        $lines = explode("\n", trim($output));
        $processes = [];

        foreach ($lines as $line) {
            $cols = preg_split('/\s+/', trim($line), 9);
            if (count($cols) < 9) continue;

            $rssKb = (int)$cols[5];
            $memMb = round($rssKb / 1024, 1);

            $processes[] = [
                'pid'     => (int)$cols[0],
                'ppid'    => (int)$cols[1],
                'user'    => $cols[2],
                'state'   => substr($cols[3], 0, 1),
                'stat'    => $cols[3],
                'nice'    => (int)$cols[4],
                'rss_kb'  => $rssKb,
                'mem_mb'  => $memMb > 0 ? $memMb : 0.1, // Muestra al menos 0.1 MB para procesos livianos como sleep
                'cpu'     => (float)$cols[6],
                'mem'     => (float)$cols[7],
                'cmd'     => $cols[8],
                'command' => $cols[8],
            ];
        }

        return $processes;
    }

    public function getStateSummary(array $processes): array
    {
        $summary = ['R' => 0, 'S' => 0, 'D' => 0, 'Z' => 0, 'T' => 0, 'total' => count($processes)];
        foreach ($processes as $proc) {
            $state = $proc['state'] ?? 'S';
            if (isset($summary[$state])) {
                $summary[$state]++;
            } else {
                $summary['S']++;
            }
        }
        return $summary;
    }

    public function getTopProcesses(array $processes): array
    {
        $topCpu = $processes;
        usort($topCpu, fn($a, $b) => $b['cpu'] <=> $a['cpu']);

        $topMem = $processes;
        usort($topMem, fn($a, $b) => $b['mem_mb'] <=> $a['mem_mb']);

        return [
            'top_cpu' => array_slice($topCpu, 0, 5),
            'top_mem' => array_slice($topMem, 0, 5),
        ];
    }
}
