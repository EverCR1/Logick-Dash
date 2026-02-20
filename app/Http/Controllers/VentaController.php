<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

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
            $page = $request->get('page', 1);
            
            // Llamar a la API con filtros y paginación
            $response = $this->apiService->get('ventas', [
                'page' => $page,
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
                
                // Transformar los links de paginación
                if (isset($ventas['links'])) {
                    $ventas['links'] = $this->transformPaginationLinks($ventas['links'], $request->path());
                }
                
                // Transformar las URLs en los links de paginación
                if (isset($ventas['links'])) {
                    foreach ($ventas['links'] as &$link) {
                        if (isset($link['url']) && $link['url']) {
                            $parsedUrl = parse_url($link['url']);
                            parse_str($parsedUrl['query'] ?? '', $queryParams);
                            $page = $queryParams['page'] ?? null;
                            
                            if ($page) {
                                // Mantener todos los filtros actuales
                                $filtros = [
                                    'page' => $page,
                                    'search' => $search,
                                    'estado' => $estado,
                                    'tipo' => $tipo,
                                    'fecha_inicio' => $fecha_inicio,
                                    'fecha_fin' => $fecha_fin
                                ];
                                // Eliminar filtros vacíos
                                $filtros = array_filter($filtros, function($value) {
                                    return $value !== '' && $value !== null && $value !== 'todos';
                                });
                                
                                $link['url'] = route('ventas.index', $filtros);
                            }
                        }
                    }
                }
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
                'tipo' => 'todos',
                'fecha_inicio' => null,
                'fecha_fin' => null
            ])->with('error', 'Error al cargar las ventas');
        }
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
        // PASO 0: Decodificar items si viene como string JSON (por si acaso)
        if ($request->has('items') && is_string($request->items)) {
            $request->merge([
                'items' => json_decode($request->items, true)
            ]);
        }

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
            'crear_credito' => 'nullable|in:0,1',
            'credito_data' => 'nullable|json',
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

        // PASO 4: Enviar a la API para crear la venta
        $response = $this->apiService->post('ventas', $data);

        if (!$response->successful()) {
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
        }

        // Obtener datos de la venta creada
        $ventaResponse = $response->json();
        $ventaId = $ventaResponse['venta']['id'] ?? null;

        // PASO 5: Si es venta a crédito, crear el crédito
        if ($request->crear_credito == '1' && $request->credito_data) {
            $creditoData = json_decode($request->credito_data, true);
            
            // Agregar referencia a la venta si la API lo soporta
            if ($ventaId) {
                $creditoData['venta_id'] = $ventaId;
            }
            
            // Enviar a la API para crear el crédito
            $creditoResponse = $this->apiService->post('creditos', $creditoData);
            
            if (!$creditoResponse->successful()) {
                // Log del error pero no interrumpimos el flujo
                Log::warning('Venta creada pero crédito falló:', [
                    'venta_id' => $ventaId,
                    'error' => $creditoResponse->json()
                ]);
                
                return redirect()->route('ventas.index')
                    ->with('warning', 'Venta creada exitosamente pero hubo un problema al generar el crédito. Verifique manualmente.');
            }
            
            $creditoResult = $creditoResponse->json();
            return redirect()->route('ventas.index')
                ->with('success', 'Venta registrada exitosamente con crédito asociado');
        }

        return redirect()->route('ventas.index')
            ->with('success', 'Venta registrada exitosamente');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        throw $e;
    } catch (\Exception $e) {
        Log::error('Error creando venta: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
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
                    // Transformar detalles a items si es necesario
                    if (isset($venta['detalles']) && !isset($venta['items'])) {
                        $venta['items'] = [];
                        $venta['total_subtotal'] = 0;
                        $venta['total_descuento'] = 0;
                        
                        foreach ($venta['detalles'] as $detalle) {
                            // Determinar el tipo (producto o servicio)
                            $tipo = 'otro';
                            $descripcion = '';
                            $referencia = '';
                            $producto_id = null;
                            $servicio_id = null;
                            
                            if (isset($detalle['producto']) && $detalle['producto']) {
                                $tipo = 'producto';
                                $descripcion = $detalle['producto']['nombre'] ?? 'Producto sin nombre';
                                $referencia = $detalle['producto']['codigo'] ?? $detalle['producto']['referencia'] ?? '';
                                $producto_id = $detalle['producto']['id'] ?? null;
                            } elseif (isset($detalle['servicio']) && $detalle['servicio']) {
                                $tipo = 'servicio';
                                $descripcion = $detalle['servicio']['nombre'] ?? 'Servicio sin nombre';
                                $referencia = $detalle['servicio']['codigo'] ?? '';
                                $servicio_id = $detalle['servicio']['id'] ?? null;
                            } else {
                                // Si no hay relaciones, usar datos del detalle
                                $tipo = $detalle['tipo'] ?? 'otro';
                                $descripcion = $detalle['descripcion'] ?? 'Item sin descripción';
                                $referencia = $detalle['referencia'] ?? '';
                            }
                            
                            // Calcular subtotal y total
                            $cantidad = $detalle['cantidad'] ?? 1;
                            $precio_unitario = $detalle['precio_unitario'] ?? $detalle['precio'] ?? 0;
                            $descuento = $detalle['descuento'] ?? 0;
                            $subtotal = $cantidad * $precio_unitario;
                            $total = $subtotal - $descuento;
                            
                            // Acumular totales
                            $venta['total_subtotal'] += $subtotal;
                            $venta['total_descuento'] += $descuento;
                            
                            // Crear item transformado
                            $venta['items'][] = [
                                'id' => $detalle['id'] ?? null,
                                'tipo' => $tipo,
                                'descripcion' => $descripcion,
                                'referencia' => $referencia,
                                'cantidad' => $cantidad,
                                'precio_unitario' => $precio_unitario,
                                'descuento' => $descuento,
                                'subtotal' => $subtotal,
                                'total' => $total,
                                'producto_id' => $producto_id,
                                'servicio_id' => $servicio_id,
                                'observaciones' => $detalle['observaciones'] ?? ''
                            ];
                        }
                    }
                    
                    // Asegurar que el total esté presente
                    if (!isset($venta['total']) && isset($venta['total_subtotal']) && isset($venta['total_descuento'])) {
                        $venta['total'] = $venta['total_subtotal'] - $venta['total_descuento'];
                    }
                    
                    // Asegurar fecha
                    if (!isset($venta['fecha'])) {
                        $venta['fecha'] = $venta['created_at'] ?? now();
                    }
                    
                    return view('ventas.show', compact('venta'));
                }
                
                // Si no hay venta, redirigir con error
                return redirect()->route('ventas.index')
                    ->with('error', 'No se encontraron datos de la venta');
            }
            
            // Si la respuesta no fue exitosa
            return redirect()->route('ventas.index')
                ->with('error', 'Error al obtener los detalles de la venta');
            
        } catch (\Exception $e) {
            Log::error('Error en show de ventas: ' . $e->getMessage());
            return redirect()->route('ventas.index')
                ->with('error', 'Error al cargar los detalles de la venta: ' . $e->getMessage());
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

    /**
     * Descargar comprobante de venta en PDF con formato de la imagen
     */
    public function descargarComprobante($id)
    {
        try {
            // Obtener datos de la venta
            $response = $this->apiService->get("ventas/{$id}");
            
            if (!$response->successful()) {
                return redirect()->route('ventas.show', $id)
                    ->with('error', 'Error al obtener los datos de la venta para generar el comprobante');
            }

            $data = $response->json();
            $venta = $data['venta'] ?? null;

            if (!$venta) {
                return redirect()->route('ventas.show', $id)
                    ->with('error', 'No se encontraron datos de la venta');
            }

            // Transformar y formatear los datos para el comprobante
            $ventaFormateada = $this->formatearVentaParaComprobante($venta);

            // Generar PDF
            $pdf = Pdf::loadView('ventas.pdf.comprobante', ['venta' => $ventaFormateada]);
            
            // Configurar el papel (tamaño carta)
            $pdf->setPaper('letter', 'portrait');
            
            // Configurar opciones adicionales (opcional)
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);

            // Descargar el PDF con nombre personalizado
            $nombreArchivo = 'comprobante-venta-' . $id . '-' . date('Y-m-d') . '.pdf';
            
            return $pdf->download($nombreArchivo);

        } catch (\Exception $e) {
            Log::error('Error generando comprobante PDF: ' . $e->getMessage(), [
                'venta_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('ventas.show', $id)
                ->with('error', 'Error al generar el comprobante: ' . $e->getMessage());
        }
    }

    /**
     * Formatear los datos de la venta para que coincidan con el formato de la imagen
     */
    private function formatearVentaParaComprobante($venta)
    {
        // Configurar zona horaria de Guatemala
        $zonaHoraria = 'America/Guatemala';
        
        // Si no hay items, intentar transformar desde detalles
        if (!isset($venta['items']) && isset($venta['detalles'])) {
            $venta['items'] = [];
            $venta['total_subtotal'] = 0;
            $venta['total_descuento'] = 0;
            
            foreach ($venta['detalles'] as $detalle) {
                // Determinar el tipo
                $tipo = 'otro';
                $descripcion = '';
                $referencia = '';
                
                if (isset($detalle['producto']) && $detalle['producto']) {
                    $tipo = 'producto';
                    $descripcion = $detalle['producto']['nombre'] ?? 'Producto sin nombre';
                    $referencia = $detalle['producto']['codigo'] ?? $detalle['producto']['referencia'] ?? '';
                } elseif (isset($detalle['servicio']) && $detalle['servicio']) {
                    $tipo = 'servicio';
                    $descripcion = $detalle['servicio']['nombre'] ?? 'Servicio sin nombre';
                    $referencia = $detalle['servicio']['codigo'] ?? '';
                } else {
                    $descripcion = $detalle['descripcion'] ?? 'Item sin descripción';
                    $referencia = $detalle['referencia'] ?? '';
                }
                
                // Calcular valores
                $cantidad = $detalle['cantidad'] ?? 1;
                $precio_unitario = $detalle['precio_unitario'] ?? $detalle['precio'] ?? 0;
                $descuento = $detalle['descuento'] ?? 0;
                $subtotal = $cantidad * $precio_unitario;
                
                // Acumular totales
                $venta['total_subtotal'] += $subtotal;
                $venta['total_descuento'] += $descuento;
                
                // Crear item
                $venta['items'][] = [
                    'tipo' => $tipo,
                    'descripcion' => $descripcion,
                    'referencia' => $referencia,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio_unitario,
                    'descuento' => $descuento,
                    'subtotal' => $subtotal
                ];
            }
            
            // Calcular total
            $venta['total'] = ($venta['total'] ?? 0) ?: ($venta['total_subtotal'] - $venta['total_descuento']);
        }

        // Formatear items para que coincidan con la imagen
        foreach ($venta['items'] as $index => &$item) {
            // Formatear descuento según el tipo de item para que coincida con la imagen
            if ($item['descuento'] > 0) {
                // Para el primer item (impresión de folletos) - descuento por unidad
                if (strpos(strtolower($item['descripcion']), 'folletos') !== false) {
                    $item['descuento_formateado'] = 'Q ' . number_format($item['descuento'] / $item['cantidad'], 2) . ' c/u';
                }
                // Para items 3 y 4 (si tienen descuento)
                elseif ($index == 2 || $index == 3) {
                    $item['descuento_formateado'] = 'Q ' . number_format($item['descuento'], 2);
                } else {
                    $item['descuento_formateado'] = 'Q ' . number_format($item['descuento'], 2);
                }
            } else {
                $item['descuento_formateado'] = 'Q 0.00';
            }

            // Formatear subtotal
            $item['subtotal_formateado'] = 'Q ' . number_format($item['subtotal'] ?? ($item['cantidad'] * $item['precio_unitario']), 2);
            
            // Formatear precio unitario
            $item['precio_unitario_formateado'] = 'Q ' . number_format($item['precio_unitario'], 2);
        }

        // NO agregar nota fija, solo usar las observaciones existentes
        // Si hay observaciones, las usamos, si no, simplemente no mostramos nada
        if (isset($venta['observaciones']) && !empty($venta['observaciones'])) {
            $venta['nota_descuentos'] = $venta['observaciones'];
        } else {
            // No establecer 'nota_descuentos' para que no se muestre nada
            $venta['nota_descuentos'] = null;
        }
        
        // Asegurar que tenemos la fecha con hora de Guatemala
        $fechaOriginal = $venta['fecha'] ?? $venta['fecha_venta'] ?? $venta['created_at'] ?? now();
        
        // Parsear la fecha y aplicar zona horaria de Guatemala
        $fecha = \Carbon\Carbon::parse($fechaOriginal);
        $fecha->setTimezone($zonaHoraria);
        
        // Formatear fecha y hora (ej: 15/12/2025 14:30)
        $venta['fecha_formateada'] = $fecha->format('d/m/Y H:i');
        
        // También guardar fecha separada por si necesitamos usarla en otros formatos
        $venta['fecha_solo'] = $fecha->format('d/m/Y');
        $venta['hora_solo'] = $fecha->format('H:i');

        return $venta;
    }
    /**
     * Vista previa del comprobante en el navegador (opcional)
     */
    public function previsualizarComprobante($id)
    {
        try {
            // Obtener datos de la venta
            $response = $this->apiService->get("ventas/{$id}");
            
            if (!$response->successful()) {
                return redirect()->route('ventas.show', $id)
                    ->with('error', 'Error al obtener los datos de la venta');
            }

            $data = $response->json();
            $venta = $data['venta'] ?? null;

            if (!$venta) {
                return redirect()->route('ventas.show', $id)
                    ->with('error', 'No se encontraron datos de la venta');
            }

            // Formatear datos
            $ventaFormateada = $this->formatearVentaParaComprobante($venta);

            // Generar PDF para visualizar en el navegador
            $pdf = Pdf::loadView('ventas.pdf.comprobante', ['venta' => $ventaFormateada]);
            $pdf->setPaper('letter', 'portrait');
            
            // Mostrar en el navegador en lugar de descargar
            return $pdf->stream('comprobante-venta-' . $id . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error previsualizando comprobante: ' . $e->getMessage());
            
            return redirect()->route('ventas.show', $id)
                ->with('error', 'Error al previsualizar el comprobante');
        }
    }
}