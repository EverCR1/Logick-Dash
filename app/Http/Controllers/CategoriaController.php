<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class CategoriaController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        $response = $this->apiService->get('categorias-tree');
        
        if ($response->successful()) {
            $categorias = $response->json()['categorias'] ?? [];
        } else {
            $categorias = [];
        }

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        // Obtener categorías para el select
        $response = $this->apiService->get('categorias-flat');
        $categoriasPadre = $response->successful() ? $response->json()['categorias'] ?? [] : [];

        return view('categorias.create', compact('categoriasPadre'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'parent_id' => 'nullable|exists:categorias,id',
        ]);

        $response = $this->apiService->post('categorias', $request->all());

        if ($response->successful()) {
            return redirect()->route('categorias.index')
                ->with('success', 'Categoría creada exitosamente');
        }

        return back()->withErrors($response->json()['errors'] ?? ['Error al crear categoría'])
                    ->withInput();
    }

    public function edit($id)
    {
        // Obtener categoría
        $response = $this->apiService->get("categorias/{$id}");
        
        if (!$response->successful()) {
            return redirect()->route('categorias.index')
                ->with('error', 'Categoría no encontrada');
        }

        $categoria = $response->json()['categoria'] ?? null;
        
        // Obtener categorías para el select (excluyendo la actual y sus descendientes)
        $response = $this->apiService->get('categorias-flat');
        $categoriasPadre = $response->successful() ? $response->json()['categorias'] ?? [] : [];

        return view('categorias.edit', compact('categoria', 'categoriasPadre'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'parent_id' => 'nullable|exists:categorias,id',
        ]);

        $response = $this->apiService->put("categorias/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('categorias.index')
                ->with('success', 'Categoría actualizada exitosamente');
        }

        return back()->withErrors($response->json()['errors'] ?? ['Error al actualizar categoría'])
                    ->withInput();
    }

    public function destroy($id)
    {
        $response = $this->apiService->delete("categorias/{$id}");

        if ($response->successful()) {
            return redirect()->route('categorias.index')
                ->with('success', 'Categoría eliminada exitosamente');
        }

        return redirect()->route('categorias.index')
            ->with('error', 'Error al eliminar categoría');
    }
}