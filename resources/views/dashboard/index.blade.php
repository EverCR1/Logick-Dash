{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        @php
                            \Carbon\Carbon::setLocale('es');
                        @endphp

                        <div>
                            <h4 class="card-title mb-1">¡Bienvenido, {{ $user['nombres'] ?? 'Usuario' }}!</h4>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-calendar-alt me-2"></i>
                                {{ \Carbon\Carbon::now()->timezone('America/Guatemala')->isoFormat('dddd, D MMMM YYYY') }}
                                <i class="fas fa-clock ms-3 me-2"></i>
                                {{ \Carbon\Carbon::now()->timezone('America/Guatemala')->format('h:i A') }}
                            </p>
                        </div>
                        <!-- En la card de bienvenida, junto al avatar -->
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-sm btn-light me-2" id="refreshDashboard" title="Actualizar datos">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <div class="user-avatar-large">
                                {{ substr($user['nombres'] ?? 'U', 0, 1) }}{{ substr($user['apellidos'] ?? 'S', 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas de stock bajo -->
    @if($stats['total_alertas'] > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                    <div>
                        <strong>¡Atención!</strong> Hay {{ $stats['total_alertas'] }} producto(s) con stock bajo.
                        @if($stats['total_alertas'] > 0 && count($stats['alertas_stock_bajo']) > 0)
                            <ul class="mb-0 mt-2">
                                @foreach(array_slice($stats['alertas_stock_bajo'], 0, 5) as $producto)
                                    <li>
                                        <strong>{{ $producto['nombre'] ?? '' }}</strong> - 
                                        Stock: {{ $producto['stock'] ?? 0 }} | 
                                        Mínimo: {{ $producto['stock_minimo'] ?? 0 }}
                                    </li>
                                @endforeach
                                @if($stats['total_alertas'] > 5)
                                    <li class="text-muted">... y {{ $stats['total_alertas'] - 5 }} más</li>
                                @endif
                            </ul>
                        @endif
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Cards principales de Ventas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Ventas Hoy</h6>
                            <h3 class="mb-0 fw-bold text-primary">Q {{ number_format($stats['ventas_hoy'], 2) }}</h3>
                            <small class="text-muted">{{ $stats['total_ventas_hoy'] }} transacciones</small>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Ventas Semana</h6>
                            <h3 class="mb-0 fw-bold text-success">Q {{ number_format($stats['ventas_semana'], 2) }}</h3>
                            <small class="text-muted">{{ $stats['total_ventas_semana'] }} transacciones</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Ventas Mes</h6>
                            <h3 class="mb-0 fw-bold text-info">Q {{ number_format($stats['ventas_mes'], 2) }}</h3>
                            <small class="text-muted">{{ $stats['total_ventas_mes'] }} transacciones</small>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-alt fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Promedio Venta</h6>
                            <h3 class="mb-0 fw-bold text-warning">Q {{ number_format($stats['promedio_venta'], 2) }}</h3>
                            <small class="text-muted">Máx: Q {{ number_format($stats['venta_maxima'], 2) }}</small>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-bar fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila: Clientes, Productos, Servicios, Créditos -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Clientes</h6>
                            <h3 class="mb-0 fw-bold text-danger">{{ number_format($stats['total_clientes']) }}</h3>
                            <small class="text-muted">
                                {{ $stats['clientes_activos'] }} activos | 
                                {{ $stats['clientes_nuevos_mes'] }} nuevos
                            </small>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-users fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-secondary border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Productos</h6>
                            <h3 class="mb-0 fw-bold text-secondary">{{ number_format($stats['total_productos']) }}</h3>
                            <small class="text-muted">
                                {{ $stats['productos_stock_bajo'] }} bajo stock | 
                                {{ $stats['productos_agotados'] }} agotados
                            </small>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-box fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-purple border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Servicios</h6>
                            <h3 class="mb-0 fw-bold text-purple">{{ number_format($stats['total_servicios']) }}</h3>
                            <small class="text-muted">{{ $stats['servicios_activos'] }} activos</small>
                        </div>
                        <div class="bg-purple bg-opacity-10 p-3 rounded">
                            <i class="fas fa-cogs fa-2x text-purple"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-teal border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Créditos</h6>
                            <h3 class="mb-0 fw-bold text-teal">{{ number_format($stats['creditos_activos']) }}</h3>
                            <small class="text-muted">
                                Q {{ number_format($stats['capital_pendiente'], 2) }} pendiente
                            </small>
                        </div>
                        <div class="bg-teal bg-opacity-10 p-3 rounded">
                            <i class="fas fa-credit-card fa-2x text-teal"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tercera fila: Proveedores, Categorías, Usuarios, Valor Inventario -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-cyan border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Proveedores</h6>
                            <h3 class="mb-0 fw-bold text-cyan">{{ number_format($stats['total_proveedores']) }}</h3>
                            <small class="text-muted">{{ $stats['proveedores_activos'] }} activos</small>
                        </div>
                        <div class="bg-cyan bg-opacity-10 p-3 rounded">
                            <i class="fas fa-truck fa-2x text-cyan"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-indigo border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Categorías</h6>
                            <h3 class="mb-0 fw-bold text-indigo">{{ number_format($stats['total_categorias']) }}</h3>
                            <small class="text-muted">
                                Nivel 0: {{ $stats['categorias_por_nivel']['nivel_0'] ?? 0 }} | 
                                Nivel 1: {{ $stats['categorias_por_nivel']['nivel_1'] ?? 0 }}
                            </small>
                        </div>
                        <div class="bg-indigo bg-opacity-10 p-3 rounded">
                            <i class="fas fa-tags fa-2x text-indigo"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-pink border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Usuarios</h6>
                            <h3 class="mb-0 fw-bold text-pink">{{ number_format($stats['total_usuarios']) }}</h3>
                            <small class="text-muted">
                                Admin: {{ $stats['usuarios_por_rol']['administrador'] }} | 
                                Vendedor: {{ $stats['usuarios_por_rol']['vendedor'] }}
                            </small>
                        </div>
                        <div class="bg-pink bg-opacity-10 p-3 rounded">
                            <i class="fas fa-user-tie fa-2x text-pink"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted fw-semibold">Valor Inventario</h6>
                            <h3 class="mb-0 fw-bold text-success">Q {{ number_format($stats['valor_inventario'], 2) }}</h3>
                            <small class="text-muted">Total recuperado: Q {{ number_format($stats['total_recuperado'], 2) }}</small>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-dollar-sign fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y listados -->
    <div class="row">
        <!-- Top Productos -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-simple me-2"></i>Productos Más Vendidos
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($stats['top_productos']))
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['top_productos'] as $producto)
                                    <tr>
                                        <td>
                                            <strong>{{ $producto['producto']['nombre'] ?? 'N/A' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $producto['total_vendido'] ?? 0 }}</span>
                                        </td>
                                        <td class="text-end">Q {{ number_format($producto['total_ingreso'] ?? 0, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p>No hay datos de ventas este mes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Clientes -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>Top Clientes
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($stats['top_clientes']))
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th class="text-center">Compras</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stats['top_clientes'] as $cliente)
                                    <tr>
                                        <td>
                                            <strong>{{ $cliente['nombre'] ?? 'N/A' }}</strong>
                                            <small class="text-muted d-block">{{ $cliente['nit'] ?? '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $cliente['ventas_count'] ?? 0 }}</span>
                                        </td>
                                        <td class="text-end">Q {{ number_format($cliente['total_comprado'] ?? 0, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <p>No hay datos de clientes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Métodos de Pago -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-credit-card me-2"></i>Métodos de Pago
                    </h5>
                </div>
                <div class="card-body">
                    @if(!empty($stats['metodos_pago']))
                        <div class="list-group list-group-flush">
                            @foreach($stats['metodos_pago'] as $metodo)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <span class="badge bg-secondary me-2">
                                        @switch($metodo['metodo_pago'])
                                            @case('efectivo')
                                                <i class="fas fa-money-bill-wave"></i>
                                                @break
                                            @case('tarjeta')
                                                <i class="fas fa-credit-card"></i>
                                                @break
                                            @case('transferencia')
                                                <i class="fas fa-university"></i>
                                                @break
                                            @case('mixto')
                                                <i class="fas fa-random"></i>
                                                @break
                                        @endswitch
                                    </span>
                                    {{ ucfirst($metodo['metodo_pago']) }}
                                    <small class="text-muted d-block">{{ $metodo['cantidad'] }} transacciones</small>
                                </div>
                                <span class="fw-bold text-info">Q {{ number_format($metodo['total'], 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-credit-card fa-3x mb-3"></i>
                            <p>No hay datos de pagos</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Estado de Créditos -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Estado de Créditos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="creditosChart" style="height: 200px;"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><span class="badge bg-danger me-2">●</span> Activos:</span>
                                    <span class="fw-bold">{{ number_format($stats['creditos_activos']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><span class="badge bg-warning me-2">●</span> Abonados:</span>
                                    <span class="fw-bold">{{ number_format($stats['creditos_abonados']) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><span class="badge bg-success me-2">●</span> Pagados:</span>
                                    <span class="fw-bold">{{ number_format($stats['creditos_pagados']) }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Capital Pendiente:</span>
                                    <span class="fw-bold text-danger">Q {{ number_format($stats['capital_pendiente'], 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Total Recuperado:</span>
                                    <span class="fw-bold text-success">Q {{ number_format($stats['total_recuperado'], 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Accesos Rápidos
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @if($userRole == 'administrador')
                        <a href="{{ route('usuarios.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-users me-3 text-primary"></i>
                            <span>Gestionar Usuarios</span>
                            <span class="badge bg-primary ms-auto">{{ $stats['total_usuarios'] }}</span>
                        </a>
                        @endif
                        
                        <a href="{{ route('clientes.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-user me-3 text-success"></i>
                            <span>Gestionar Clientes</span>
                            <span class="badge bg-success ms-auto">{{ $stats['total_clientes'] }}</span>
                        </a>
                        
                        <a href="{{ route('proveedores.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-truck me-3 text-info"></i>
                            <span>Gestionar Proveedores</span>
                            <span class="badge bg-info ms-auto">{{ $stats['total_proveedores'] }}</span>
                        </a>
                        
                        <a href="{{ route('productos.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-box me-3 text-warning"></i>
                            <span>Gestionar Productos</span>
                            <span class="badge bg-warning ms-auto">{{ $stats['total_productos'] }}</span>
                        </a>
                        
                        <a href="{{ route('servicios.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-cogs me-3 text-secondary"></i>
                            <span>Gestionar Servicios</span>
                            <span class="badge bg-secondary ms-auto">{{ $stats['total_servicios'] }}</span>
                        </a>
                        
                        <a href="{{ route('creditos.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-credit-card me-3 text-danger"></i>
                            <span>Gestionar Créditos</span>
                            <span class="badge bg-danger ms-auto">{{ $stats['creditos_activos'] }}</span>
                        </a>
                        
                        <a href="{{ route('categorias.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-tags me-3 text-indigo"></i>
                            <span>Gestionar Categorías</span>
                            <span class="badge bg-indigo ms-auto">{{ $stats['total_categorias'] }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .user-avatar-large {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 600;
        border: 2px solid rgba(255,255,255,0.3);
    }
    
    .border-start.border-primary { border-left: 4px solid var(--bs-primary) !important; }
    .border-start.border-success { border-left: 4px solid var(--bs-success) !important; }
    .border-start.border-info { border-left: 4px solid var(--bs-info) !important; }
    .border-start.border-warning { border-left: 4px solid var(--bs-warning) !important; }
    .border-start.border-danger { border-left: 4px solid var(--bs-danger) !important; }
    .border-start.border-secondary { border-left: 4px solid var(--bs-secondary) !important; }
    .border-start.border-purple { border-left: 4px solid #6f42c1 !important; }
    .border-start.border-teal { border-left: 4px solid #20c997 !important; }
    .border-start.border-pink { border-left: 4px solid #d63384 !important; }
    .border-start.border-indigo { border-left: 4px solid #6610f2 !important; }
    .border-start.border-cyan { border-left: 4px solid #0dcaf0 !important; }
    
    .text-purple { color: #6f42c1 !important; }
    .text-teal { color: #20c997 !important; }
    .text-pink { color: #d63384 !important; }
    .text-indigo { color: #6610f2 !important; }
    .text-cyan { color: #0dcaf0 !important; }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .list-group-item-action {
        transition: all 0.2s;
    }
    
    .list-group-item-action:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de créditos
    const ctxCreditos = document.getElementById('creditosChart').getContext('2d');
    new Chart(ctxCreditos, {
        type: 'doughnut',
        data: {
            labels: ['Activos', 'Abonados', 'Pagados'],
            datasets: [{
                data: [
                    {{ $stats['creditos_activos'] ?? 0 }},
                    {{ $stats['creditos_abonados'] ?? 0 }},
                    {{ $stats['creditos_pagados'] ?? 0 }}
                ],
                backgroundColor: ['#dc3545', '#ffc107', '#198754'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            }
        }
    });
});
</script>
// Al final del script, después del gráfico de créditos
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ... gráfico de créditos existente ...

    // Refrescar datos
    const refreshBtn = document.getElementById('refreshDashboard');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            const btn = this;
            const originalHtml = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            fetch('{{ route("dashboard.refresh") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Recargar para mostrar nuevos datos
                } else {
                    alert('Error al actualizar datos');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar datos');
            })
            .finally(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        });
    }
});
</script>
@endpush