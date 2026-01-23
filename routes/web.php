<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas
// Ruta principal
Route::get('/', function () {
    return redirect()->route('login');
});

// ================= RUTAS PÚBLICAS =================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Rutas protegidas
// ================= RUTAS PROTEGIDAS =================
Route::middleware(['web.auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Usuarios (solo administrador)
    Route::middleware(['role:administrador'])->prefix('usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/crear', [UserController::class, 'create'])->name('usuarios.create');
        Route::post('/', [UserController::class, 'store'])->name('usuarios.store');
        Route::get('/{id}', [UserController::class, 'show'])->name('usuarios.show');
        Route::get('/{id}/editar', [UserController::class, 'edit'])->name('usuarios.edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('usuarios.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });
    
    // Proveedores (admin y vendedor)
    Route::middleware(['role:administrador,vendedor'])->prefix('proveedores')->group(function () {
        Route::get('/', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('/crear', [ProveedorController::class, 'create'])->name('proveedores.create');
        Route::post('/', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::get('/{id}', [ProveedorController::class, 'show'])->name('proveedores.show');
        Route::get('/{id}/editar', [ProveedorController::class, 'edit'])->name('proveedores.edit');
        Route::put('/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::delete('/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    });
    
    // Categorías (todos pueden ver, solo admin/vendedor modificar)
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    
    Route::middleware(['role:administrador,vendedor'])->prefix('categorias')->group(function () {
        Route::get('/crear', [CategoriaController::class, 'create'])->name('categorias.create');
        Route::post('/', [CategoriaController::class, 'store'])->name('categorias.store');
        Route::get('/{id}/editar', [CategoriaController::class, 'edit'])->name('categorias.edit');
        Route::put('/{id}', [CategoriaController::class, 'update'])->name('categorias.update');
        Route::delete('/{id}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
    });

    // Productos (admin y vendedor)
    Route::middleware(['web.auth:administrador,vendedor'])->prefix('productos')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/crear', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/{id}', [ProductoController::class, 'show'])->name('productos.show');
        Route::get('/{id}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/{id}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        Route::get('/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
        Route::get('/stock-bajo', [ProductoController::class, 'stockBajo'])->name('productos.stock-bajo');
        
        // Nuevas rutas para imágenes
        Route::post('/{id}/subir-imagenes', [ProductoController::class, 'subirImagenes'])->name('productos.subir-imagenes');
        Route::post('/{id}/imagenes/{imagenId}/establecer-principal', [ProductoController::class, 'establecerImagenPrincipal'])->name('productos.imagenes.establecer-principal');
        Route::delete('/{id}/imagenes/{imagenId}', [ProductoController::class, 'eliminarImagen'])->name('productos.imagenes.eliminar');
    });
});
