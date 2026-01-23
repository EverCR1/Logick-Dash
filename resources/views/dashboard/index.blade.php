@extends('layouts.app')

@section('title', 'Dashboard - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Bienvenida -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">¡Bienvenido, {{ $user['nombres'] ?? 'Usuario' }}!</h4>
                            <p class="card-text text-muted">
                                {{ date('l, d F Y') }} | {{ date('h:i A') }}
                            </p>
                        </div>
                        <div class="user-avatar-large">
                            {{ substr($user['nombres'] ?? 'U', 0, 1) }}{{ substr($user['apellidos'] ?? 'S', 0, 1) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted fw-semibold">Usuarios</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_usuarios'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted fw-semibold">Proveedores</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_proveedores'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-truck fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted fw-semibold">Categorías</h6>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_categorias'] }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tags fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted fw-semibold">Ventas Hoy</h6>
                            <h3 class="mb-0 fw-bold">Q {{ number_format($stats['ventas_hoy'], 2) }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alertas de stock bajo -->
    @php
        // Esto sería idealmente obtenido de una llamada a la API
        $alertasStockBajo = 0; // Temporal, se implementará después
    @endphp

    @if($alertasStockBajo > 0)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>¡Atención!</strong> Hay {{ $alertasStockBajo }} producto(s) con stock bajo.
        <a href="{{ route('productos.stock-bajo') }}" class="alert-link">Ver detalles</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Gráficos y más información -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ventas de los últimos 30 días</h5>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Gráfico de ventas se mostrará aquí</p>
                        <small>Implementaremos esto cuando tengamos datos de ventas</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Accesos Rápidos</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @if($userRole == 'administrador')
                        <a href="{{ route('usuarios.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-users me-3 text-primary"></i>
                            <span>Gestionar Usuarios</span>
                        </a>
                        @endif
                        
                        @if(in_array($userRole, ['administrador', 'vendedor']))
                        <a href="{{ route('proveedores.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-truck me-3 text-success"></i>
                            <span>Gestionar Proveedores</span>
                        </a>
                        <a href="{{ route('categorias.index') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-tags me-3 text-warning"></i>
                            <span>Gestionar Categorías</span>
                        </a>
                        @endif
                        
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-box me-3 text-info"></i>
                            <span>Gestionar Productos</span>
                            <span class="badge bg-secondary ms-auto">Próximamente</span>
                        </a>
                        
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="fas fa-shopping-cart me-3 text-danger"></i>
                            <span>Registrar Venta</span>
                            <span class="badge bg-secondary ms-auto">Próximamente</span>
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
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .border-start.border-primary {
        border-left-width: 4px !important;
    }
</style>
@endsection