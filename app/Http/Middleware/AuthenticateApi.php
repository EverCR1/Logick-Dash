<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ApiService;

class AuthenticateApi
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verificar si está autenticado
        if (!$this->apiService->isAuthenticated()) {
            return redirect()->route('login')->with('error', 'Por favor inicia sesión');
        }

        // Verificar roles si se especifican
        if (!empty($roles)) {
            $userRole = $this->apiService->getUserRole();
            
            if (!in_array($userRole, $roles)) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tienes permisos para acceder a esta sección');
            }
        }

        // Verificar que el token aún sea válido
        $profileResponse = $this->apiService->getProfile();
        if (!$profileResponse['success']) {
            // Token inválido, cerrar sesión
            $this->apiService->logout();
            return redirect()->route('login')
                ->with('error', 'Tu sesión ha expirado. Por favor inicia sesión nuevamente');
        }

        return $next($request);
    }
}