<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class AuthController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if ($this->apiService->isAuthenticated()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required|min:1',
        ], [
            'email.required' => 'El email o username es requerido',
            'password.required' => 'La contraseña es requerida',
        ]);

        \Log::info('Login attempt from form:', [
            'email' => $request->email,
            'password_length' => strlen($request->password),
        ]);

        $result = $this->apiService->login(
            $request->email, 
            $request->password,
            $request->email // Usar el mismo valor para username si es necesario
        );

        if ($result['success']) {
            \Log::info('Login successful, redirecting to dashboard');
            return redirect()->route('dashboard')->with('success', 'Bienvenido al sistema');
        }

        \Log::warning('Login failed:', $result);

        return back()->withErrors([
            'login' => $result['error'] ?? 'Credenciales incorrectas',
        ])->withInput($request->except('password'));
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        $this->apiService->logout();
        
        return redirect()->route('login')->with('success', 'Sesión cerrada exitosamente');
    }
}