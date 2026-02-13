@extends('layouts.app')

@section('title', 'Estadísticas de Clientes - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active">Estadísticas</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Estadísticas de Clientes</h5>
        </div>
        <div class="card-body">
            <!-- Tarjetas de resumen -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Total Clientes</h6>
                                    <h3 class="mb-0">{{ $estadisticas['total_clientes'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Activos</h6>
                                    <h3 class="mb-0">{{ $estadisticas['activos'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                </div>
                            </div>
                            <small class="text-white-50">
                                {{ number_format($estadisticas['porcentaje_activos'] ?? 0, 1) }}% del total
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Con Ventas</h6>
                                    <h3 class="mb-0">{{ $estadisticas['con_ventas'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Sin Ventas</h6>
                                    <h3 class="mb-0">{{ $estadisticas['sin_ventas'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-times fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Distribución por tipo -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Distribución por Tipo</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <span class="badge bg-info">Naturales: {{ $estadisticas['naturales'] ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="badge bg-warning">Jurídicos: {{ $estadisticas['juridicos'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="progress" style="height: 30px;">
                                @php
                                    $total = $estadisticas['total_clientes'] ?? 1;
                                    $naturales = $estadisticas['naturales'] ?? 0;
                                    $juridicos = $estadisticas['juridicos'] ?? 0;
                                    $porcNaturales = ($naturales / $total) * 100;
                                    $porcJuridicos = ($juridicos / $total) * 100;
                                @endphp
                                <div class="progress-bar bg-info" role="progressbar" 
                                     style="width: {{ $porcNaturales }}%" 
                                     aria-valuenow="{{ $porcNaturales }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format($porcNaturales, 1) }}%
                                </div>
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: {{ $porcJuridicos }}%" 
                                     aria-valuenow="{{ $porcJuridicos }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format($porcJuridicos, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribución por estado -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Distribución por Estado</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <span class="badge bg-success">Activos: {{ $estadisticas['activos'] ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="badge bg-danger">Inactivos: {{ $estadisticas['inactivos'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="progress" style="height: 30px;">
                                @php
                                    $activos = $estadisticas['activos'] ?? 0;
                                    $inactivos = $estadisticas['inactivos'] ?? 0;
                                    $porcActivos = ($activos / $total) * 100;
                                    $porcInactivos = ($inactivos / $total) * 100;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $porcActivos }}%" 
                                     aria-valuenow="{{ $porcActivos }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format($porcActivos, 1) }}%
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" 
                                     style="width: {{ $porcInactivos }}%" 
                                     aria-valuenow="{{ $porcInactivos }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ number_format($porcInactivos, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clientes frecuentes -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">Top 10 Clientes Frecuentes</h6>
                </div>
                <div class="card-body">
                    @if(count($clientesFrecuentes) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Total Ventas</th>
                                    <th>Total Gastado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientesFrecuentes as $index => $cliente)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $cliente['nombre'] }}</strong>
                                        <div class="small text-muted">
                                            {{ $cliente['nit'] ?? 'Sin NIT' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $cliente['ventas_count'] }} ventas
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            Q{{ number_format($cliente['total_gastado'], 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('clientes.show', $cliente['id']) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5>No hay datos de clientes frecuentes</h5>
                        <p class="text-muted">No se han registrado ventas aún</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('clientes.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i> Volver a la lista de clientes
                </a>
            </div>
        </div>
    </div>
</div>
@endsection