<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ServicioController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar lista de servicios
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        
        $response = $this->apiService->get('servicios', [
            'page' => $page
        ]);
        
        if ($response->successful()) {
            $servicios = $response->json()['servicios'] ?? [];
            
            // Transformar los links de paginación
            if (isset($servicios['links'])) {
                $servicios['links'] = $this->transformPaginationLinks($servicios['links'], $request->path());
            }
            
            // Transformar las URLs en los links de paginación
            if (isset($servicios['links'])) {
                foreach ($servicios['links'] as &$link) {
                    if (isset($link['url']) && $link['url']) {
                        $parsedUrl = parse_url($link['url']);
                        parse_str($parsedUrl['query'] ?? '', $queryParams);
                        $page = $queryParams['page'] ?? null;
                        
                        if ($page) {
                            $link['url'] = route('servicios.index', ['page' => $page]);
                        }
                    }
                }
            }
        } else {
            $servicios = [
                'data' => [],
                'links' => [],
                'meta' => []
            ];
        }

        return view('servicios.index', compact('servicios'));
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

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('servicios.create');
    }

    /**
     * Almacenar nuevo servicio
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'inversion_estimada' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
            'notas_internas' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $servicioData = $request->except(['imagen', '_token']);
            
            // Crear servicio primero
            $response = $this->apiService->post('servicios', $servicioData);

            if ($response->successful()) {
                $servicio = $response->json()['servicio'] ?? null;
                $servicioId = $servicio['id'] ?? null;
                
                // Subir imagen si se proporcionó
                if ($request->hasFile('imagen') && $servicioId) {
                    $this->subirImagenServicio($request, $servicioId);
                }
                
                return redirect()->route('servicios.index')
                    ->with('success', 'Servicio creado exitosamente' . 
                        ($request->hasFile('imagen') ? ' (imagen subida)' : ''));
            }

            $errors = $response->json()['errors'] ?? [];
            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            return back()->withErrors(['Error al crear servicio: ' . ($response->json()['message'] ?? 'Error desconocido')])->withInput();

        } catch (\Exception $e) {
            \Log::error('Error creando servicio:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Error interno: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Subir imagen para servicio
     */
    private function subirImagenServicio(Request $request, $servicioId)
    {
        try {
            $endpoint = "servicios/{$servicioId}/upload-image";
            
            // Preparar datos para la API
            $data = [];
            $files = [];
            
            // Preparar archivo
            if ($request->hasFile('imagen')) {
                $files['imagen'] = $request->file('imagen');
            }
            
            // Usar el método postWithFiles
            $response = $this->apiService->postWithFiles($endpoint, $data, $files);
            
            if (!$response->successful()) {
                throw new \Exception('Error API: ' . ($response->json()['message'] ?? $response->status()));
            }
            
            \Log::info('Imagen subida exitosamente para servicio ID: ' . $servicioId);
            
        } catch (\Exception $e) {
            \Log::error('Error subiendo imagen de servicio:', [
                'message' => $e->getMessage(),
                'servicio_id' => $servicioId
            ]);
            throw $e;
        }
    }

    /**
     * Mostrar servicio específico
     */
    public function show($id)
    {
        $response = $this->apiService->get("servicios/{$id}");
        
        if ($response->successful()) {
            $servicio = $response->json()['servicio'] ?? null;
            
            if (!$servicio) {
                return redirect()->route('servicios.index')
                    ->with('error', 'Servicio no encontrado');
            }
            
            return view('servicios.show', compact('servicio'));
        }

        return redirect()->route('servicios.index')
            ->with('error', 'Servicio no encontrado');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $response = $this->apiService->get("servicios/{$id}");
        
        if (!$response->successful()) {
            return redirect()->route('servicios.index')
                ->with('error', 'Servicio no encontrado');
        }

        $data = $response->json();
        $servicio = $data['servicio'] ?? null;
        
        if (!$servicio) {
            return redirect()->route('servicios.index')
                ->with('error', 'Servicio no encontrado en la respuesta');
        }

        return view('servicios.edit', compact('servicio'));
    }

    /**
     * Actualizar servicio
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo' => 'required|string|max:50',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'inversion_estimada' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
            'notas_internas' => 'nullable|string',
            'nueva_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            // Preparar datos para la API (excluir nueva_imagen)
            $servicioData = $request->except(['_token', '_method', 'nueva_imagen']);

            \Log::info('Actualizando servicio:', [
                'id' => $id,
                'data' => $servicioData,
                'tiene_nueva_imagen' => $request->hasFile('nueva_imagen')
            ]);

            // Actualizar servicio
            $response = $this->apiService->put("servicios/{$id}", $servicioData);

            if ($response->successful()) {
                // Subir nueva imagen si se proporcionó
                if ($request->hasFile('nueva_imagen')) {
                    try {
                        $this->subirNuevaImagenServicio($request, $id);
                    } catch (\Exception $e) {
                        \Log::warning('Error subiendo imagen al actualizar servicio:', [
                            'message' => $e->getMessage(),
                            'servicio_id' => $id
                        ]);
                        
                        return redirect()->route('servicios.index')
                            ->with('warning', 'Servicio actualizado pero hubo un error al subir la imagen: ' . $e->getMessage());
                    }
                }
                
                return redirect()->route('servicios.index')
                    ->with('success', 'Servicio actualizado exitosamente' . 
                        ($request->hasFile('nueva_imagen') ? ' (imagen actualizada)' : ''));
            }

            $errors = $response->json()['errors'] ?? [];
            $message = $response->json()['message'] ?? 'Error desconocido al actualizar';
            
            \Log::error('Error API al actualizar servicio:', [
                'status' => $response->status(),
                'errors' => $errors,
                'message' => $message
            ]);

            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            return back()->withErrors(['error' => $message])->withInput();

        } catch (\Exception $e) {
            \Log::error('Excepción al actualizar servicio:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Error interno: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Método auxiliar para subir nueva imagen (para update)
     */
    private function subirNuevaImagenServicio(Request $request, $servicioId)
    {
        try {
            $endpoint = "servicios/{$servicioId}/upload-image";
            
            // Preparar datos para la API
            $data = [];
            $files = [];
            
            // Preparar archivo - usar 'imagen' para la API
            if ($request->hasFile('nueva_imagen')) {
                $files['imagen'] = $request->file('nueva_imagen');
            }
            
            \Log::info('Subiendo nueva imagen a API:', [
                'endpoint' => $endpoint,
                'file_name' => $request->file('nueva_imagen')->getClientOriginalName()
            ]);
            
            // Usar el método postWithFiles
            $response = $this->apiService->postWithFiles($endpoint, $data, $files);
            
            if (!$response->successful()) {
                $errorData = $response->json();
                \Log::error('Error API al subir nueva imagen:', [
                    'status' => $response->status(),
                    'response' => $errorData
                ]);
                throw new \Exception('Error API: ' . ($errorData['message'] ?? $response->status()));
            }
            
            \Log::info('Nueva imagen subida exitosamente para servicio ID: ' . $servicioId);
            
        } catch (\Exception $e) {
            \Log::error('Error subiendo nueva imagen de servicio:', [
                'message' => $e->getMessage(),
                'servicio_id' => $servicioId
            ]);
            throw $e;
        }
    }

    /**
     * Eliminar servicio
     */
    public function destroy($id)
    {
        $response = $this->apiService->delete("servicios/{$id}");

        if ($response->successful()) {
            return redirect()->route('servicios.index')
                ->with('success', 'Servicio eliminado exitosamente');
        }

        $message = $response->json()['message'] ?? 'Error desconocido';
        
        return redirect()->route('servicios.index')
            ->with('error', 'Error al eliminar servicio: ' . $message);
    }

    /**
     * Cambiar estado del servicio
     */
    public function changeStatus($id)
    {
        try {
            $response = $this->apiService->post("servicios/{$id}/change-status");
            
            if ($response->successful()) {
                return back()->with('success', 'Estado del servicio cambiado exitosamente');
            }
            
            return back()->with('error', 'Error al cambiar estado: ' . ($response->json()['message'] ?? 'Error desconocido'));
            
        } catch (\Exception $e) {
            \Log::error('Error cambiando estado del servicio:', [
                'message' => $e->getMessage(),
                'servicio_id' => $id
            ]);
            
            return back()->with('error', 'Error interno al cambiar estado');
        }
    }

    /**
     * Buscar servicios
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        $estado = $request->get('estado', 'todos');

        $params = [];
        if ($query) {
            $params['query'] = $query;
        }
        if ($estado !== 'todos') {
            $params['estado'] = $estado;
        }

        $response = $this->apiService->get('servicios/search', $params);

        if ($response->successful()) {
            $servicios = $response->json()['servicios'] ?? [];
        } else {
            $servicios = [
                'data' => [],
                'links' => [],
                'meta' => []
            ];
        }

        return view('servicios.buscar', compact('servicios', 'query', 'estado'));
    }

    /**
     * Subir imagen para servicio existente
     */
    public function subirImagen(Request $request, $id)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $endpoint = "servicios/{$id}/upload-image";
            
            // Preparar datos para la API
            $data = [];
            $files = [];
            
            // Preparar archivo
            if ($request->hasFile('imagen')) {
                $files['imagen'] = $request->file('imagen');
            }
            
            $response = $this->apiService->postWithFiles($endpoint, $data, $files);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen subida exitosamente',
                    'data' => $response->json()
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al subir imagen: ' . ($response->json()['message'] ?? 'Error ' . $response->status()),
                'errors' => $response->json()['errors'] ?? null
            ], $response->status());
            
        } catch (\Exception $e) {
            \Log::error('Excepción en subirImagen:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'servicio_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar imagen de servicio
     */
    public function eliminarImagen($id, $imagenId)
    {
        try {
            $response = $this->apiService->delete("servicios/{$id}/images/{$imagenId}");
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen eliminada exitosamente'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar imagen: ' . ($response->json()['message'] ?? 'Error ' . $response->status())
            ], $response->status());
            
        } catch (\Exception $e) {
            \Log::error('Error eliminando imagen de servicio:', [
                'message' => $e->getMessage(),
                'servicio_id' => $id,
                'imagen_id' => $imagenId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }
}