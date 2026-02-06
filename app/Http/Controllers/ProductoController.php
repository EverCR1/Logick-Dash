<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProductoController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        $response = $this->apiService->get('productos');
        
        if ($response->successful()) {
            $productos = $response->json()['productos'] ?? [];
        } else {
            $productos = [
                'data' => [],
                'links' => [],
                'meta' => []
            ];
        }

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        // Obtener proveedores para el select
        $proveedoresResponse = $this->apiService->get('proveedores');
        $proveedores = $proveedoresResponse->successful() ? $proveedoresResponse->json()['proveedores'] ?? [] : [];
        
        // Obtener categorías para el select múltiple
        $categoriasResponse = $this->apiService->get('categorias-flat');
        $categorias = $categoriasResponse->successful() ? $categoriasResponse->json()['categorias'] ?? [] : [];

        return view('productos.create', compact('proveedores', 'categorias'));
    }


    /**
     * Subir imágenes a un producto
     */
    public function subirImagenes(Request $request, $id)
    {
        \Log::info('Iniciando subirImagenes', [
            'producto_id' => $id,
            'files_count' => $request->hasFile('imagenes') ? count($request->file('imagenes')) : 0,
            'all_data' => $request->all()
        ]);

        $request->validate([
            'imagenes' => 'required|array',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'establecer_principal' => 'nullable|boolean',
        ]);

        try {
            $endpoint = "productos/{$id}/upload-images";
            
            \Log::info('Preparando datos para API', ['endpoint' => $endpoint]);
            
            // Preparar datos para la API
            $data = [];
            $files = [];
            
            // Preparar archivos
            if ($request->hasFile('imagenes')) {
                $files['imagenes'] = $request->file('imagenes');
                \Log::info('Archivos recibidos', ['count' => count($files['imagenes'])]);
            }
            
            // Agregar campo establecer_principal si existe
            if ($request->has('establecer_principal') && $request->establecer_principal) {
                $data['establecer_principal'] = '1';
            }
            
            \Log::info('Enviando a API', ['data' => $data, 'files_count' => count($files)]);
            
            // Usar el nuevo método postWithFiles
            $response = $this->apiService->postWithFiles($endpoint, $data, $files);
            
            \Log::info('Respuesta de API', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Imágenes subidas exitosamente',
                    'data' => $response->json()
                ]);
            }
            
            // Loggear error detallado
            \Log::error('Error API al subir imágenes:', [
                'status' => $response->status(),
                'response' => $response->json(),
                'producto_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al subir imágenes: ' . ($response->json()['message'] ?? 'Error ' . $response->status()),
                'errors' => $response->json()['errors'] ?? null
            ], $response->status());
            
        } catch (\Exception $e) {
            \Log::error('Excepción en subirImagenes:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'producto_id' => $id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Establecer imagen como principal
     */
    public function establecerImagenPrincipal(Request $request, $id, $imagenId)
    {
        try {
            $response = $this->apiService->post("productos/{$id}/images/{$imagenId}/set-main");
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen principal establecida exitosamente'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error al establecer imagen principal: ' . ($response->json()['message'] ?? 'Error ' . $response->status())
            ], $response->status());
            
        } catch (\Exception $e) {
            Log::error('Error estableciendo imagen principal:', [
                'message' => $e->getMessage(),
                'producto_id' => $id,
                'imagen_id' => $imagenId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Eliminar imagen
     */
    public function eliminarImagen($id, $imagenId)
    {
        try {
            $response = $this->apiService->delete("productos/{$id}/images/{$imagenId}");
            
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
            Log::error('Error eliminando imagen:', [
                'message' => $e->getMessage(),
                'producto_id' => $id,
                'imagen_id' => $imagenId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Modificar el método store para usar la nueva función
     */
    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|max:50',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'especificaciones' => 'nullable|string',
            'marca' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'proveedor_id' => 'required',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'codigo_barras' => 'nullable|string|max:100',
            'ubicacion' => 'nullable|string|max:100',
            'notas_internas' => 'nullable|string',
            'categorias' => 'required|array',
            'imagenes' => 'nullable|array',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Cambiado a nullable
            'establecer_principal' => 'nullable|boolean',
        ]);

        // Crear producto sin imágenes primero
        $productoData = $request->except(['imagenes', '_token', 'establecer_principal', 'categorias']);
        
        // Agregar categorías por separado
        $productoData['categorias'] = $request->categorias;
        
        $response = $this->apiService->post('productos', $productoData);

        if ($response->successful()) {
            $producto = $response->json()['producto'] ?? null;
            $productoId = $producto['id'] ?? null;
            
            // Subir imágenes si se proporcionaron
            if ($request->hasFile('imagenes') && $productoId) {
                try {
                    // Llamar directamente al método interno para subir imágenes
                    $this->subirImagenesProducto($request, $productoId);
                } catch (\Exception $e) {
                    \Log::warning('Error subiendo imágenes al crear producto:', [
                        'message' => $e->getMessage(),
                        'producto_id' => $productoId
                    ]);
                    
                    // Continuar sin imágenes si hay error
                    return redirect()->route('productos.index')
                        ->with('warning', 'Producto creado pero hubo un error al subir las imágenes: ' . $e->getMessage());
                }
            }
            
            return redirect()->route('productos.index')
                ->with('success', 'Producto creado exitosamente' . 
                    ($request->hasFile('imagenes') ? ' (imágenes subidas)' : ''));
        }

        $errors = $response->json()['errors'] ?? [];
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        return back()->withErrors(['Error al crear producto: ' . ($response->json()['message'] ?? 'Error desconocido')])->withInput();
    }

    /**
     * Método interno para subir imágenes (solo para creación)
     */
    private function subirImagenesProducto(Request $request, $productoId)
    {
        try {
            $endpoint = "productos/{$productoId}/upload-images";
            
            // Preparar datos para la API
            $data = [];
            $files = [];
            
            // Preparar archivos
            if ($request->hasFile('imagenes')) {
                $files['imagenes'] = $request->file('imagenes');
            }
            
            // Agregar campo establecer_principal si existe
            if ($request->has('establecer_principal') && $request->establecer_principal) {
                $data['establecer_principal'] = '1';
            }
            
            // Usar el método postWithFiles
            $response = $this->apiService->postWithFiles($endpoint, $data, $files);
            
            if (!$response->successful()) {
                throw new \Exception('Error API: ' . ($response->json()['message'] ?? $response->status()));
            }
            
            \Log::info('Imágenes subidas exitosamente para producto ID: ' . $productoId);
            
        } catch (\Exception $e) {
            \Log::error('Error subiendo imágenes:', [
                'message' => $e->getMessage(),
                'producto_id' => $productoId
            ]);
            throw $e;
        }
    }


    public function show($id)
    {
        $response = $this->apiService->get("productos/{$id}");
        
        if ($response->successful()) {
            $producto = $response->json()['producto'] ?? null;
            
            // Debug temporal
            // dd($producto); // Descomenta para ver estructura
            
            return view('productos.show', compact('producto'));
        }

        return redirect()->route('productos.index')
            ->with('error', 'Producto no encontrado');
    }

    public function edit($id)
{
    try {
        // Obtener producto con imágenes
        $response = $this->apiService->get("productos/{$id}");
        
        if (!$response->successful()) {
            return redirect()->route('productos.index')
                ->with('error', 'Producto no encontrado');
        }

        $data = $response->json();
        $producto = $data['producto'] ?? null;
        
        if (!$producto) {
            return redirect()->route('productos.index')
                ->with('error', 'Producto no encontrado en la respuesta');
        }
        
        // DEPURACIÓN: Verificar estructura de imágenes
        \Log::info('Estructura del producto recibida:', [
            'id' => $id,
            'tiene_imagenes' => isset($producto['imagenes']),
            'numero_imagenes' => isset($producto['imagenes']) ? count($producto['imagenes']) : 0,
            'estructura_imagenes' => isset($producto['imagenes']) ? array_keys($producto['imagenes'][0] ?? []) : []
        ]);
        
        // Verificar y normalizar la estructura de imágenes
        if (isset($producto['imagenes']) && is_array($producto['imagenes'])) {
            foreach ($producto['imagenes'] as &$imagen) {
                // Asegurar que todas las imágenes tienen las propiedades esperadas
                $imagen = array_merge([
                    'id' => $imagen['id'] ?? null,
                    'url' => $imagen['url'] ?? $imagen['ruta'] ?? $imagen['path'] ?? null,
                    'nombre' => $imagen['nombre'] ?? $imagen['original_name'] ?? 'imagen.jpg',
                    'principal' => $imagen['principal'] ?? $imagen['is_principal'] ?? false,
                    'orden' => $imagen['orden'] ?? $imagen['order'] ?? 0
                ], $imagen);
            }
            unset($imagen); // Romper la referencia
        } else {
            $producto['imagenes'] = [];
        }

        // Obtener proveedores
        $proveedoresResponse = $this->apiService->get('proveedores');
        $proveedores = $proveedoresResponse->successful() ? 
            ($proveedoresResponse->json()['proveedores'] ?? []) : [];
        
        // Obtener categorías
        $categoriasResponse = $this->apiService->get('categorias-flat');
        $categorias = $categoriasResponse->successful() ? 
            ($categoriasResponse->json()['categorias'] ?? []) : [];

        // DEPURACIÓN: Verificar datos
        \Log::info('Datos para vista edit:', [
            'producto_id' => $producto['id'] ?? null,
            'proveedores_count' => count($proveedores),
            'categorias_count' => count($categorias),
            'imagenes_count' => count($producto['imagenes'] ?? [])
        ]);

        return view('productos.edit', compact('producto', 'proveedores', 'categorias'));
        
    } catch (\Exception $e) {
        \Log::error('Error en método edit:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'producto_id' => $id
        ]);
        
        return redirect()->route('productos.index')
            ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
    }
}

    public function update(Request $request, $id)
    {
        $request->validate([
            'sku' => 'required|string|max:50',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'especificaciones' => 'nullable|string',
            'marca' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'proveedor_id' => 'required',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0',
            'estado' => 'required|in:activo,inactivo',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'codigo_barras' => 'nullable|string|max:100',
            'ubicacion' => 'nullable|string|max:100',
            'notas_internas' => 'nullable|string',
            'categorias' => 'required|array',
        ]);

        try {
            // Preparar datos para la API
            $productoData = $request->except(['categorias', '_token', '_method']);
            
            // Agregar categorías de forma especial
            $productoData['categorias'] = $request->categorias;

            // DEPURACIÓN: Log de los datos que se envían
            \Log::info('Datos para actualizar producto:', [
                'id' => $id,
                'data' => $productoData,
                'categorias_count' => count($request->categorias ?? [])
            ]);

            $response = $this->apiService->put("productos/{$id}", $productoData);

            // DEPURACIÓN: Log de la respuesta
            \Log::info('Respuesta de actualización:', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->json() ?? []
            ]);

            if ($response->successful()) {
                return redirect()->route('productos.index')
                    ->with('success', 'Producto actualizado exitosamente');
            }

            // Manejar errores de la API
            $errors = $response->json()['errors'] ?? [];
            $message = $response->json()['message'] ?? 'Error desconocido al actualizar';
            
            \Log::error('Error API al actualizar producto:', [
                'status' => $response->status(),
                'errors' => $errors,
                'message' => $message
            ]);

            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            return back()->withErrors(['error' => $message])->withInput();

        } catch (\Exception $e) {
            \Log::error('Excepción al actualizar producto:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Error interno: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $response = $this->apiService->delete("productos/{$id}");

        if ($response->successful()) {
            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente');
        }

        return redirect()->route('productos.index')
            ->with('error', 'Error al eliminar producto');
    }

    public function buscar(Request $request)
    {
        $query = $request->get('q');
        $tipo = $request->get('tipo', 'todos');

        if ($query) {
            $response = $this->apiService->get('productos/search', [
                'query' => $query,
                'tipo' => $tipo
            ]);

            if ($response->successful()) {
                $productos = $response->json()['productos'] ?? [];
            } else {
                $productos = [];
            }
        } else {
            $productos = [];
        }

        return view('productos.buscar', compact('productos', 'query', 'tipo'));
    }

    public function stockBajo()
    {
        $response = $this->apiService->get('productos/stock-bajo');
        
        if ($response->successful()) {
            $productos = $response->json()['productos'] ?? [];
        } else {
            $productos = [];
        }

        return view('productos.stock-bajo', compact('productos'));
    }
}