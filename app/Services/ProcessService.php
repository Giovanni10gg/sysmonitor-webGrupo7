<?php

namespace App\Services;

class ProcessService
{
    public function getProcessList(): array
    {
        $output = shell_exec('ps -eo pid,ppid,user,stat,ni,%cpu,%mem,args --no-headers');
        if (!$output) return [];

        $lines = explode("\n", trim($output));
        $processes = [];

        foreach ($lines as $line) {
            $cols = preg_split('/\s+/', trim($line), 8);
            if (count($cols) < 8) continue;

            $processes[] = [
                'pid' => (int)$cols[0],
                'ppid' => (int)$cols[1],
                'user' => $cols[2],
                'state' => substr($cols[3], 0, 1),
                'nice' => (int)$cols[4],
                'cpu' => (float)$cols[5],
                'mem' => (float)$cols[6],
                'cmd' => $cols[7],
            ];
        }

        return $processes;
    }

    public function getStateSummary(array $processes): array
    {
        $summary = ['R' => 0, 'S' => 0, 'D' => 0, 'Z' => 0, 'T' => 0];
        foreach ($processes as $proc) {
            $state = $proc['state'];
            if (isset($summary[$state])) {
                $summary[$state]++;
            }
        }
        return $summary;
    }

    public function getTopProcesses(array $processes): array
    {
        $topCpu = $processes;
        usort($topCpu, fn($a, $b) => $b['cpu'] <=> $a['cpu']);

        $topMem = $processes;
        usort($topMem, fn($a, $b) => $b['mem'] <=> $a['mem']);

        return [
            'top_cpu' => array_slice($topCpu, 0, 5),
            'top_mem' => array_slice($topMem, 0, 5),
        ];
    }
}
