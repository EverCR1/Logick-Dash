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
    } catch (\Exception $e) {
        \Log::warning('Error obteniendo estadísticas para dashboard: ' . $e->getMessage());
    }
    
    return view('dashboard.index', compact('user', 'stats'));
}
}