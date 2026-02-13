@extends('layouts.app')

@section('title', 'Gestión de Clientes - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Clientes</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Gestión de Clientes</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('clientes.estadisticas') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar me-2"></i> Estadísticas
                </a>
                <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nuevo Cliente
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filtros de búsqueda -->
            <form method="GET" action="{{ route('clientes.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Buscar por nombre, NIT, email o teléfono" 
                               value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="tipo">
                            <option value="todos" {{ ($tipo ?? 'todos') == 'todos' ? 'selected' : '' }}>Todos los tipos</option>
                            <option value="natural" {{ ($tipo ?? '') == 'natural' ? 'selected' : '' }}>Natural</option>
                            <option value="juridico" {{ ($tipo ?? '') == 'juridico' ? 'selected' : '' }}>Jurídico</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="estado">
                            <option value="todos" {{ ($estado ?? 'todos') == 'todos' ? 'selected' : '' }}>Todos los estados</option>
                            <option value="activo" {{ ($estado ?? '') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ ($estado ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>

            @php
                // Extraer datos de manera segura - Mismo patrón que servicios
                $clientesData = [];
                $clientesLinks = [];
                $clientesMeta = [];
                
                if (isset($clientes['data'])) {
                    $clientesData = $clientes['data'];
                } elseif (isset($clientes) && is_array($clientes)) {
                    $clientesData = $clientes;
                }
                
                if (isset($clientes['links']) && is_array($clientes['links'])) {
                    $clientesLinks = $clientes['links'];
                }
                
                if (isset($clientes['meta']) && is_array($clientes['meta'])) {
                    $clientesMeta = $clientes['meta'];
                }
            @endphp

            @if(empty($clientesData))
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay clientes registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer cliente</p>
                    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Cliente
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>NIT</th>
                                <th>Contacto</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th style="width: 200px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientesData as $cliente)
                            <tr>
                                <td>{{ $cliente['id'] ?? 'N/A' }}</td>
                                <td>
                                    <strong>{{ $cliente['nombre'] ?? 'N/A' }}</strong>
                                    @if(isset($cliente['notas']) && !empty($cliente['notas']))
                                        <small class="text-muted d-block">{{ Str::limit($cliente['notas'], 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $cliente['nit'] ?? 'N/A' }}</td>
                                <td>
                                    @if(isset($cliente['email']) && !empty($cliente['email']))
                                        <div><i class="fas fa-envelope me-1"></i> {{ $cliente['email'] }}</div>
                                    @endif
                                    @if(isset($cliente['telefono']) && !empty($cliente['telefono']))
                                        <div><i class="fas fa-phone me-1"></i> {{ $cliente['telefono'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($cliente['tipo'] ?? 'natural') == 'natural' ? 'info' : 'warning' }}">
                                        {{ ($cliente['tipo'] ?? 'natural') == 'natural' ? 'Natural' : 'Jurídico' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ ($cliente['estado'] ?? 'activo') == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $cliente['estado'] ?? 'activo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('clientes.show', $cliente['id'] ?? '#') }}" 
                                           class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clientes.edit', $cliente['id'] ?? '#') }}" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('clientes.destroy', $cliente['id'] ?? '#') }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este cliente?')">
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
                @if(!empty($clientesLinks) && count($clientesLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($clientesMeta))
                            Mostrando 
                            {{ $clientesMeta['from'] ?? 1 }} - 
                            {{ $clientesMeta['to'] ?? count($clientesData) }} de 
                            {{ $clientesMeta['total'] ?? count($clientesData) }} clientes
                        @else
                            Mostrando {{ count($clientesData) }} clientes
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($clientesLinks as $link)
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
    // Agregar tooltips a los botones
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
            const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
            
            if (!confirm(`¿Cambiar estado del cliente de "${estadoActual}" a "${nuevoEstado}"?`)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.badge {
    font-size: 0.85em;
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
    
    .d-flex.gap-2 {
        flex-wrap: wrap;
        gap: 0.5rem !important;
    }
    
    .btn {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
}
</style>
@endpush