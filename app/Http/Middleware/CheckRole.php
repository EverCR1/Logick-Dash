<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = session('user');
        
        Log::info('CheckRole middleware', [
            'user_role' => $user['rol'] ?? 'none',
            'required_roles' => $roles,
            'path' => $request->path()
        ]);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Por favor inicia sesión.');
        }

        if (!in_array($user['rol'], $roles)) {
            Log::warning('Acceso denegado por rol', [
                'user' => $user['email'],
                'role' => $user['rol'],
                'required' => $roles
            ]);
            
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}