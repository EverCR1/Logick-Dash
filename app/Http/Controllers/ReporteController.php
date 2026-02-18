<?php
// Front: app/Http/Controllers/ReporteController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class ReporteController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Página principal de reportes
     */
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Panel de resumen general (antes dashboard)
     */
    public function resumen()
    {
        $response = $this->apiService->get('reportes/resumen'); // Cambiado de dashboard a resumen
        
        if ($response->successful()) {
            $data = $response->json()['data'] ?? [];
        } else {
            $data = [
                'ventas' => ['hoy' => 0, 'semana' => 0, 'mes' => 0, 'total' => 0, 'promedio_diario' => 0],
                'clientes' => ['total' => 0, 'activos' => 0, 'nuevos_mes' => 0, 'con_ventas' => 0],
                'productos' => ['total' => 0, 'stock_bajo' => 0, 'agotados' => 0, 'valor_inventario' => 0],
                'usuarios' => ['total' => 0, 'activos' => 0]
            ];
        }

        return view('reportes.resumen', compact('data')); // Vista renombrada
    }

    /**
     * Reporte de ventas
     */
    public function ventas(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        
        $response = $this->apiService->get('reportes/ventas', [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'cliente_id' => $request->get('cliente_id'),
            'vendedor_id' => $request->get('vendedor_id'),
            'metodo_pago' => $request->get('metodo_pago')
        ]);

        $data = $response->successful() ? $response->json() : [
            'ventas' => [],
            'resumen' => [
                'total_ventas' => 0,
                'monto_total' => 0,
                'promedio_venta' => 0,
                'por_metodo_pago' => []
            ]
        ];

        // Obtener datos para filtros
        $clientes = $this->getClientes();
        $vendedores = $this->getVendedores();

        return view('reportes.ventas', compact('data', 'fechaInicio', 'fechaFin', 'clientes', 'vendedores'));
    }

    /**
     * Reporte de productos más vendidos
     */
    public function productosMasVendidos(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        
        $response = $this->apiService->get('reportes/productos-mas-vendidos', [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'limite' => $request->get('limite', 20)
        ]);

        $data = $response->successful() ? $response->json() : ['productos' => []];

        return view('reportes.productos-mas-vendidos', compact('data', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Reporte de inventario
     */
    public function inventario(Request $request)
    {
        $response = $this->apiService->get('reportes/inventario', [
            'categoria_id' => $request->get('categoria_id'),
            'proveedor_id' => $request->get('proveedor_id'),
            'estado_stock' => $request->get('estado_stock', 'todos')
        ]);

        $data = $response->successful() ? $response->json() : [
            'productos' => [],
            'resumen' => [
                'total_productos' => 0,
                'valor_total_inventario' => 0,
                'valor_venta_total' => 0,
                'productos_bajo_stock' => 0,
                'productos_agotados' => 0
            ]
        ];

        // Obtener categorías y proveedores para filtros
        $categorias = $this->getCategorias();
        $proveedores = $this->getProveedores();

        return view('reportes.inventario', compact('data', 'categorias', 'proveedores'));
    }

    /**
     * Top clientes
     */
    public function topClientes(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        
        $response = $this->apiService->get('reportes/top-clientes', [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'limite' => $request->get('limite', 10)
        ]);

        $data = $response->successful() ? $response->json() : ['clientes' => []];

        return view('reportes.top-clientes', compact('data', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Rendimiento de vendedores
     */
    public function rendimientoVendedores(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        
        $response = $this->apiService->get('reportes/rendimiento-vendedores', [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ]);

        $data = $response->successful() ? $response->json() : ['vendedores' => []];

        return view('reportes.rendimiento-vendedores', compact('data', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Métodos auxiliares para obtener datos de filtros
     */
    private function getClientes()
    {
        $response = $this->apiService->get('clientes', ['por_pagina' => 100]);
        return $response->successful() ? ($response->json()['clientes']['data'] ?? []) : [];
    }

    private function getVendedores()
    {
        $response = $this->apiService->get('users', ['rol' => 'vendedor']);
        return $response->successful() ? ($response->json()['users'] ?? []) : [];
    }

    private function getCategorias()
    {
        $response = $this->apiService->get('categorias-flat');
        $categoriasRaw = $response->successful() ? ($response->json()['categorias'] ?? []) : [];
        
        $categorias = [];
        foreach ($categoriasRaw as $id => $nombre) {
            $categorias[] = [
                'id' => $id,
                'nombre' => preg_replace('/^[\s\-]+/', '', $nombre)
            ];
        }
        return $categorias;
    }

    private function getProveedores()
    {
        $response = $this->apiService->get('proveedores');
        return $response->successful() ? ($response->json()['proveedores'] ?? []) : [];
    }

    /**
     * Reporte de ventas de los últimos 30 días (para gráfico)
     */
    public function ventas30Dias()
    {
        try {
            // Obtener TODAS las ventas (sin filtro de fecha en la API)
            $response = $this->apiService->get('ventas');
            
            $ventasPorDia = [];
            
            if ($response->successful()) {
                $ventasData = $response->json();
                
                // Obtener el array de ventas de la estructura de paginación
                $todasLasVentas = [];
                
                if (isset($ventasData['ventas']['data'])) {
                    // Estructura con paginación: ventas.data es un array
                    $todasLasVentas = $ventasData['ventas']['data'];
                } elseif (isset($ventasData['ventas']) && is_array($ventasData['ventas'])) {
                    // Estructura simple: ventas es un array
                    $todasLasVentas = $ventasData['ventas'];
                }
                
                // Definir el rango de fechas (últimos 30 días)
                $fechaInicio = now()->subDays(30)->startOfDay();
                $fechaFin = now()->endOfDay();
                
                // Inicializar array para ventas por día
                $ventasPorDia = [];
                
                // Filtrar ventas por created_at
                foreach ($todasLasVentas as $venta) {
                    if (isset($venta['created_at'])) {
                        $fechaVenta = \Carbon\Carbon::parse($venta['created_at']);
                        
                        // Verificar si la venta está dentro del rango
                        if ($fechaVenta->between($fechaInicio, $fechaFin)) {
                            $fechaKey = $fechaVenta->format('Y-m-d');
                            
                            if (!isset($ventasPorDia[$fechaKey])) {
                                $ventasPorDia[$fechaKey] = [
                                    'total' => 0,
                                    'cantidad' => 0
                                ];
                            }
                            
                            $ventasPorDia[$fechaKey]['total'] += floatval($venta['total'] ?? 0);
                            $ventasPorDia[$fechaKey]['cantidad']++;
                        }
                    }
                }
            }
            
            // Formatear para el gráfico (garantizar 31 días)
            $ventas30Dias = [];
            $fechaActual = now()->subDays(30)->startOfDay();
            
            for ($i = 0; $i <= 30; $i++) {
                $fecha = $fechaActual->copy()->addDays($i);
                $fechaKey = $fecha->format('Y-m-d');
                
                $ventas30Dias[] = [
                    'fecha' => $fecha->format('d/m'),
                    'fecha_completa' => $fechaKey,
                    'total' => $ventasPorDia[$fechaKey]['total'] ?? 0,
                    'cantidad' => $ventasPorDia[$fechaKey]['cantidad'] ?? 0
                ];
            }
            
            return view('reportes.ventas-30-dias', compact('ventas30Dias'));
            
        } catch (\Exception $e) {
            Log::error('Error en ventas30Dias: ' . $e->getMessage());
            
            // Array vacío en caso de error
            $ventas30Dias = [];
            $fechaActual = now()->subDays(30);
            
            for ($i = 0; $i <= 30; $i++) {
                $fecha = $fechaActual->copy()->addDays($i);
                $ventas30Dias[] = [
                    'fecha' => $fecha->format('d/m'),
                    'fecha_completa' => $fecha->format('Y-m-d'),
                    'total' => 0,
                    'cantidad' => 0
                ];
            }
            
            return view('reportes.ventas-30-dias', compact('ventas30Dias'));
        }
    }
}