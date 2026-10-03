<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProcessService;
use App\Models\TestProcess;
use App\Models\Log;

class ProcessController extends Controller
{
    protected ProcessService $processService;

    public function __construct(ProcessService $processService)
    {
        $this->processService = $processService;
    }

    public function index()
    {
        $processes = $this->processService->getProcessList();
        $summary = $this->processService->getStateSummary($processes);
        $whitelistedPids = TestProcess::pluck('pid')->toArray();

        return view('processes.index', compact('processes', 'summary', 'whitelistedPids'));
    }

    public function spawn(Request $request)
    {
        $seconds = (int) $request->input('seconds', 300);
        $escapedSeconds = escapeshellarg($seconds);
        
        $pid = (int) trim(shell_exec("sleep {$escapedSeconds} > /dev/null 2>&1 & echo $!"));

        if ($pid > 0) {
            TestProcess::create([
                'pid' => $pid,
                'command' => "sleep {$seconds}",
            ]);

            Log::create([
                'user_id' => auth()->id() ?? null,
                'action' => 'SPAWN',
                'target_process_pid' => $pid,
                'result' => "Proceso de prueba iniciado con PID {$pid}",
            ]);

            return back()->with('success', "Proceso de prueba creado con PID: {$pid}");
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

            Log::create([
                'user_id' => auth()->id() ?? null,
                'action' => 'KILL',
                'target_process_pid' => $pid,
                'result' => "Proceso PID {$pid} eliminado con SIGKILL (9)",
            ]);

            return back()->with('success', "Proceso PID {$pid} finalizado correctamente.");
        }

        return back()->with('error', "Falló el intento de eliminar el PID {$pid}.");
    }

    public function renice(Request $request)
    {
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
            Log::create([
                'user_id' => auth()->id() ?? null,
                'action' => 'RENICE',
                'target_process_pid' => $pid,
                'result' => "Prioridad cambiada a {$nice} para PID {$pid}",
            ]);

            return back()->with('success', "Prioridad del PID {$pid} modificada a {$nice}.");
        }

        return back()->with('error', "No se pudo modificar la prioridad del PID {$pid}.");
    }
}
