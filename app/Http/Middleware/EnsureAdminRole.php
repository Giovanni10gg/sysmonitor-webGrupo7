<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole {
    public function handle(Request $request, Closure $next): Response {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            AuditLog::record(
                $request->route() ? $request->route()->getName() : $request->path(),
                $request->input('pid') ? 'PID ' . $request->input('pid') : 'Acción administrativa',
                'DENIED'
            );

            return redirect()->back()->with('error', 'Acceso denegado: Se requieren permisos de Administrador.');
        }

        return $next($request);
    }
}
