<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar dashboard con estadísticas reales (optimizado con caché)
     */
    public function index()
    {
        $user = $this->apiService->getUser();
        $userRole = $user['rol'] ?? 'vendedor';
        
        // Cache key único por usuario (para respetar permisos)
        $cacheKey = 'dashboard_stats_' . ($user['id'] ?? 'guest');
        
        // Intentar obtener del caché (5 minutos de duración)
        $stats = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user) {
            return $this->loadDashboardStats();
        });

        return view('dashboard.index', compact('user', 'userRole', 'stats'));
    }

    /**
     * Cargar todas las estadísticas del dashboard
     */
    private function loadDashboardStats()
    {
        // Inicializar estadísticas
        $stats = [
            // Ventas
            'ventas_hoy' => 0,
            'ventas_semana' => 0,
            'ventas_mes' => 0,
            'total_ventas_hoy' => 0,
            'total_ventas_semana' => 0,
            'total_ventas_mes' => 0,
            'promedio_venta' => 0,
            'venta_maxima' => 0,
            
            // Clientes
            'total_clientes' => 0,
            'clientes_activos' => 0,
            'clientes_nuevos_mes' => 0,
            'clientes_con_ventas' => 0,
            'top_clientes' => [],
            
            // Productos
            'total_productos' => 0,
            'productos_stock_bajo' => 0,
            'productos_agotados' => 0,
            'valor_inventario' => 0,
            'top_productos' => [],
            
            // Servicios
            'total_servicios' => 0,
            'servicios_activos' => 0,
            'top_servicios' => [],
            
            // Créditos
            'creditos_activos' => 0,
            'creditos_abonados' => 0,
            'creditos_pagados' => 0,
            'capital_pendiente' => 0,
            'total_recuperado' => 0,
            
            // Usuarios
            'total_usuarios' => 0,
            'usuarios_activos' => 0,
            'usuarios_por_rol' => [
                'administrador' => 0,
                'vendedor' => 0,
                'analista' => 0
            ],
            
            // Proveedores
            'total_proveedores' => 0,
            'proveedores_activos' => 0,
            
            // Categorías
            'total_categorias' => 0,
            'categorias_activas' => 0,
            'categorias_por_nivel' => [],
            
            // Métodos de pago
            'metodos_pago' => [],
            
            // Alertas
            'alertas_stock_bajo' => [],
            'total_alertas' => 0
        ];

        try {
            // Ejecutar todas las llamadas API en paralelo
            $responses = $this->executeParallelRequests();
            
            // Procesar respuestas
            $this->processResumenResponse($responses['resumen'], $stats);
            $this->processVentasResponse($responses['ventas'], $stats);
            $this->processStockBajoResponse($responses['stock_bajo'], $stats);
            $this->processTopClientesResponse($responses['top_clientes'], $stats);
            $this->processCreditosResponse($responses['creditos'], $stats);
            $this->processProveedoresResponse($responses['proveedores'], $stats);
            $this->processCategoriasResponse($responses['categorias'], $stats);
            $this->processServiciosResponse($responses['servicios'], $stats);
            $this->processReporteVentasResponse($responses['reporte_ventas'], $stats);
            $this->processUsuariosResponse($responses['usuarios'], $stats);

        } catch (\Exception $e) {
            Log::error('Error cargando estadísticas: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Ejecutar todas las llamadas API en paralelo
     */
    private function executeParallelRequests()
    {
        $requests = [
            'resumen' => fn() => $this->apiService->get('reportes/resumen'),
            'ventas' => fn() => $this->apiService->get('ventas?estadisticas=true&limit=1'),
            'stock_bajo' => fn() => $this->apiService->get('productos/stock-bajo?limit=10'),
            'top_clientes' => fn() => $this->apiService->get('reportes/top-clientes', ['limite' => 5]),
            'creditos' => fn() => $this->apiService->get('creditos?estadisticas=true&limit=1'),
            'proveedores' => fn() => $this->apiService->get('proveedores?limit=100'),
            'categorias' => fn() => $this->apiService->get('categorias-flat'),
            'servicios' => fn() => $this->apiService->get('servicios?limit=100'),
            'reporte_ventas' => fn() => $this->apiService->get('reportes/ventas', [
                'fecha_inicio' => now()->startOfMonth()->format('Y-m-d'),
                'fecha_fin' => now()->format('Y-m-d'),
                'limit' => 1
            ]),
            'usuarios' => fn() => $this->apiService->get('users?limit=100')
        ];

        $responses = [];
        foreach ($requests as $key => $request) {
            try {
                $responses[$key] = $request();
            } catch (\Exception $e) {
                Log::warning("Error en request {$key}: " . $e->getMessage());
                $responses[$key] = null;
            }
        }

        return $responses;
    }

    // Métodos procesadores (simplificados)
    private function processResumenResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $data = $response->json()['data'] ?? [];
        
        $stats['ventas_hoy'] = $data['ventas']['hoy'] ?? 0;
        $stats['ventas_semana'] = $data['ventas']['semana'] ?? 0;
        $stats['ventas_mes'] = $data['ventas']['mes'] ?? 0;
        $stats['promedio_venta'] = $data['ventas']['promedio_diario'] ?? 0;
        $stats['total_clientes'] = $data['clientes']['total'] ?? 0;
        $stats['clientes_activos'] = $data['clientes']['activos'] ?? 0;
        $stats['clientes_nuevos_mes'] = $data['clientes']['nuevos_mes'] ?? 0;
        $stats['clientes_con_ventas'] = $data['clientes']['con_ventas'] ?? 0;
        $stats['total_productos'] = $data['productos']['total'] ?? 0;
        $stats['productos_stock_bajo'] = $data['productos']['stock_bajo'] ?? 0;
        $stats['productos_agotados'] = $data['productos']['agotados'] ?? 0;
        $stats['valor_inventario'] = $data['productos']['valor_inventario'] ?? 0;
        $stats['total_usuarios'] = $data['usuarios']['total'] ?? 0;
        $stats['usuarios_activos'] = $data['usuarios']['activos'] ?? 0;
    }

    private function processVentasResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $data = $response->json();
        
        if (isset($data['estadisticas'])) {
            $stats['total_ventas_hoy'] = $data['estadisticas']['totales']['hoy']['ventas'] ?? 0;
            $stats['total_ventas_semana'] = $data['estadisticas']['totales']['semana']['ventas'] ?? 0;
            $stats['total_ventas_mes'] = $data['estadisticas']['totales']['mes']['ventas'] ?? 0;
            $stats['metodos_pago'] = array_slice($data['estadisticas']['metodos_pago'] ?? [], 0, 5);
            $stats['top_productos'] = array_slice($data['estadisticas']['top_productos'] ?? [], 0, 5);
            $stats['top_servicios'] = array_slice($data['estadisticas']['top_servicios'] ?? [], 0, 5);
        }
    }

    private function processStockBajoResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $data = $response->json();
        $stats['alertas_stock_bajo'] = array_slice($data['productos'] ?? [], 0, 5);
        $stats['total_alertas'] = $data['total'] ?? 0;
        
        if (($data['total'] ?? 0) > $stats['productos_stock_bajo']) {
            $stats['productos_stock_bajo'] = $data['total'] ?? 0;
        }
    }

    private function processTopClientesResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $stats['top_clientes'] = $response->json()['clientes'] ?? [];
    }

    private function processCreditosResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $data = $response->json();
        
        if (isset($data['estadisticas'])) {
            $stats['creditos_activos'] = $data['estadisticas']['activos'] ?? 0;
            $stats['creditos_abonados'] = $data['estadisticas']['abonados'] ?? 0;
            $stats['creditos_pagados'] = $data['estadisticas']['pagados'] ?? 0;
            $stats['capital_pendiente'] = $data['estadisticas']['capital_pendiente_activos'] ?? 0;
            $stats['total_recuperado'] = $data['estadisticas']['total_recuperado'] ?? 0;
        }
    }

    private function processProveedoresResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $data = $response->json()['proveedores'] ?? [];
        $stats['total_proveedores'] = count($data);
        $stats['proveedores_activos'] = count(array_filter($data, fn($p) => ($p['estado'] ?? '') === 'activo'));
    }

    private function processCategoriasResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $data = $response->json()['categorias'] ?? [];
        $stats['total_categorias'] = count($data);
        $stats['categorias_activas'] = count(array_filter($data, fn($c) => ($c['estado'] ?? '') === 'activo'));
        
        // Simplificar: contar por nivel basado en los datos existentes
        $stats['categorias_por_nivel']['nivel_0'] = count(array_filter($data, fn($c) => ($c['nivel'] ?? 0) == 0));
        $stats['categorias_por_nivel']['nivel_1'] = count(array_filter($data, fn($c) => ($c['nivel'] ?? 0) == 1));
    }

    private function processServiciosResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $data = $response->json()['servicios']['data'] ?? [];
        $stats['total_servicios'] = count($data);
        $stats['servicios_activos'] = count(array_filter($data, fn($s) => ($s['estado'] ?? '') === 'activo'));
    }

    private function processReporteVentasResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $data = $response->json();
        $stats['venta_maxima'] = $data['resumen']['venta_maxima'] ?? 0;
    }

    private function processUsuariosResponse($response, &$stats)
    {
        if (!$response || !$response->successful()) return;
        $data = $response->json()['users'] ?? [];
        
        $stats['usuarios_por_rol'] = [
            'administrador' => 0,
            'vendedor' => 0,
            'analista' => 0
        ];
        
        foreach ($data as $usuario) {
            $rol = $usuario['rol'] ?? '';
            if (isset($stats['usuarios_por_rol'][$rol])) {
                $stats['usuarios_por_rol'][$rol]++;
            }
        }
    }

    /**
     * Endpoint para refrescar caché via AJAX
     */
    public function refreshStats()
    {
        try {
            $user = $this->apiService->getUser();
            $cacheKey = 'dashboard_stats_' . ($user['id'] ?? 'guest');
            
            // Forzar recarga de estadísticas
            $stats = $this->loadDashboardStats();
            Cache::put($cacheKey, $stats, now()->addMinutes(5));
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}