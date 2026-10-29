<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller {
    public function index(Request $request) {
        $query = AuditLog::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('target', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(15);

        return view('audit.index', compact('logs'));
    }
}
