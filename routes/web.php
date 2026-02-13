<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;

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

    // Servicios (admin y vendedor)
    Route::middleware(['web.auth:administrador,vendedor'])->prefix('servicios')->group(function () {
        Route::get('/', [ServicioController::class, 'index'])->name('servicios.index');
        Route::get('/crear', [ServicioController::class, 'create'])->name('servicios.create');
        Route::post('/', [ServicioController::class, 'store'])->name('servicios.store');
        Route::get('/{id}', [ServicioController::class, 'show'])->name('servicios.show');
        Route::get('/{id}/editar', [ServicioController::class, 'edit'])->name('servicios.edit');
        Route::put('/{id}', [ServicioController::class, 'update'])->name('servicios.update');
        Route::delete('/{id}', [ServicioController::class, 'destroy'])->name('servicios.destroy');
        Route::post('/{id}/cambiar-estado', [ServicioController::class, 'changeStatus'])->name('servicios.change-status');
        Route::get('/buscar', [ServicioController::class, 'buscar'])->name('servicios.buscar');
        
        // Rutas para imágenes
        Route::post('/{id}/subir-imagen', [ServicioController::class, 'subirImagen'])->name('servicios.subir-imagen');
        Route::delete('/{id}/imagenes/{imagenId}', [ServicioController::class, 'eliminarImagen'])->name('servicios.imagenes.eliminar');
    });

    // Créditos (admin y vendedor)
    Route::middleware(['web.auth:administrador,vendedor'])->prefix('creditos')->group(function () {
        Route::get('/', [CreditoController::class, 'index'])->name('creditos.index');
        Route::get('/crear', [CreditoController::class, 'create'])->name('creditos.create');
        Route::post('/', [CreditoController::class, 'store'])->name('creditos.store');
        Route::get('/{id}', [CreditoController::class, 'show'])->name('creditos.show');
        Route::get('/{id}/editar', [CreditoController::class, 'edit'])->name('creditos.edit');
        Route::put('/{id}', [CreditoController::class, 'update'])->name('creditos.update');
        Route::delete('/{id}', [CreditoController::class, 'destroy'])->name('creditos.destroy');
        Route::post('/{id}/cambiar-estado', [CreditoController::class, 'changeStatus'])->name('creditos.change-status');
        Route::post('/{id}/registrar-pago', [CreditoController::class, 'registrarPago'])->name('creditos.registrar-pago');
        Route::get('/buscar', [CreditoController::class, 'buscar'])->name('creditos.buscar');
        Route::get('/estado/{estado}', [CreditoController::class, 'porEstado'])->name('creditos.por-estado');
    });

    // Rutas de clientes (administrador y vendedor)
    Route::middleware(['web.auth', 'role:administrador,vendedor'])->prefix('clientes')->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/crear', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/{id}', [ClienteController::class, 'show'])->name('clientes.show');
        Route::get('/{id}/editar', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/{id}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::delete('/{id}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::post('/{id}/cambiar-estado', [ClienteController::class, 'changeStatus'])->name('clientes.changeStatus');
        Route::post('/{id}/cambiar-estado', [ClienteController::class, 'changeStatus'])->name('clientes.changeStatus');
        Route::get('/estadisticas', [ClienteController::class, 'estadisticas'])->name('clientes.estadisticas');
    });

    // Ventas (admin y vendedor)
    Route::middleware(['web.auth:administrador,vendedor'])->prefix('ventas')->group(function () {
        Route::get('/', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/crear', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('/', [VentaController::class, 'store'])->name('ventas.store');
        Route::get('/{id}', [VentaController::class, 'show'])->name('ventas.show');
        Route::post('/{id}/cancelar', [VentaController::class, 'cancelar'])->name('ventas.cancelar');
        Route::get('/buscar', [VentaController::class, 'buscar'])->name('ventas.buscar');
        Route::get('/reporte', [VentaController::class, 'reporte'])->name('ventas.reporte');
        
        // Rutas AJAX para búsqueda
        Route::get('/buscar/productos-ajax', [VentaController::class, 'buscarProductos'])->name('ventas.buscar.productos.ajax');
        Route::get('/buscar/servicios-ajax', [VentaController::class, 'buscarServicios'])->name('ventas.buscar.servicios.ajax');
        Route::get('/buscar/clientes-ajax', [VentaController::class, 'buscarClientes'])->name('ventas.buscar.clientes.ajax');
    });
});
