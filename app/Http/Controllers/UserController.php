<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiService;

class UserController extends Controller
{
    protected $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        $response = $this->apiService->get('users');
        
        if ($response->successful()) {
            $users = $response->json()['users'] ?? [];
        } else {
            $users = [];
        }

        return view('usuarios.index', compact('users'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email',
            'username' => 'required|string|max:50',
            'password' => 'required|min:8|confirmed',
            'rol' => 'required|in:administrador,vendedor,analista',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $response = $this->apiService->post('users', $request->all());

        if ($response->successful()) {
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario creado exitosamente');
        }

        return back()->withErrors($response->json()['errors'] ?? ['Error al crear usuario'])
                    ->withInput();
    }

    public function show($id)
    {
        $response = $this->apiService->get("users/{$id}");
        
        if ($response->successful()) {
            $user = $response->json()['user'] ?? null;
            return view('usuarios.show', compact('user'));
        }

        return redirect()->route('usuarios.index')
            ->with('error', 'Usuario no encontrado');
    }

    public function edit($id)
    {
        $response = $this->apiService->get("users/{$id}");
        
        if ($response->successful()) {
            $user = $response->json()['user'] ?? null;
            return view('usuarios.edit', compact('user'));
        }

        return redirect()->route('usuarios.index')
            ->with('error', 'Usuario no encontrado');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email' . $id,
            'username' => 'required|string|max:50' . $id,
            'rol' => 'required|in:administrador,vendedor,analista',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);

        $response = $this->apiService->put("users/{$id}", $request->all());

        if ($response->successful()) {
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario actualizado exitosamente');
        }

        return back()->withErrors($response->json()['errors'] ?? ['Error al actualizar usuario'])
                    ->withInput();
    }

    public function destroy($id)
    {
        $response = $this->apiService->delete("users/{$id}");

        if ($response->successful()) {
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario eliminado exitosamente');
        }

        return redirect()->route('usuarios.index')
            ->with('error', 'Error al eliminar usuario');
    }
}