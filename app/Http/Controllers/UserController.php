<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log; // Añadir esto para los logs

class UserController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar lista de usuarios con paginación y filtros
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $estado = $request->get('estado', 'todos');
        $page = $request->get('page', 1);
        
        // Parámetros para la API
        $params = [
            'page' => $page,
            'query' => $search,
            'estado' => $estado
        ];
        
        // Llamar a la API con paginación
        $response = $this->apiService->get('users', $params);
        
        if ($response->successful()) {
            $data = $response->json();
            
            // La API devuelve 'users' con los datos paginados
            $usuarios = $data['users'] ?? [];
            
            // Transformar los links de paginación
            if (isset($usuarios['links'])) {
                $usuarios['links'] = $this->transformPaginationLinks($usuarios['links'], $request->path());
            }
            
            // Transformar las URLs en los links de paginación
            if (isset($usuarios['links'])) {
                foreach ($usuarios['links'] as &$link) {
                    if (isset($link['url']) && $link['url']) {
                        // Extraer el número de página de la URL original
                        $parsedUrl = parse_url($link['url']);
                        parse_str($parsedUrl['query'] ?? '', $queryParams);
                        $page = $queryParams['page'] ?? null;
                        
                        if ($page) {
                            // Mantener los filtros actuales
                            $filtros = [
                                'page' => $page,
                                'search' => $search,
                                'estado' => $estado
                            ];
                            // Reemplazar con la ruta del frontend incluyendo filtros
                            $link['url'] = route('usuarios.index', $filtros);
                        }
                    }
                }
            }
        } else {
            // Estructura vacía para cuando falla la API
            $usuarios = [
                'data' => [],
                'links' => [],
                'meta' => []
            ];
        }

        return view('usuarios.index', compact('usuarios', 'search', 'estado'));
    }

    /**
     * Transformar los links de paginación
     */
    private function transformPaginationLinks($links, $path)
    {
        foreach ($links as &$link) {
            if (isset($link['url']) && $link['url']) {
                // Extraer el número de página de la URL original
                preg_match('/[?&]page=(\d+)/', $link['url'], $matches);
                $page = $matches[1] ?? null;
                
                if ($page) {
                    // Reemplazar con la ruta del frontend
                    $link['url'] = url($path) . '?page=' . $page;
                } else {
                    $link['url'] = null;
                }
            }
        }
        
        return $links;
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email',
            'username' => 'required|string|max:50',
            'password' => 'required|min:8|confirmed',
            'rol' => 'required|in:administrador,vendedor,analista',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $response = $this->apiService->post('users', $request->all());

        if ($response->successful()) {
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario creado exitosamente');
        }

        return back()->withErrors($response->json()['errors'] ?? ['Error al crear usuario'])
                    ->withInput();
    }

    public function show($id)
    {
        \Log::info("=== SHOW METHOD ===");
        \Log::info("ID recibido: " . $id);
        
        $response = $this->apiService->get("users/{$id}");
        
        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Respuesta de API:", $data);
            
            // Cambiamos de $user a $usuario
            $usuario = $data['user'] ?? null;
            \Log::info("Usuario encontrado: ", $usuario ?? []);
            
            return view('usuarios.show', compact('usuario'));
        }

        \Log::error("Usuario no encontrado con ID: " . $id);
        return redirect()->route('usuarios.index')
            ->with('error', 'Usuario no encontrado');
    }

    public function edit($id)
    {
        \Log::info("=== EDIT METHOD ===");
        \Log::info("ID recibido: " . $id);
        
        $response = $this->apiService->get("users/{$id}");
        
        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Respuesta de API:", $data);
            
            // Cambiamos de $user a $usuario
            $usuario = $data['user'] ?? null;
            \Log::info("Usuario encontrado: ", $usuario ?? []);
            
            return view('usuarios.edit', compact('usuario'));
        }

        \Log::error("Usuario no encontrado con ID: " . $id);
        return redirect()->route('usuarios.index')
            ->with('error', 'Usuario no encontrado');
    }

    public function update(Request $request, $id)
    {
        \Log::info("=== UPDATE METHOD ===");
        \Log::info("ID recibido: " . $id);
        \Log::info("Datos recibidos:", $request->except('password', 'password_confirmation'));

        // Validaciones básicas de formato (sin reglas de unicidad)
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'username' => 'required|string|max:50',
            'rol' => 'required|in:administrador,vendedor,analista',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        // Si se envía contraseña, validarla
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
        }

        $response = $this->apiService->put("users/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario actualizado exitosamente');
        }

        // Capturar errores de la API
        $errors = $response->json()['errors'] ?? ['Error al actualizar usuario'];
        
        return back()->withErrors($errors)
                    ->withInput($request->except('password', 'password_confirmation'));
    }

    public function destroy($id)
    {
        \Log::info("=== DESTROY METHOD FRONTEND ===");
        \Log::info("ID recibido: " . $id);
        
        $response = $this->apiService->delete("users/{$id}");

        if ($response->successful()) {
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario eliminado exitosamente');
        }

        // Capturar errores específicos de la API
        $errorData = $response->json();
        $errorMessage = 'Error al eliminar usuario';
        
        if (isset($errorData['errors']['relaciones'])) {
            // Si es un error por relaciones, formateamos el mensaje
            $errorMessage = implode('<br>', $errorData['errors']['relaciones']);
            return redirect()->route('usuarios.index')
                ->with('error_relaciones', $errorMessage);
        } elseif (isset($errorData['message'])) {
            $errorMessage = $errorData['message'];
        }

        return redirect()->route('usuarios.index')
            ->with('error', $errorMessage);
    }

    /**
     * Cambiar estado del usuario
     */
    public function changeStatus(Request $request, $id)
    {
        \Log::info("=== CHANGE STATUS METHOD ===");
        \Log::info("ID recibido: " . $id);
        \Log::info("Estado solicitado: " . $request->estado);
        
        $request->validate([
            'estado' => 'required|in:activo,inactivo'
        ]);

        $response = $this->apiService->post("users/{$id}/change-status", [
            'estado' => $request->estado
        ]);

        if ($response->successful()) {
            return redirect()->route('usuarios.index')
                ->with('success', 'Estado del usuario actualizado exitosamente');
        }

        return redirect()->route('usuarios.index')
            ->with('error', 'Error al cambiar el estado del usuario');
    }
}