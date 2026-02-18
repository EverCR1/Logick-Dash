@extends('layouts.app')

@section('title', 'Resumen de Reportes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Resumen</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4">
                <i class="fas fa-chart-pie me-2"></i>Resumen General de Reportes
            </h4>
        </div>
    </div>

    {{-- Ventas --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-line me-2"></i>Resumen de Ventas
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Hoy</small>
                                <h4 class="mb-0">Q{{ number_format($data['ventas']['hoy'] ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Esta semana</small>
                                <h4 class="mb-0">Q{{ number_format($data['ventas']['semana'] ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Este mes</small>
                                <h4 class="mb-0">Q{{ number_format($data['ventas']['mes'] ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Total histórico</small>
                                <h4 class="mb-0">Q{{ number_format($data['ventas']['total'] ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4 col-12 mb-3">
                            <div class="border rounded p-3 text-center">
                                <small class="text-muted">Promedio por venta</small>
                                <h4 class="mb-0">Q{{ number_format($data['ventas']['promedio_diario'] ?? 0, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Clientes y Productos --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-users me-2"></i>Clientes
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2>{{ $data['clientes']['total'] ?? 0 }}</h2>
                                <small>Total clientes</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2>{{ $data['clientes']['activos'] ?? 0 }}</h2>
                                <small>Activos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h2>{{ $data['clientes']['nuevos_mes'] ?? 0 }}</h2>
                                <small>Nuevos este mes</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h2>{{ $data['clientes']['con_ventas'] ?? 0 }}</h2>
                                <small>Han comprado</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-warning">
                    <i class="fas fa-boxes me-2"></i>Inventario
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2>{{ $data['productos']['total'] ?? 0 }}</h2>
                                <small>Total productos</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-danger">{{ $data['productos']['stock_bajo'] ?? 0 }}</h2>
                                <small>Stock bajo</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h2 class="text-secondary">{{ $data['productos']['agotados'] ?? 0 }}</h2>
                                <small>Agotados</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <h6>Valor inventario</h6>
                                <strong>Q{{ number_format($data['productos']['valor_inventario'] ?? 0, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Accesos rapidos a reportes --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-file-alt me-2"></i>Reportes Disponibles
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('reportes.ventas') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-shopping-cart me-2"></i>Ventas
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('reportes.productos-mas-vendidos') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-chart-bar me-2"></i>Más vendidos
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('reportes.inventario') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-boxes me-2"></i>Inventario
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('reportes.top-clientes') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-trophy me-2"></i>Top clientes
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('reportes.rendimiento-vendedores') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-user-tie me-2"></i>Vendedores
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection