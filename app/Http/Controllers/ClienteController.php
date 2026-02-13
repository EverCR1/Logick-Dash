<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class ClienteController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar lista de clientes
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $estado = $request->get('estado', 'todos');
        $tipo = $request->get('tipo', 'todos');
        
        // Llamar a la API igual que en servicios
        $response = $this->apiService->get('clientes', [
            'query' => $search,
            'estado' => $estado,
            'tipo' => $tipo
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            // DEBUG: Ver qué devuelve la API
            // dd($data);
            
            $clientes = $data['clientes'] ?? [];
        } else {
            $clientes = [
                'data' => [],
                'links' => [],
                'meta' => []
            ];
        }

        return view('clientes.index', compact('clientes', 'search', 'estado', 'tipo'));
    }


    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Almacenar nuevo cliente
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:200',
            'nit' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'tipo' => 'required|in:natural,juridico',
            'notas' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['estado'] = $request->estado ?? 'activo';

        $response = $this->apiService->post('clientes', $data);

        if ($response->successful()) {
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente creado exitosamente');
        }

        $errors = $response->json()['errors'] ?? ['Error al crear cliente'];
        return back()->withErrors($errors)->withInput();
    }

    /**
     * Mostrar detalles del cliente
     */
    public function show($id)
    {
        $response = $this->apiService->get("clientes/{$id}");
        
        if ($response->successful()) {
            $data = $response->json();
            $cliente = $data['cliente'] ?? null;
            $estadisticas = $data['estadisticas'] ?? null;
            
            return view('clientes.show', compact('cliente', 'estadisticas'));
        }

        return redirect()->route('clientes.index')
            ->with('error', 'Cliente no encontrado');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $response = $this->apiService->get("clientes/{$id}");
        
        if ($response->successful()) {
            $data = $response->json();
            $cliente = $data['cliente'] ?? null;
            
            return view('clientes.edit', compact('cliente'));
        }

        return redirect()->route('clientes.index')
            ->with('error', 'Cliente no encontrado');
    }

    /**
     * Actualizar cliente
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:200',
            'nit' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'tipo' => 'required|in:natural,juridico',
            'estado' => 'required|in:activo,inactivo',
            'notas' => 'nullable|string',
        ]);

        $response = $this->apiService->put("clientes/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente actualizado exitosamente');
        }

        $errors = $response->json()['errors'] ?? ['Error al actualizar cliente'];
        return back()->withErrors($errors)->withInput();
    }

    /**
     * Eliminar cliente
     */
    public function destroy($id)
    {
        $response = $this->apiService->delete("clientes/{$id}");

        if ($response->successful()) {
            $data = $response->json();
            $message = $data['message'] ?? 'Cliente eliminado exitosamente';
            
            return redirect()->route('clientes.index')
                ->with('success', $message);
        }

        return redirect()->route('clientes.index')
            ->with('error', 'Error al eliminar cliente');
    }

    /**
     * Cambiar estado del cliente
     */
    public function changeStatus($id)
    {
        $response = $this->apiService->post("clientes/{$id}/change-status", []);

        if ($response->successful()) {
            return redirect()->route('clientes.index')
                ->with('success', 'Estado del cliente cambiado exitosamente');
        }

        return redirect()->route('clientes.index')
            ->with('error', 'Error al cambiar estado');
    }

    /**
     * Ver estadísticas de clientes
     */
    public function estadisticas()
    {
        $response = $this->apiService->get('clientes/estadisticas');
        
        if ($response->successful()) {
            $data = $response->json();
            $estadisticas = $data['estadisticas'] ?? [];
            $clientesFrecuentes = [];
            
            // Obtener clientes frecuentes
            $responseFrecuentes = $this->apiService->get('clientes/frecuentes');
            if ($responseFrecuentes->successful()) {
                $dataFrecuentes = $responseFrecuentes->json();
                $clientesFrecuentes = $dataFrecuentes['clientes'] ?? [];
            }
            
            return view('clientes.estadisticas', compact('estadisticas', 'clientesFrecuentes'));
        }

        return redirect()->route('clientes.index')
            ->with('error', 'Error al cargar estadísticas');
    }
}