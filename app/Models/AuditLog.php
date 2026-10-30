<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model {
    protected $fillable = ['username', 'role', 'action', 'target', 'result', 'ip_address'];

    public static function record(string $action, string $target, string $result): void {
        $user = Auth::user();

        self::create([
            'username'   => $user ? $user->name : 'invitado',
            'role'       => $user ? $user->role : 'none',
            'action'     => strtoupper($action),
            'target'     => $target,
            'result'     => strtoupper($result),
            'ip_address' => Request::ip(),
        ]);
    }
}
