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

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
                'new_password_confirmation' => 'required|string'
            ]);

            // Llamar a la API para cambiar contraseña
            $response = $this->apiService->post('change-password', [
                'current_password' => $request->current_password,
                'new_password' => $request->new_password,
                'new_password_confirmation' => $request->new_password_confirmation
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Si la API indica que debemos cerrar sesión
                if (isset($data['message']) && str_contains($data['message'], 'Inicia sesión nuevamente')) {
                    // Limpiar sesión actual
                    $request->session()->forget(['api_token', 'user']);
                    $request->session()->regenerate();
                    
                    return response()->json([
                        'success' => true,
                        'message' => $data['message'],
                        'redirect' => route('login')
                    ]);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $data['message'] ?? 'Contraseña cambiada exitosamente'
                ]);
            }

            // Si hay error en la API
            $errorData = $response->json();
            $errorMessage = $errorData['message'] ?? 'Error al cambiar la contraseña';
            
            if (isset($errorData['errors'])) {
                $firstError = collect($errorData['errors'])->flatten()->first();
                $errorMessage = $firstError ?? $errorMessage;
            }
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 422);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error cambiando contraseña: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar la contraseña. Intenta de nuevo.'
            ], 500);
        }
    }
}