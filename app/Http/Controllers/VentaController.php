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
     * Almacenar nueva venta
     */
    public function store(Request $request)
    {
        try {
            // PASO 1: Validación básica inicial
            $request->validate([
                'tipo' => 'required|in:producto,servicio,otro',
                'cantidad' => 'required|integer|min:1',
                'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,mixto',
                'producto_id' => 'nullable|required_if:tipo,producto',
                'servicio_id' => 'nullable|required_if:tipo,servicio',
                'cliente_id' => 'nullable',
                'descuento' => 'nullable|numeric|min:0',
                'observaciones' => 'nullable|string',
                'descripcion' => 'nullable|string', // Añadir esta validación
            ]);

            // PASO 2: Preparar datos iniciales
            $data = [
                'tipo' => $request->tipo,
                'cantidad' => (int) $request->cantidad,
                'metodo_pago' => $request->metodo_pago,
                'descuento' => $request->descuento ? (float) $request->descuento : 0,
                'observaciones' => $request->observaciones ?? '',
                'cliente_id' => !empty($request->cliente_id) ? (int) $request->cliente_id : null,
                'descripcion' => $request->descripcion ?? '', // ¡IMPORTANTE! Capturar la descripción del request
            ];

            // PASO 3: Obtener datos según el tipo desde la API
            if ($request->tipo === 'producto' && !empty($request->producto_id)) {
                $responseProducto = $this->apiService->get("productos/{$request->producto_id}");
                
                if ($responseProducto->successful()) {
                    $producto = $responseProducto->json()['producto'] ?? $responseProducto->json();
                    
                    $data['producto_id'] = (int) $request->producto_id;
                    $data['precio_unitario'] = (float) ($producto['precio_oferta'] ?? $producto['precio_venta'] ?? 0);
                    $data['servicio_id'] = null;
                    
                    // Si por alguna razón la descripción no vino del frontend, usar la de la API
                    if (empty($data['descripcion'])) {
                        $data['descripcion'] = $producto['nombre'] ?? 'Producto sin nombre';
                    }
                } else {
                    return back()->withErrors(['producto_id' => 'No se pudo obtener el producto de la API'])->withInput();
                }
            } 
            elseif ($request->tipo === 'servicio' && !empty($request->servicio_id)) {
                $responseServicio = $this->apiService->get("servicios/{$request->servicio_id}");
                
                if ($responseServicio->successful()) {
                    $servicio = $responseServicio->json()['servicio'] ?? $responseServicio->json();
                    
                    $data['servicio_id'] = (int) $request->servicio_id;
                    $data['precio_unitario'] = (float) ($servicio['precio_oferta'] ?? $servicio['precio_venta'] ?? 0);
                    $data['producto_id'] = null;
                    
                    // Si por alguna razón la descripción no vino del frontend, usar la de la API
                    if (empty($data['descripcion'])) {
                        $data['descripcion'] = $servicio['nombre'] ?? 'Servicio sin nombre';
                    }
                } else {
                    return back()->withErrors(['servicio_id' => 'No se pudo obtener el servicio de la API'])->withInput();
                }
            } 
            else {
                // Caso "otro" - validar que tenga descripción
                $request->validate([
                    'descripcion' => 'required|string|max:500',
                    'precio_unitario' => 'required|numeric|min:0',
                ]);
                
                $data['precio_unitario'] = (float) $request->precio_unitario;
                $data['producto_id'] = null;
                $data['servicio_id'] = null;
                // La descripción ya se capturó arriba
            }

            // PASO 4: Validar que tenemos todos los datos necesarios
            if (!isset($data['precio_unitario']) || $data['precio_unitario'] <= 0) {
                return back()->withErrors(['precio_unitario' => 'No se pudo determinar el precio'])->withInput();
            }

            if (!isset($data['descripcion']) || empty($data['descripcion'])) {
                return back()->withErrors(['descripcion' => 'No se pudo determinar la descripción'])->withInput();
            }

            // PASO 5: Calcular total
            $data['total'] = ($data['precio_unitario'] * $data['cantidad']) - $data['descuento'];
            
            // PASO 6: Agregar campos que faltan
            $data['usuario_id'] = auth()->id() ?? 1;
            $data['es_credito'] = $request->has('es_credito') ? (bool) $request->es_credito : false;

            // PASO 7: Enviar a la API
            $response = $this->apiService->post('ventas', $data);

            if ($response->successful()) {
                return redirect()->route('ventas.index')
                    ->with('success', 'Venta registrada exitosamente');
            }

            $errors = $response->json()['errors'] ?? ['Error al crear venta: ' . $response->body()];
            return back()->withErrors($errors)->withInput();
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error creando venta: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear venta: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mostrar detalles de una venta
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
}