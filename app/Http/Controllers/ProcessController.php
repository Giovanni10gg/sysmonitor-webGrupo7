<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProcessService;
use App\Models\TestProcess;
use App\Models\Log;
use App\Models\User;

class ProcessController extends Controller
{
    protected ProcessService $processService;

    public function __construct(ProcessService $processService)
    {
        $this->processService = $processService;
    }

    public function index(Request $request)
    {
        $processes = $this->processService->getProcessList();

        // Filtro de búsqueda
        $search = strtolower(trim($request->input('search', '')));
        if (!empty($search)) {
            $processes = array_filter($processes, function ($proc) use ($search) {
                $pid = (string) ($proc['pid'] ?? '');
                $user = (string) ($proc['user'] ?? '');
                $cmd = (string) ($proc['cmd'] ?? $proc['command'] ?? '');

                return str_contains(strtolower($pid), $search) ||
                       str_contains(strtolower($user), $search) ||
                       str_contains(strtolower($cmd), $search);
            });
        }

        $summary = $this->processService->getStateSummary($processes);
        $whitelistedPids = TestProcess::pluck('pid')->toArray();

        // Carga directa de logs sin requerir relaciones en el Modelo
        $spawnLogs = Log::where('action', 'SPAWN')
            ->get()
            ->keyBy('target_process_pid');

        $users = User::pluck('name', 'id')->toArray();

        return view('processes.index', compact('processes', 'summary', 'whitelistedPids', 'spawnLogs', 'users'));
    }

    public function spawn(Request $request)
    {
        $seconds = (int) ($request->input('seconds') ?? $request->input('command', 300));
        if ($seconds <= 0) $seconds = 300;

        $escapedSeconds = escapeshellarg($seconds);
        $pid = (int) trim(shell_exec("sleep {$escapedSeconds} > /dev/null 2>&1 & echo $!"));

        if ($pid > 0) {
            $user = auth()->user();
            $userName = $user->name ?? 'Usuario';
            $userRole = strtoupper($user->role ?? 'ADMIN');

            TestProcess::create([
                'pid' => $pid,
                'command' => "sleep {$seconds}",
            ]);

            Log::create([
                'user_id' => auth()->id() ?? null,
                'action' => 'SPAWN',
                'target_process_pid' => $pid,
                'result' => "Proceso iniciado por {$userName} ({$userRole}) con PID {$pid}",
            ]);

            return back()->with('success', "Proceso de prueba iniciado por {$userName} ({$userRole}) con PID: {$pid}");
        }

        return back()->with('error', 'Error al intentar iniciar el proceso de prueba.');
    }

    public function kill(Request $request)
    {
        $request->validate(['pid' => 'required|integer']);
        $pid = (int) $request->input('pid');

        $testProc = TestProcess::where('pid', $pid)->first();

        if (!$testProc) {
            Log::create([
                'user_id' => auth()->id() ?? null,
                'action' => 'KILL_DENIED',
                'target_process_pid' => $pid,
                'result' => "Acceso denegado: El PID {$pid} no está en la lista blanca.",
            ]);

            return back()->with('error', "Acción denegada. El proceso {$pid} no pertenece a la app.");
        }

        $escapedPid = escapeshellarg($pid);
        exec("kill -9 {$escapedPid}", $output, $returnCode);

        if ($returnCode === 0) {
            $testProc->delete();

            $userName = auth()->user()->name ?? 'Usuario';
            Log::create([
                'user_id' => auth()->id() ?? null,
                'action' => 'KILL',
                'target_process_pid' => $pid,
                'result' => "Proceso PID {$pid} eliminado con SIGKILL (9) por {$userName}",
            ]);

            return back()->with('success', "Proceso PID {$pid} finalizado correctamente.");
        }

        return back()->with('error', "Falló el intento de eliminar el PID {$pid}.");
    }

    public function renice(Request $request)
    {
        $nice = $request->input('nice') ?? $request->input('priority');
        $request->merge(['nice' => $nice]);

        $request->validate([
            'pid' => 'required|integer',
            'nice' => 'required|integer|between:-20,19',
        ]);

        $pid = (int) $request->input('pid');
        $nice = (int) $request->input('nice');

        $testProc = TestProcess::where('pid', $pid)->first();

        if (!$testProc) {
            return back()->with('error', "Acción denegada. El proceso {$pid} no está en la lista blanca.");
        }

        $escapedPid = escapeshellarg($pid);
        $escapedNice = escapeshellarg($nice);

        exec("renice -n {$escapedNice} -p {$escapedPid}", $output, $returnCode);

        if ($returnCode === 0) {
            $userName = auth()->user()->name ?? 'Usuario';

            Log::create([
                'user_id' => auth()->id() ?? null,
                'action' => 'RENICE',
                'target_process_pid' => $pid,
                'result' => "Prioridad cambiada a {$nice} para PID {$pid} por {$userName}",
            ]);

            return back()->with('success', "Prioridad del PID {$pid} modificada a {$nice}.");
        }

        return back()->with('error', "No se pudo modificar la prioridad del PID {$pid}.");
    }
}
