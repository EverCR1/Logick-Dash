<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar lista de ventas
     */
    public function index(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $estado = $request->get('estado', 'todos');
            $tipo = $request->get('tipo', 'todos');
            $fecha_inicio = $request->get('fecha_inicio');
            $fecha_fin = $request->get('fecha_fin');
            
            // Llamar a la API con filtros
            $response = $this->apiService->get('ventas', [
                'query' => $search,
                'estado' => $estado,
                'tipo' => $tipo,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                $ventas = $data['ventas'] ?? [];
                $estadisticas = $data['estadisticas'] ?? [];
            } else {
                $ventas = [
                    'data' => [],
                    'links' => [],
                    'meta' => []
                ];
                $estadisticas = [];
            }

            return view('ventas.index', compact('ventas', 'estadisticas', 'search', 'estado', 'tipo', 'fecha_inicio', 'fecha_fin'));
            
        } catch (\Exception $e) {
            Log::error('Error obteniendo ventas: ' . $e->getMessage());
            
            return view('ventas.index', [
                'ventas' => ['data' => [], 'links' => [], 'meta' => []],
                'estadisticas' => [],
                'search' => '',
                'estado' => 'todos',
                'tipo' => 'todos'
            ])->with('error', 'Error al cargar las ventas');
        }
    }

    /**
     * Mostrar formulario de creación de venta
     */
    public function create()
    {
        try {
            // Obtener productos activos para la búsqueda
            $productos = [];
            $responseProductos = $this->apiService->get('ventas/buscar/productos', ['query' => '', 'limit' => 50]);
            if ($responseProductos->successful()) {
                $productos = $responseProductos->json()['productos'] ?? [];
            }
            
            // Obtener servicios activos para la búsqueda
            $servicios = [];
            $responseServicios = $this->apiService->get('ventas/buscar/servicios', ['query' => '', 'limit' => 50]);
            if ($responseServicios->successful()) {
                $servicios = $responseServicios->json()['servicios'] ?? [];
            }
            
            // Obtener clientes activos
            $clientes = [];
            $responseClientes = $this->apiService->get('ventas/buscar/clientes', ['query' => '', 'limit' => 50]);
            if ($responseClientes->successful()) {
                $clientes = $responseClientes->json()['clientes'] ?? [];
            }

            return view('ventas.create', compact('productos', 'servicios', 'clientes'));
            
        } catch (\Exception $e) {
            Log::error('Error cargando formulario de venta: ' . $e->getMessage());
            
            return redirect()->route('ventas.index')
                ->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Almacenar nueva venta con múltiples productos/servicios
     */
    public function store(Request $request)
    {
        try {
            // PASO 1: Validación básica
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.tipo' => 'required|in:producto,servicio,otro',
                'items.*.cantidad' => 'required|integer|min:1',
                'items.*.descripcion' => 'required|string|max:500',
                'items.*.precio_unitario' => 'required|numeric|min:0',
                'items.*.descuento' => 'nullable|numeric|min:0',
                'items.*.producto_id' => 'nullable|required_if:items.*.tipo,producto|integer',
                'items.*.servicio_id' => 'nullable|required_if:items.*.tipo,servicio|integer',
                'items.*.referencia' => 'nullable|string|max:100',
                'cliente_id' => 'nullable|integer',
                'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,mixto',
                'observaciones' => 'nullable|string',
            ], [
                'items.required' => 'Debe agregar al menos un producto o servicio',
                'items.min' => 'Debe agregar al menos un producto o servicio',
                'items.*.tipo.required' => 'El tipo de cada item es requerido',
                'items.*.cantidad.required' => 'La cantidad de cada item es requerida',
                'items.*.cantidad.min' => 'La cantidad debe ser al menos 1',
                'items.*.descripcion.required' => 'La descripción de cada item es requerida',
                'items.*.precio_unitario.required' => 'El precio de cada item es requerido',
                'items.*.precio_unitario.min' => 'El precio debe ser mayor a 0',
            ]);

            // PASO 2: Preparar datos para enviar a la API
            $data = [
                'items' => [],
                'cliente_id' => !empty($request->cliente_id) ? (int) $request->cliente_id : null,
                'metodo_pago' => $request->metodo_pago,
                'observaciones' => $request->observaciones ?? '',
            ];

            // PASO 3: Procesar cada item
            foreach ($request->items as $index => $item) {
                $itemData = [
                    'tipo' => $item['tipo'],
                    'cantidad' => (int) $item['cantidad'],
                    'descripcion' => $item['descripcion'],
                    'precio_unitario' => (float) $item['precio_unitario'],
                    'descuento' => isset($item['descuento']) ? (float) $item['descuento'] : 0,
                ];

                // Agregar IDs según el tipo
                if ($item['tipo'] === 'producto' && !empty($item['producto_id'])) {
                    $itemData['producto_id'] = (int) $item['producto_id'];
                } elseif ($item['tipo'] === 'servicio' && !empty($item['servicio_id'])) {
                    $itemData['servicio_id'] = (int) $item['servicio_id'];
                } elseif ($item['tipo'] === 'otro' && !empty($item['referencia'])) {
                    $itemData['referencia'] = $item['referencia'];
                }

                $data['items'][] = $itemData;
            }

            // PASO 4: Enviar a la API
            $response = $this->apiService->post('ventas', $data);

            if ($response->successful()) {
                return redirect()->route('ventas.index')
                    ->with('success', 'Venta registrada exitosamente');
            }

            // Manejar errores de la API
            $errorResponse = $response->json();
            $errors = [];

            if (isset($errorResponse['errors'])) {
                foreach ($errorResponse['errors'] as $field => $messages) {
                    if (is_array($messages)) {
                        $errors[$field] = implode(', ', $messages);
                    } else {
                        $errors[] = $messages;
                    }
                }
            } elseif (isset($errorResponse['message'])) {
                $errors[] = $errorResponse['message'];
            } else {
                $errors[] = 'Error al crear venta';
            }

            return back()->withErrors($errors)->withInput();
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error creando venta: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear venta: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mostrar detalles de una venta (actualizado para mostrar múltiples items)
     */
    public function show($id)
    {
        try {
            $response = $this->apiService->get("ventas/{$id}");
            
            if ($response->successful()) {
                $data = $response->json();
                $venta = $data['venta'] ?? null;
                
                if ($venta) {
                    return view('ventas.show', compact('venta'));
                }
            }

            return redirect()->route('ventas.index')
                ->with('error', 'Venta no encontrada');
            
        } catch (\Exception $e) {
            Log::error('Error mostrando venta: ' . $e->getMessage());
            return redirect()->route('ventas.index')
                ->with('error', 'Error al cargar la venta');
        }
    }

    /**
     * Cancelar una venta
     */
    public function cancelar($id)
    {
        try {
            $response = $this->apiService->post("ventas/{$id}/cancelar");
            
            if ($response->successful()) {
                return redirect()->route('ventas.index')
                    ->with('success', 'Venta cancelada exitosamente');
            }

            return redirect()->route('ventas.index')
                ->with('error', 'Error al cancelar la venta');
            
        } catch (\Exception $e) {
            Log::error('Error cancelando venta: ' . $e->getMessage());
            return redirect()->route('ventas.index')
                ->with('error', 'Error al cancelar la venta');
        }
    }

    /**
     * Buscar productos para venta (AJAX)
     */
    public function buscarProductos(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $limit = $request->get('limit', 10);

            $response = $this->apiService->get('ventas/buscar/productos', [
                'query' => $query,
                'limit' => $limit
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'productos' => $response->json()['productos'] ?? []
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error buscando productos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno'
            ], 500);
        }
    }

    /**
     * Buscar servicios para venta (AJAX)
     */
    public function buscarServicios(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $limit = $request->get('limit', 10);

            $response = $this->apiService->get('ventas/buscar/servicios', [
                'query' => $query,
                'limit' => $limit
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'servicios' => $response->json()['servicios'] ?? []
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error buscando servicios: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno'
            ], 500);
        }
    }

    /**
     * Buscar clientes para venta (AJAX)
     */
    public function buscarClientes(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $limit = $request->get('limit', 10);

            $response = $this->apiService->get('ventas/buscar/clientes', [
                'query' => $query,
                'limit' => $limit
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'clientes' => $response->json()['clientes'] ?? []
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Error buscando clientes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno'
            ], 500);
        }
    }

    /**
     * Obtener reporte de ventas
     */
    public function reporte(Request $request)
    {
        try {
            $fecha_inicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
            $fecha_fin = $request->get('fecha_fin', now()->format('Y-m-d'));
            
            $response = $this->apiService->get('ventas/reporte', [
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reporte = $data['reporte'] ?? [];
                
                return view('ventas.reporte', compact('reporte', 'fecha_inicio', 'fecha_fin'));
            }

            return redirect()->route('ventas.index')
                ->with('error', 'Error al generar el reporte');
            
        } catch (\Exception $e) {
            Log::error('Error generando reporte: ' . $e->getMessage());
            return redirect()->route('ventas.index')
                ->with('error', 'Error al generar el reporte');
        }
    }

    /**
     * Buscar ventas (para la vista de búsqueda)
     */
    public function buscar(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $estado = $request->get('estado', 'todos');
            $tipo = $request->get('tipo', 'todos');
            $fecha_inicio = $request->get('fecha_inicio');
            $fecha_fin = $request->get('fecha_fin');
            
            $response = $this->apiService->get('ventas/search', [
                'query' => $query,
                'estado' => $estado,
                'tipo' => $tipo,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $ventas = $data['ventas'] ?? [];
            } else {
                $ventas = [
                    'data' => [],
                    'links' => [],
                    'meta' => []
                ];
            }

            return view('ventas.buscar', compact('ventas', 'query', 'estado', 'tipo', 'fecha_inicio', 'fecha_fin'));
            
        } catch (\Exception $e) {
            Log::error('Error buscando ventas: ' . $e->getMessage());
            
            return view('ventas.buscar', [
                'ventas' => ['data' => [], 'links' => [], 'meta' => []],
                'query' => '',
                'estado' => 'todos',
                'tipo' => 'todos'
            ]);
        }
    }

    /**
     * Agregar item a venta existente (nuevo método)
     */
    public function agregarItem(Request $request, $id)
    {
        try {
            $request->validate([
                'tipo' => 'required|in:producto,servicio,otro',
                'cantidad' => 'required|integer|min:1',
                'descripcion' => 'required|string|max:500',
                'precio_unitario' => 'required|numeric|min:0',
                'descuento' => 'nullable|numeric|min:0',
                'producto_id' => 'nullable|required_if:tipo,producto|integer',
                'servicio_id' => 'nullable|required_if:tipo,servicio|integer',
                'referencia' => 'nullable|string|max:100',
            ]);

            $response = $this->apiService->post("ventas/{$id}/detalles", $request->all());

            if ($response->successful()) {
                return redirect()->route('ventas.show', $id)
                    ->with('success', 'Item agregado exitosamente');
            }

            return back()->withErrors(['error' => 'Error al agregar item'])->withInput();
            
        } catch (\Exception $e) {
            Log::error('Error agregando item: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al agregar item: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Eliminar item de venta (nuevo método)
     */
    public function eliminarItem($id, $detalleId)
    {
        try {
            $response = $this->apiService->delete("ventas/{$id}/detalles/{$detalleId}");

            if ($response->successful()) {
                return redirect()->route('ventas.show', $id)
                    ->with('success', 'Item eliminado exitosamente');
            }

            return back()->withErrors(['error' => 'Error al eliminar item']);
            
        } catch (\Exception $e) {
            Log::error('Error eliminando item: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al eliminar item: ' . $e->getMessage()]);
        }
    }
}