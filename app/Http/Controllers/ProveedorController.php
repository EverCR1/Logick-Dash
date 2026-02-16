<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class ProveedorController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        $response = $this->apiService->get('proveedores');
        
        if ($response->successful()) {
            $proveedores = $response->json()['proveedores'] ?? [];
        } else {
            $proveedores = [];
        }

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:200',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $response = $this->apiService->post('proveedores', $request->all());

        if ($response->successful()) {
            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor creado exitosamente');
        }

        return back()->withErrors($response->json()['errors'] ?? ['Error al crear proveedor'])
                    ->withInput();
    }

    public function show($id)
    {
        $response = $this->apiService->get("proveedores/{$id}");
        
        if ($response->successful()) {
            $proveedor = $response->json()['proveedor'] ?? null;
            return view('proveedores.show', compact('proveedor'));
        }

        return redirect()->route('proveedores.index')
            ->with('error', 'Proveedor no encontrado');
    }

    public function edit($id)
    {
        $response = $this->apiService->get("proveedores/{$id}");
        
        if ($response->successful()) {
            $proveedor = $response->json()['proveedor'] ?? null;
            return view('proveedores.edit', compact('proveedor'));
        }

        return redirect()->route('proveedores.index')
            ->with('error', 'Proveedor no encontrado');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:200',
            'email' => 'nullable|email|max:100',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $response = $this->apiService->put("proveedores/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor actualizado exitosamente');
        }

        return back()->withErrors($response->json()['errors'] ?? ['Error al actualizar proveedor'])
                    ->withInput();
    }

    public function destroy($id)
    {
        $response = $this->apiService->delete("proveedores/{$id}");

        if ($response->successful()) {
            return redirect()->route('proveedores.index')
                ->with('success', 'Proveedor eliminado exitosamente');
        }

        return redirect()->route('proveedores.index')
            ->with('error', 'Error al eliminar proveedor');
    }

    /**
     * Cambiar estado del proveedor
     */
    public function changeStatus(Request $request, $id)
    {
        $response = $this->apiService->post("proveedores/{$id}/change-status", [
            'estado' => $request->estado
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return redirect()->route('proveedores.index')
                ->with('success', $data['message'] ?? 'Estado cambiado exitosamente');
        }

        return redirect()->route('proveedores.index')
            ->with('error', 'Error al cambiar el estado del proveedor');
    }
}