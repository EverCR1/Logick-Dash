@extends('layouts.app')

@section('title', 'Créditos - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Créditos</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Estadísticas -->
    @if(!empty($estadisticas))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Total Créditos</h6>
                            <h4 class="mb-0">{{ $estadisticas['total_creditos'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-file-invoice-dollar fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Activos</h6>
                            <h4 class="mb-0">{{ $estadisticas['activos'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock fa-2x text-success"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        Q{{ number_format($estadisticas['capital_pendiente_activos'] ?? 0, 2) }} pendiente
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Abonados</h6>
                            <h4 class="mb-0">{{ $estadisticas['abonados'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                        </div>
                    </div>
                    <small class="text-muted">
                        Q{{ number_format($estadisticas['capital_pendiente_abonados'] ?? 0, 2) }} pendiente
                    </small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Recuperado</h6>
                            <h4 class="mb-0">Q{{ number_format($estadisticas['total_recuperado'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Gestión de Créditos</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('creditos.buscar') }}" class="btn btn-info">
                    <i class="fas fa-search me-2"></i> Buscar
                </a>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-2"></i> Por Estado
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('creditos.por-estado', 'activo') }}">Activos</a></li>
                        <li><a class="dropdown-item" href="{{ route('creditos.por-estado', 'abonado') }}">Abonados</a></li>
                        <li><a class="dropdown-item" href="{{ route('creditos.por-estado', 'pagado') }}">Pagados</a></li>
                    </ul>
                </div>
                <a href="{{ route('creditos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nuevo Crédito
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @php
                // Extraer datos de manera segura
                $creditosData = [];
                $creditosLinks = [];
                $creditosMeta = [];
                
                if (isset($creditos['data'])) {
                    $creditosData = $creditos['data'];
                } elseif (isset($creditos) && is_array($creditos)) {
                    $creditosData = $creditos;
                }
                
                if (isset($creditos['links']) && is_array($creditos['links'])) {
                    $creditosLinks = $creditos['links'];
                }
                
                if (isset($creditos['meta']) && is_array($creditos['meta'])) {
                    $creditosMeta = $creditos['meta'];
                }
            @endphp

            @if(empty($creditosData))
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay créditos registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer crédito</p>
                    <a href="{{ route('creditos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Crédito
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Producto/Servicio</th>
                                <th>Capital</th>
                                <th>Restante</th>
                                <th>Progreso</th>
                                <th>Fecha Crédito</th>
                                <th>Último Pago</th>
                                <th>Estado</th>
                                <th style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($creditosData as $credito)
                            @php
                                $porcentajePagado = $credito['capital'] > 0 ? 
                                    (($credito['capital'] - $credito['capital_restante']) / $credito['capital']) * 100 : 0;
                                
                                $estadoColors = [
                                    'activo' => 'danger',
                                    'abonado' => 'warning',
                                    'pagado' => 'success'
                                ];
                                
                                $estadoLabels = [
                                    'activo' => 'Activo',
                                    'abonado' => 'Abonado',
                                    'pagado' => 'Pagado'
                                ];
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $credito['nombre_cliente'] ?? '' }}</strong>
                                </td>
                                <td>
                                    <small>{{ Str::limit($credito['producto_o_servicio_dado'] ?? 'No especificado', 50) }}</small>
                                </td>
                                <td>
                                    <strong>Q{{ number_format($credito['capital'] ?? 0, 2) }}</strong>
                                </td>
                                <td>
                                    @if($credito['capital_restante'] > 0)
                                        <strong class="text-danger">Q{{ number_format($credito['capital_restante'], 2) }}</strong>
                                    @else
                                        <span class="text-success">Q0.00</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $estadoColors[$credito['estado']] ?? 'info' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $porcentajePagado }}%"
                                                 aria-valuenow="{{ $porcentajePagado }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="ms-2">{{ number_format($porcentajePagado, 0) }}%</small>
                                    </div>
                                </td>
                                <td>
                                    <small>{{ isset($credito['fecha_credito']) ? \Carbon\Carbon::parse($credito['fecha_credito'])->format('d/m/Y') : 'N/A' }}</small>
                                </td>
                                <td>
                                    @if($credito['fecha_ultimo_pago'])
                                        <small>{{ \Carbon\Carbon::parse($credito['fecha_ultimo_pago'])->format('d/m/Y') }}</small>
                                        <br>
                                        <small class="text-muted">Q{{ number_format($credito['ultima_cantidad_pagada'] ?? 0, 2) }}</small>
                                    @else
                                        <span class="badge bg-light text-dark">Sin pagos</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('creditos.change-status', $credito['id'] ?? '#') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ $estadoColors[$credito['estado']] ?? 'secondary' }}">
                                            {{ $estadoLabels[$credito['estado']] ?? $credito['estado'] }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('creditos.show', $credito['id'] ?? '#') }}" class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('creditos.edit', $credito['id'] ?? '#') }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('creditos.destroy', $credito['id'] ?? '#') }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este crédito?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if(!empty($creditosLinks) && count($creditosLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($creditosMeta))
                            Mostrando 
                            {{ $creditosMeta['from'] ?? 1 }} - 
                            {{ $creditosMeta['to'] ?? count($creditosData) }} de 
                            {{ $creditosMeta['total'] ?? count($creditosData) }} créditos
                        @else
                            Mostrando {{ count($creditosData) }} créditos
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($creditosLinks as $link)
                                @if(is_array($link))
                                    <li class="page-item {{ $link['active'] ?? false ? 'active' : '' }} {{ empty($link['url']) ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $link['url'] ?? '#' }}">
                                            {!! $link['label'] ?? '' !!}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </nav>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agregar tooltips a los botones de acciones
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Confirmación antes de cambiar estado
    const statusForms = document.querySelectorAll('form[action*="cambiar-estado"]');
    statusForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const estadoActual = button.textContent.trim();
            
            // Determinar próximo estado
            const estados = ['Activo', 'Abonado', 'Pagado'];
            const currentIndex = estados.indexOf(estadoActual);
            const nextIndex = (currentIndex + 1) % estados.length;
            const nuevoEstado = estados[nextIndex];
            
            if (!confirm(`¿Cambiar estado del crédito de "${estadoActual}" a "${nuevoEstado}"?`)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.progress {
    min-width: 100px;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Estilos para botones de estado */
.btn-status {
    min-width: 80px;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .btn-group .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
}

/* Estilos para cards de estadísticas */
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endpush