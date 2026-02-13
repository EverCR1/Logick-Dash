<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class DashboardController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar dashboard
     */
    public function index()
    {
        $user = $this->apiService->getUser();
        
        // Obtener estadísticas
        $stats = [
            'total_usuarios' => 0,
            'total_proveedores' => 0,
            'total_categorias' => 0,
            'ventas_hoy' => 0,
            'ventas_semana' => 0,
            'ventas_mes' => 0,
            'total_ventas_hoy' => 0,
            'ventas_pendientes' => 0,
        ];
        
        // Intentar obtener datos reales
        try {
            $usuariosResponse = $this->apiService->get('users');
            if ($usuariosResponse->successful()) {
                $stats['total_usuarios'] = count($usuariosResponse->json()['users'] ?? []);
            }
            
            $proveedoresResponse = $this->apiService->get('proveedores');
            if ($proveedoresResponse->successful()) {
                $stats['total_proveedores'] = count($proveedoresResponse->json()['proveedores'] ?? []);
            }
            
            $categoriasResponse = $this->apiService->get('categorias');
            if ($categoriasResponse->successful()) {
                $stats['total_categorias'] = count($categoriasResponse->json()['categorias'] ?? []);
            }
            
            // Obtener estadísticas de ventas
            $ventasResponse = $this->apiService->get('ventas');
            if ($ventasResponse->successful()) {
                $ventasData = $ventasResponse->json();
                if (isset($ventasData['estadisticas'])) {
                    $stats['ventas_hoy'] = $ventasData['estadisticas']['totales']['hoy']['total'] ?? 0;
                    $stats['ventas_semana'] = $ventasData['estadisticas']['totales']['semana']['total'] ?? 0;
                    $stats['ventas_mes'] = $ventasData['estadisticas']['totales']['mes']['total'] ?? 0;
                    $stats['total_ventas_hoy'] = $ventasData['estadisticas']['totales']['hoy']['ventas'] ?? 0;
                }
            }
            
        } catch (\Exception $e) {
            \Log::warning('Error obteniendo estadísticas para dashboard: ' . $e->getMessage());
        }
        
        // Obtener alertas de stock bajo
        $alertasStockBajo = 0;
        try {
            $stockBajoResponse = $this->apiService->get('productos/stock-bajo');
            if ($stockBajoResponse->successful()) {
                $stockData = $stockBajoResponse->json();
                $alertasStockBajo = $stockData['total'] ?? 0;
            }
        } catch (\Exception $e) {
            \Log::warning('Error obteniendo stock bajo: ' . $e->getMessage());
        }
        
        return view('dashboard.index', compact('user', 'stats', 'alertasStockBajo'));
    }
}