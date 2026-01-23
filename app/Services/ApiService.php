<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ApiService
{
    protected $baseUrl;
    protected $apiUrl;

    public function __construct()
    {
        $this->baseUrl = config('api.base_url'); // Para API calls
        $this->apiUrl = config('api.url'); // Para assets/archivos
        
    }

    public function login($email, $password, $username = null)
    {
        try {
            $data = ['password' => $password];
            
            if ($email) {
                $data['email'] = $email;
            } elseif ($username) {
                $data['username'] = $username;
            } else {
                return [
                    'success' => false,
                    'error' => 'Email o username requerido'
                ];
            }

            Log::info('Enviando login a API:', [
                'url' => "{$this->baseUrl}/api/login",
                'data' => ['email' => $data['email'] ?? $data['username'], 'password' => '***']
            ]);

            $response = Http::post("{$this->baseUrl}/api/login", $data);

            Log::info('Respuesta de API:', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['access_token'])) {
                    Session::put('api_token', $data['access_token']);
                    Session::put('user', $data['user']);
                    
                    Log::info('Login exitoso. Token guardado.');
                    
                    return [
                        'success' => true,
                        'data' => $data
                    ];
                }
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'error' => $response->json()['message'] ?? 'Credenciales incorrectas',
                'errors' => $response->json()['errors'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Error en conexión API:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => 'Error de conexión con el servidor: ' . $e->getMessage()
            ];
        }
    }

    public function get($endpoint, $params = [])
    {
        return Http::withToken(Session::get('api_token'))
                  ->get("{$this->baseUrl}/api/{$endpoint}", $params);
    }

    public function post($endpoint, $data = [])
    {
        return Http::withToken(Session::get('api_token'))
                  ->post("{$this->baseUrl}/api/{$endpoint}", $data);
    }

    /**
     * Enviar petición POST con multipart/form-data para subir archivos
     */
    public function postMultipart($endpoint, $multipart)
    {
        $url = "{$this->baseUrl}/api/{$endpoint}";
        
        Log::info('Enviando multipart a API:', [
            'url' => $url,
            'multipart_count' => count($multipart)
        ]);
        
        // Iniciar la petición con el token
        $http = Http::withToken(Session::get('api_token'))
                   ->withHeaders(['Accept' => 'application/json']);
        
        // Agregar cada parte del multipart
        foreach ($multipart as $part) {
            if (isset($part['contents']) && is_resource($part['contents'])) {
                $http = $http->attach(
                    $part['name'],
                    $part['contents'],
                    $part['filename'] ?? null
                );
            } else {
                // Si es un string, enviarlo como campo normal
                $http = $http->withOptions([
                    'multipart' => [
                        [
                            'name' => $part['name'],
                            'contents' => $part['contents'] ?? ''
                        ]
                    ]
                ]);
            }
        }
        
        return $http->post($url);
    }

    /**
     * Método alternativo más simple para subir archivos
     */
    public function postWithFiles($endpoint, $data = [], $files = [])
    {
        $url = "{$this->baseUrl}/api/{$endpoint}";
        
        \Log::info('Enviando archivos a API:', [
            'url' => $url,
            'data_fields' => count($data),
            'files_count' => count($files)
        ]);
        
        // Iniciar la petición
        $http = Http::withToken(Session::get('api_token'))
                ->acceptJson();
        
        // Agregar archivos si existen
        foreach ($files as $fieldName => $fileArray) {
            if (is_array($fileArray)) {
                // Múltiples archivos
                foreach ($fileArray as $index => $file) {
                    if ($file && $file->isValid()) {
                        $http = $http->attach(
                            $fieldName . '[]',
                            fopen($file->getRealPath(), 'r'),
                            $file->getClientOriginalName()
                        );
                    }
                }
            } else {
                // Un solo archivo
                if ($fileArray && $fileArray->isValid()) {
                    $http = $http->attach(
                        $fieldName,
                        fopen($fileArray->getRealPath(), 'r'),
                        $fileArray->getClientOriginalName()
                    );
                }
            }
        }
        
        // Agregar datos adicionales como multipart
        foreach ($data as $key => $value) {
            $http = $http->attach($key, $value);
        }
        
        return $http->post($url);
    }

    /**
     * Obtener URL completa para una imagen de la API
     */
    public function getImageUrl($imagePath)
    {
        // Si ya es una URL completa
        if (str_starts_with($imagePath, 'http')) {
            return $imagePath;
        }
        
        // Si comienza con /storage
        if (str_starts_with($imagePath, '/storage')) {
            return rtrim($this->apiUrl, '/') . $imagePath;
        }
        
        // Si es solo el nombre del archivo
        if (!str_contains($imagePath, '/')) {
            return rtrim($this->apiUrl, '/') . '/storage/productos/' . $imagePath;
        }
        
        // Por defecto
        return rtrim($this->apiUrl, '/') . '/' . ltrim($imagePath, '/');
    }

    public function put($endpoint, $data = [])
    {
        return Http::withToken(Session::get('api_token'))
                  ->put("{$this->baseUrl}/api/{$endpoint}", $data);
    }

    public function delete($endpoint, $data = [])
    {
        return Http::withToken(Session::get('api_token'))
                  ->delete("{$this->baseUrl}/api/{$endpoint}", $data);
    }

    public function logout()
    {
        try {
            // Intentar hacer logout en la API
            $token = Session::get('api_token');
            if ($token) {
                Http::withToken($token)
                    ->post("{$this->baseUrl}/api/logout");
            }
        } catch (\Exception $e) {
            Log::warning('Error al hacer logout en API:', ['error' => $e->getMessage()]);
        }
        
        // Limpiar sesión
        Session::forget(['api_token', 'user']);
        Session::flush();
    }

    public function isAuthenticated()
    {
        return Session::has('api_token') && Session::has('user');
    }

    public function getUser()
    {
        return Session::get('user');
    }

    public function getUserRole()
    {
        $user = $this->getUser();
        return $user['rol'] ?? null;
    }
    
    /**
     * Método para verificar si el token aún es válido
     */
    public function checkToken()
    {
        try {
            $response = $this->get('user');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Error verificando token:', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    /**
     * Obtener el token actual
     */
    public function getToken()
    {
        return Session::get('api_token');
    }
}