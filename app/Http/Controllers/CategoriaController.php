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

    // ── INDEX ─────────────────────────────────────────────────────────

    public function index()
    {
        $response = $this->apiService->get('categorias-tree');

        if ($response->successful()) {
            $categorias = $response->json()['categorias'] ?? [];
        } else {
            $categorias = [];
            Log::error('CategoriaController@index', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
        }

        return view('categorias.index', [
            'categorias' => $categorias,
            'userRole'   => auth()->user()->rol ?? 'vendedor'
        ]);
    }

    // ── CREATE ────────────────────────────────────────────────────────

    public function create()
    {
        $response        = $this->apiService->get('categorias-flat');
        $categoriasPadre = $response->successful()
            ? ($response->json()['categorias'] ?? [])
            : [];

        return view('categorias.create', compact('categoriasPadre'));
    }

    // ── STORE ─────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'parent_id'   => 'nullable',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $data = $request->only(['nombre', 'descripcion', 'parent_id']);

            // 1. Crear categoría
            $response = $this->apiService->post('categorias', $data);

            if (!$response->successful()) {
                $errors  = $response->json()['errors']  ?? [];
                $message = $response->json()['message'] ?? 'Error al crear categoría';
                return !empty($errors)
                    ? back()->withErrors($errors)->withInput()
                    : back()->withErrors(['error' => $message])->withInput();
            }

            $categoriaId = $response->json()['categoria']['id'] ?? null;

            // 2. Subir imagen si viene — mismo patrón que ServicioController@store
            if ($request->hasFile('imagen') && $categoriaId) {
                $this->subirImagenCategoria($request, $categoriaId, 'imagen');
            }

            return redirect()
                ->route('categorias.index')
                ->with('success', 'Categoría creada exitosamente' .
                    ($request->hasFile('imagen') ? ' (imagen subida)' : ''));

        } catch (\Exception $e) {
            Log::error('CategoriaController@store: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error interno: ' . $e->getMessage()])->withInput();
        }
    }

    // ── SHOW ──────────────────────────────────────────────────────────

    public function show($id)
    {
        $response = $this->apiService->get("categorias/{$id}");

        if (!$response->successful()) {
            return redirect()->route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        $categoria = $response->json()['categoria'] ?? null;

        if (!$categoria) {
            return redirect()->route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        // Productos de la categoría
        $productosResp = $this->apiService->get('productos/search', [
            'categoria_id' => $id,
            'per_page'     => 10
        ]);

        if ($productosResp->successful()) {
            $pd = $productosResp->json();
            $categoria['productos']       = $pd['productos']['data'] ?? [];
            $categoria['productos_count'] = $pd['productos']['total'] ?? count($categoria['productos']);
        } else {
            $categoria['productos']       = [];
            $categoria['productos_count'] = 0;
        }

        $userRole = auth()->user()->rol ?? 'vendedor';

        return view('categorias.show', compact('categoria', 'userRole'));
    }

    // ── EDIT ──────────────────────────────────────────────────────────

    public function edit($id)
    {
        $response = $this->apiService->get("categorias/{$id}");

        if (!$response->successful()) {
            return redirect()->route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        $categoria = $response->json()['categoria'] ?? null;

        if (!$categoria) {
            return redirect()->route('categorias.index')->with('error', 'Categoría no encontrada.');
        }

        $responsePadre   = $this->apiService->get('categorias-flat');
        $categoriasPadre = $responsePadre->successful()
            ? ($responsePadre->json()['categorias'] ?? [])
            : [];

        // Excluir la categoría actual del selector de padre
        unset($categoriasPadre[$id]);

        return view('categorias.edit', compact('categoria', 'categoriasPadre'));
    }

    // ── UPDATE ────────────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'       => 'required|string|max:100',
            'descripcion'  => 'nullable|string',
            'parent_id'    => 'nullable',
            'estado'       => 'required|in:activo,inactivo',
            'nueva_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $data = $request->only(['nombre', 'descripcion', 'parent_id', 'estado']);

            $response = $this->apiService->put("categorias/{$id}", $data);

            if (!$response->successful()) {
                $errors  = $response->json()['errors']  ?? [];
                $message = $response->json()['message'] ?? 'Error al actualizar';
                return !empty($errors)
                    ? back()->withErrors($errors)->withInput()
                    : back()->withErrors(['error' => $message])->withInput();
            }

            // Subir nueva imagen — mismo patrón que ServicioController@update
            if ($request->hasFile('nueva_imagen')) {
                try {
                    $this->subirImagenCategoria($request, $id, 'nueva_imagen');
                } catch (\Exception $e) {
                    Log::warning('CategoriaController@update imagen: ' . $e->getMessage());
                    return redirect()
                        ->route('categorias.index')
                        ->with('warning', 'Categoría actualizada pero hubo un error al subir la imagen: ' . $e->getMessage());
                }
            }

            // Eliminar imagen si se marcó el checkbox
            if ($request->boolean('eliminar_imagen')) {
                $delResponse = $this->apiService->delete("categorias/{$id}/imagen");
                if (!$delResponse->successful()) {
                    Log::warning('No se pudo eliminar imagen de categoría ' . $id);
                }
            }

            return redirect()
                ->route('categorias.index')
                ->with('success', 'Categoría actualizada exitosamente' .
                    ($request->hasFile('nueva_imagen') ? ' (imagen actualizada)' : ''));

        } catch (\Exception $e) {
            Log::error('CategoriaController@update: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error interno: ' . $e->getMessage()])->withInput();
        }
    }

    // ── DESTROY ───────────────────────────────────────────────────────

    public function destroy($id)
    {
        $response = $this->apiService->delete("categorias/{$id}");

        if ($response->successful()) {
            return redirect()
                ->route('categorias.index')
                ->with('success', 'Categoría eliminada exitosamente.');
        }

        // La API devuelve mensajes específicos (subcategorías, productos, etc.)
        $message = $response->json()['message'] ?? 'Error al eliminar categoría';

        return redirect()
            ->route('categorias.index')
            ->with('error', $message);
    }

    // ── CHANGE STATUS ─────────────────────────────────────────────────

    /**
     * Mismo patrón que ServicioController@changeStatus
     */
    public function changeStatus($id)
    {
        try {
            $response = $this->apiService->post("categorias/{$id}/change-status", [
                'estado' => request('estado', 'activo')
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Estado de la categoría cambiado exitosamente.');
            }

            return back()->with('error', 'Error al cambiar estado: ' .
                ($response->json()['message'] ?? 'Error desconocido'));

        } catch (\Exception $e) {
            Log::error('CategoriaController@changeStatus: ' . $e->getMessage(), [
                'categoria_id' => $id
            ]);
            return back()->with('error', 'Error interno al cambiar estado.');
        }
    }

    // ── SUBIR IMAGEN (AJAX) ───────────────────────────────────────────

    /**
     * POST /categorias/{id}/subir-imagen
     * Mismo patrón que ServicioController@subirImagen
     */
    public function subirImagen(Request $request, $id)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $response = $this->apiService->postWithFiles(
                "categorias/{$id}/upload-image",
                [],
                ['imagen' => $request->file('imagen')]
            );

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen subida exitosamente',
                    'data'    => $response->json()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al subir imagen: ' .
                    ($response->json()['message'] ?? 'Error ' . $response->status()),
                'errors'  => $response->json()['errors'] ?? null
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('CategoriaController@subirImagen: ' . $e->getMessage(), [
                'categoria_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    // ── ELIMINAR IMAGEN (AJAX) ────────────────────────────────────────

    /**
     * DELETE /categorias/{id}/imagen
     * Mismo patrón que ServicioController@eliminarImagen
     */
    public function eliminarImagen($id)
    {
        try {
            $response = $this->apiService->delete("categorias/{$id}/imagen");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Imagen eliminada exitosamente'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar imagen: ' .
                    ($response->json()['message'] ?? 'Error ' . $response->status())
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('CategoriaController@eliminarImagen: ' . $e->getMessage(), [
                'categoria_id' => $id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    // ── HELPER PRIVADO ────────────────────────────────────────────────

    /**
     * Sube imagen a la API usando postWithFiles.
     * Mismo patrón que subirImagenServicio / subirNuevaImagenServicio en ServicioController.
     *
     * @param  Request $request
     * @param  int     $categoriaId
     * @param  string  $campo       nombre del campo en el request ('imagen' o 'nueva_imagen')
     */
    private function subirImagenCategoria(Request $request, int $categoriaId, string $campo): void
    {
        $endpoint = "categorias/{$categoriaId}/upload-image";
        $file = $request->file($campo);
        
        Log::info('Inicio subida imagen categoría', [
            'categoria_id' => $categoriaId,
            'campo' => $campo,
            'file_present' => $file !== null,
            'file_name' => $file ? $file->getClientOriginalName() : null,
            'file_size' => $file ? $file->getSize() : null,
            'file_mime' => $file ? $file->getMimeType() : null
        ]);

        if (!$file) {
            throw new \Exception('No se recibió el archivo en el campo ' . $campo);
        }

        try {
            $response = $this->apiService->postWithFiles(
                $endpoint,
                [],
                ['imagen' => $file]
            );

            Log::info('Respuesta API subida imagen', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);

            if (!$response->successful()) {
                throw new \Exception('Error API: ' . ($response->json()['message'] ?? $response->status()));
            }

            Log::info('Imagen de categoría subida exitosamente', [
                'categoria_id' => $categoriaId
            ]);
            
        } catch (\Exception $e) {
            Log::error('Excepción subiendo imagen', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}