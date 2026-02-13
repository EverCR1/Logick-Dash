<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class CreditoController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Mostrar lista de créditos
     */
    public function index()
    {
        $response = $this->apiService->get('creditos');
        
        if ($response->successful()) {
            $data = $response->json();
            $creditos = $data['creditos'] ?? [];
            $estadisticas = $data['estadisticas'] ?? [];
        } else {
            $creditos = [
                'data' => [],
                'links' => [],
                'meta' => []
            ];
            $estadisticas = [];
        }

        return view('creditos.index', compact('creditos', 'estadisticas'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('creditos.create');
    }

    /**
     * Almacenar nuevo crédito
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_cliente' => 'required|string|max:200',
            'capital' => 'required|numeric|min:0.01',
            'producto_o_servicio_dado' => 'nullable|string',
            'fecha_credito' => 'required|date',
            'capital_restante' => 'required|numeric|min:0',
        ]);

        try {
            $creditoData = $request->all();
            
            $response = $this->apiService->post('creditos', $creditoData);

            if ($response->successful()) {
                return redirect()->route('creditos.index')
                    ->with('success', 'Crédito creado exitosamente');
            }

            $errors = $response->json()['errors'] ?? [];
            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            return back()->withErrors(['Error al crear crédito: ' . ($response->json()['message'] ?? 'Error desconocido')])->withInput();

        } catch (\Exception $e) {
            \Log::error('Error creando crédito:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Error interno: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mostrar crédito específico
     */
    public function show($id)
    {
        $response = $this->apiService->get("creditos/{$id}");
        
        if ($response->successful()) {
            $data = $response->json();
            $credito = $data['credito'] ?? null;
            
            if (!$credito) {
                return redirect()->route('creditos.index')
                    ->with('error', 'Crédito no encontrado');
            }
            
            return view('creditos.show', compact('credito'));
        }

        return redirect()->route('creditos.index')
            ->with('error', 'Crédito no encontrado');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $response = $this->apiService->get("creditos/{$id}");
        
        if (!$response->successful()) {
            return redirect()->route('creditos.index')
                ->with('error', 'Crédito no encontrado');
        }

        $data = $response->json();
        $credito = $data['credito'] ?? null;
        
        if (!$credito) {
            return redirect()->route('creditos.index')
                ->with('error', 'Crédito no encontrado en la respuesta');
        }

        return view('creditos.edit', compact('credito'));
    }

    /**
     * Actualizar crédito
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_cliente' => 'required|string|max:200',
            'capital' => 'required|numeric|min:0.01',
            'producto_o_servicio_dado' => 'nullable|string',
            'fecha_credito' => 'required|date',
        ]);

        try {
            $creditoData = $request->except(['_token', '_method']);
            
            \Log::info('Actualizando crédito:', [
                'id' => $id,
                'data' => $creditoData
            ]);

            $response = $this->apiService->put("creditos/{$id}", $creditoData);

            if ($response->successful()) {
                return redirect()->route('creditos.index')
                    ->with('success', 'Crédito actualizado exitosamente');
            }

            $errors = $response->json()['errors'] ?? [];
            $message = $response->json()['message'] ?? 'Error desconocido al actualizar';
            
            \Log::error('Error API al actualizar crédito:', [
                'status' => $response->status(),
                'errors' => $errors,
                'message' => $message
            ]);

            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            return back()->withErrors(['error' => $message])->withInput();

        } catch (\Exception $e) {
            \Log::error('Excepción al actualizar crédito:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Error interno: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Eliminar crédito
     */
    public function destroy($id)
    {
        $response = $this->apiService->delete("creditos/{$id}");

        if ($response->successful()) {
            return redirect()->route('creditos.index')
                ->with('success', 'Crédito eliminado exitosamente');
        }

        $message = $response->json()['message'] ?? 'Error desconocido';
        
        return redirect()->route('creditos.index')
            ->with('error', 'Error al eliminar crédito: ' . $message);
    }

    /**
     * Cambiar estado del crédito
     */
    public function changeStatus($id)
    {
        try {
            $response = $this->apiService->post("creditos/{$id}/change-status");
            
            if ($response->successful()) {
                return back()->with('success', 'Estado del crédito cambiado exitosamente');
            }
            
            return back()->with('error', 'Error al cambiar estado: ' . ($response->json()['message'] ?? 'Error desconocido'));
            
        } catch (\Exception $e) {
            \Log::error('Error cambiando estado del crédito:', [
                'message' => $e->getMessage(),
                'credito_id' => $id
            ]);
            
            return back()->with('error', 'Error interno al cambiar estado');
        }
    }

    /**
     * Registrar pago/abono
     */
    public function registrarPago(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'tipo' => 'required|in:abono,pago_total',
            'observaciones' => 'nullable|string',
        ]);

        try {
            $pagoData = $request->all();
            
            $response = $this->apiService->post("creditos/{$id}/registrar-pago", $pagoData);

            if ($response->successful()) {
                return redirect()->route('creditos.show', $id)
                    ->with('success', 'Pago registrado exitosamente');
            }

            $errors = $response->json()['errors'] ?? [];
            $message = $response->json()['message'] ?? 'Error desconocido al registrar pago';
            
            \Log::error('Error API al registrar pago:', [
                'status' => $response->status(),
                'errors' => $errors,
                'message' => $message
            ]);

            if (!empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            return back()->withErrors(['error' => $message])->withInput();

        } catch (\Exception $e) {
            \Log::error('Excepción al registrar pago:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Error interno: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Buscar créditos
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        $estado = $request->get('estado', 'todos');

        $params = [];
        if ($query) {
            $params['query'] = $query;
        }
        if ($estado !== 'todos') {
            $params['estado'] = $estado;
        }

        $response = $this->apiService->get('creditos/search', $params);

        if ($response->successful()) {
            $creditos = $response->json()['creditos'] ?? [];
        } else {
            $creditos = [
                'data' => [],
                'links' => [],
                'meta' => []
            ];
        }

        return view('creditos.buscar', compact('creditos', 'query', 'estado'));
    }

    /**
     * Créditos por estado
     */
    public function porEstado($estado)
    {
        if (!in_array($estado, ['activo', 'abonado', 'pagado'])) {
            return redirect()->route('creditos.index')
                ->with('error', 'Estado no válido');
        }

        $response = $this->apiService->get("creditos/estado/{$estado}");

        if ($response->successful()) {
            $creditos = $response->json()['creditos'] ?? [];
        } else {
            $creditos = [
                'data' => [],
                'links' => [],
                'meta' => []
            ];
        }

        return view('creditos.por-estado', compact('creditos', 'estado'));
    }
}