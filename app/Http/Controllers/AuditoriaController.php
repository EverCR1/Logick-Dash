<?php
// app/Http/Controllers/AuditoriaFrontController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

class AuditoriaController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar vista principal de auditoría
     */
    public function index(Request $request)
    {
        // Obtener módulos para filtros
        $modulosResponse = $this->apiService->get('auditoria/modulos');
        $modulos = $modulosResponse->successful() ? $modulosResponse->json()['modulos'] ?? [] : [];

        // Parámetros de filtrado
        $params = [
            'modulo' => $request->get('modulo', 'todos'),
            'accion' => $request->get('accion', 'todos'),
            'usuario_id' => $request->get('usuario_id', 'todos'),
            'fecha_inicio' => $request->get('fecha_inicio', now()->subDays(7)->format('Y-m-d')),
            'fecha_fin' => $request->get('fecha_fin', now()->format('Y-m-d')),
            'busqueda' => $request->get('busqueda', ''),
            'per_page' => 20, 
            'page' => $request->get('page', 1)
        ];

        // Llamar a la API
        $response = $this->apiService->get('auditoria', $params);

        if ($response->successful()) {
            $data = $response->json();
            $auditoria = $data['auditoria'] ?? [];
            
            // Transformar los links de paginación
            if (isset($auditoria['links'])) {
                $auditoria['links'] = $this->transformPaginationLinks($auditoria['links'], $request->path());
            }
            
            // Transformar las URLs en los links de paginación
            if (isset($auditoria['links'])) {
                foreach ($auditoria['links'] as &$link) {
                    if (isset($link['url']) && $link['url']) {
                        $parsedUrl = parse_url($link['url']);
                        parse_str($parsedUrl['query'] ?? '', $queryParams);
                        $page = $queryParams['page'] ?? null;
                        
                        if ($page) {
                            // Mantener todos los filtros actuales
                            $filtros = [
                                'page' => $page,
                                'modulo' => $params['modulo'],
                                'accion' => $params['accion'],
                                'usuario_id' => $params['usuario_id'],
                                'fecha_inicio' => $params['fecha_inicio'],
                                'fecha_fin' => $params['fecha_fin'],
                                'busqueda' => $params['busqueda']
                            ];
                            // Eliminar filtros con valor 'todos'
                            $filtros = array_filter($filtros, function($value) {
                                return $value !== '' && $value !== null && $value !== 'todos';
                            });
                            
                            $link['url'] = route('auditoria.index', $filtros);
                        }
                    }
                }
            }
        } else {
            $auditoria = [
                'data' => [],
                'links' => [],
                'meta' => []
            ];
        }

        return view('auditoria.index', compact('auditoria', 'modulos', 'params'));
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
     * Mostrar detalles de un log
     */
    public function show($id)
    {
        $response = $this->apiService->get("auditoria/{$id}");

        if ($response->successful()) {
            $data = $response->json();
            $log = $data['auditoria'] ?? null;

            return view('auditoria.show', compact('log'));
        }

        return redirect()->route('auditoria.index')
            ->with('error', 'Registro de auditoría no encontrado');
    }

    /**
     * Ver estadísticas
     */
    public function estadisticas(Request $request)
    {
        $dias = $request->get('dias', 30);
        
        $response = $this->apiService->get('auditoria/estadisticas', ['dias' => $dias]);

        if ($response->successful()) {
            $data = $response->json();
            $estadisticas = $data['estadisticas'] ?? [];
            
            // Procesar datos para los gráficos - sin usar helper
            if (isset($estadisticas['acciones_por_dia'])) {
                foreach ($estadisticas['acciones_por_dia'] as &$dia) {
                    // Formatear fecha directamente con Carbon
                    $dia['fecha'] = \Carbon\Carbon::parse($dia['fecha'])->format('Y-m-d');
                }
            }
        } else {
            $estadisticas = [
                'total_acciones' => 0,
                'acciones_por_dia' => [],
                'acciones_por_tipo' => [],
                'acciones_por_modulo' => [],
                'usuarios_activos' => [],
                'fecha_inicio' => now()->subDays($dias)->format('Y-m-d'),
                'fecha_fin' => now()->format('Y-m-d')
            ];
        }

        return view('auditoria.estadisticas', compact('estadisticas', 'dias'));
    }
}