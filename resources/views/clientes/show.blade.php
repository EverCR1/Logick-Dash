@extends('layouts.app')

@section('title', 'Detalles del Cliente')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
    <li class="breadcrumb-item active">{{ $cliente['nombre'] ?? 'Cliente' }}</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$cliente)
        <div class="alert alert-danger">
            Cliente no encontrado
        </div>
        <a href="{{ route('clientes.index') }}" class="btn btn-primary">Volver a la lista</a>
    @else
    <div class="row">
        <!-- Información del cliente -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Información del Cliente</h5>
                    <span class="badge {{ $cliente['estado'] == 'activo' ? 'bg-success' : 'bg-danger' }}">
                        {{ $cliente['estado'] }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4 text-center">
                        <div class="avatar-circle bg-primary text-white mb-3" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 2rem;">
                            {{ substr($cliente['nombre'], 0, 1) }}
                        </div>
                        <h4>{{ $cliente['nombre'] }}</h4>
                        <p class="text-muted">{{ $cliente['tipo'] == 'natural' ? 'Persona Natural' : 'Persona Jurídica' }}</p>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Información de contacto</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-id-card me-2 text-primary"></i>
                            <span>{{ $cliente['nit'] ?? 'Sin NIT' }}</span>
                        </div>
                        @if($cliente['email'])
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            <a href="mailto:{{ $cliente['email'] }}">{{ $cliente['email'] }}</a>
                        </div>
                        @endif
                        @if($cliente['telefono'])
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone me-2 text-primary"></i>
                            <a href="tel:{{ $cliente['telefono'] }}">{{ $cliente['telefono'] }}</a>
                        </div>
                        @endif
                    </div>

                    @if($cliente['direccion'])
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Dirección</h6>
                        <div class="d-flex align-items-start">
                            <i class="fas fa-map-marker-alt me-2 text-primary mt-1"></i>
                            <span>{{ $cliente['direccion'] }}</span>
                        </div>
                    </div>
                    @endif

                    @if($cliente['notas'])
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Notas</h6>
                        <p class="mb-0">{{ $cliente['notas'] }}</p>
                    </div>
                    @endif

                    <div class="mt-4">
                        <h6 class="text-muted mb-2">Información del sistema</h6>
                        <div class="small text-muted">
                            <div>ID: {{ $cliente['id'] }}</div>
                            <div>Creado: {{ \Carbon\Carbon::parse($cliente['created_at'])->format('d/m/Y H:i') }}</div>
                            <div>Actualizado: {{ \Carbon\Carbon::parse($cliente['updated_at'])->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('clientes.edit', $cliente['id']) }}" class="btn btn-warning flex-fill">
                            <i class="fas fa-edit me-2"></i> Editar
                        </a>
                        <form action="{{ route('clientes.changeStatus', $cliente['id']) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('POST')
                            <button type="submit" class="btn {{ $cliente['estado'] == 'activo' ? 'btn-secondary' : 'btn-success' }} w-100">
                                <i class="fas {{ $cliente['estado'] == 'activo' ? 'fa-times' : 'fa-check' }} me-2"></i>
                                {{ $cliente['estado'] == 'activo' ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas y ventas -->
        <div class="col-md-8">
            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Total Ventas</h6>
                                    <h3 class="mb-0">{{ $estadisticas['total_ventas'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
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
                                    <h6 class="text-white-50">Total Gastado</h6>
                                    <h3 class="mb-0">Q{{ number_format($estadisticas['total_gastado'] ?? 0, 2) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="text-white-50">Ventas Mes</h6>
                                    <h3 class="mb-0">{{ $estadisticas['ventas_mes_actual'] ?? 0 }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-alt fa-2x opacity-50"></i>
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
                                    <h6 class="text-white-50">Total Mes</h6>
                                    <h3 class="mb-0">Q{{ number_format($estadisticas['total_mes_actual'] ?? 0, 2) }}</h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chart-line fa-2x opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimas ventas -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Últimas Ventas</h5>
                    <a href="{{ route('ventas.index') }}?cliente_id={{ $cliente['id'] }}" 
                       class="btn btn-sm btn-primary">
                        Ver todas
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($cliente['ventas']) && count($cliente['ventas']) > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Referencia</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cliente['ventas'] as $venta)
                                <tr>
                                    <td>
                                        <a href="{{ route('ventas.show', $venta['id']) }}">
                                            {{ $venta['referencia'] }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($venta['created_at'])->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $venta['tipo'] == 'producto' ? 'info' : ($venta['tipo'] == 'servicio' ? 'success' : 'warning') }}">
                                            {{ $venta['tipo'] }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($venta['descripcion'], 30) }}</td>
                                    <td>Q{{ number_format($venta['total'], 2) }}</td>
                                    <td>
                                        <span class="badge {{ $venta['estado'] == 'completada' ? 'bg-success' : ($venta['estado'] == 'pendiente' ? 'bg-warning' : 'bg-danger') }}">
                                            {{ $venta['estado'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h5>No hay ventas registradas</h5>
                        <p class="text-muted">Este cliente aún no ha realizado compras</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection