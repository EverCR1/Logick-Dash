<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

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
            
            // Agregar conteo de productos a cada categoría (opcional - requeriría endpoint adicional)
            // Esto se puede hacer si tienes un endpoint que devuelva el conteo
        } else {
            $categorias = [];
            
            Log::error('Error al obtener categorías:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        }

        return view('categorias.index', [
            'categorias' => $categorias,
            'userRole' => auth()->user()->rol ?? 'vendedor'
        ]);
    }

    /**
     * Mostrar los detalles de una categoría específica
     */
    public function show($id)
    {
        // Obtener la categoría con sus relaciones
        $response = $this->apiService->get("categorias/{$id}");
        
        if (!$response->successful()) {
            Log::error('Error al obtener categoría:', [
                'id' => $id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return redirect()->route('categorias.index')
                ->with('error', 'Categoría no encontrada');
        }

        $data = $response->json();
        $categoria = $data['categoria'] ?? null;
        
        if (!$categoria) {
            return redirect()->route('categorias.index')
                ->with('error', 'Categoría no encontrada en la respuesta');
        }

        // Opcional: Obtener productos de esta categoría
        // Esto requeriría un endpoint adicional en la API
        // Por ahora, podemos intentar obtenerlos si el endpoint existe
        $productosResponse = $this->apiService->get('productos/search', [
            'categoria_id' => $id,
            'limit' => 10
        ]);
        
        if ($productosResponse->successful()) {
            $productosData = $productosResponse->json();
            $categoria['productos'] = $productosData['productos']['data'] ?? [];
            $categoria['productos_count'] = $productosData['productos']['total'] ?? count($categoria['productos']);
        } else {
            $categoria['productos'] = [];
            $categoria['productos_count'] = 0;
        }

        // Obtener el rol del usuario para permisos
        $userRole = auth()->user()->rol ?? 'vendedor';

        return view('categorias.show', compact('categoria', 'userRole'));
    }

    public function create()
    {
        // Obtener categorías para el select
        $response = $this->apiService->get('categorias-flat');
        
        if ($response->successful()) {
            $categoriasPadre = $response->json()['categorias'] ?? [];
        } else {
            $categoriasPadre = [];
            
            Log::error('Error al obtener categorías para select:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
        }

        return view('categorias.create', [
            'categoriasPadre' => $categoriasPadre
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'parent_id' => 'nullable',
        ]);

        $response = $this->apiService->post('categorias', $request->all());

        if ($response->successful()) {
            return redirect()->route('categorias.index')
                ->with('success', 'Categoría creada exitosamente');
        }

        $errors = $response->json()['errors'] ?? ['Error al crear categoría'];
        $message = $response->json()['message'] ?? 'Error desconocido';
        
        Log::error('Error al crear categoría:', [
            'status' => $response->status(),
            'errors' => $errors,
            'message' => $message
        ]);

        return back()->withErrors($errors)->withInput();
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
        
        // Obtener categorías para el select
        $responsePadre = $this->apiService->get('categorias-flat');
        $categoriasPadre = $responsePadre->successful() ? $responsePadre->json()['categorias'] ?? [] : [];

        return view('categorias.edit', [
            'categoria' => $categoria,
            'categoriasPadre' => $categoriasPadre
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'parent_id' => 'nullable',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $response = $this->apiService->put("categorias/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('categorias.index')
                ->with('success', 'Categoría actualizada exitosamente');
        }

        $errors = $response->json()['errors'] ?? ['Error al actualizar categoría'];
        $message = $response->json()['message'] ?? 'Error desconocido';
        
        Log::error('Error al actualizar categoría:', [
            'status' => $response->status(),
            'errors' => $errors,
            'message' => $message
        ]);

        return back()->withErrors($errors)->withInput();
    }

    public function destroy($id)
    {
        $response = $this->apiService->delete("categorias/{$id}");

        if ($response->successful()) {
            return redirect()->route('categorias.index')
                ->with('success', 'Categoría eliminada exitosamente');
        }

        $message = $response->json()['message'] ?? 'Error al eliminar categoría';
        
        Log::error('Error al eliminar categoría:', [
            'status' => $response->status(),
            'message' => $message
        ]);

        return redirect()->route('categorias.index')
            ->with('error', $message);
    }
}