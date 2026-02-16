@extends('layouts.app')

@section('title', 'Detalles del Proveedor - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('proveedores.index') }}">Proveedores</a></li>
    <li class="breadcrumb-item active">Detalles del Proveedor</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Tarjeta de perfil del proveedor -->
            <div class="card card-primary card-outline">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-id-card me-2"></i>Perfil del Proveedor
                    </h5>
                    <div class="card-tools">
                        <a href="{{ route('proveedores.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Columna izquierda: Foto/avatar y estado -->
                        <div class="col-md-4">
                            <div class="text-center mb-4">
                                <div class="avatar-circle bg-primary mb-3">
                                    <span class="initials">{{ substr($proveedor['nombre'], 0, 2) }}</span>
                                </div>
                                <h3 class="mb-1">{{ $proveedor['nombre'] }}</h3>
                                <p class="text-muted">Proveedor #{{ $proveedor['id'] }}</p>
                                
                                <!-- Badges de estado -->
                                <div class="d-flex justify-content-center gap-2 mb-3">
                                    @php
                                        $estadoClass = ($proveedor['estado'] ?? 'activo') == 'activo' ? 'bg-success' : 'bg-danger';
                                        $estadoIcon = ($proveedor['estado'] ?? 'activo') == 'activo' ? 'check-circle' : 'times-circle';
                                    @endphp
                                    <span class="badge {{ $estadoClass }} p-2">
                                        <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                        {{ ucfirst($proveedor['estado'] ?? 'activo') }}
                                    </span>
                                </div>

                                <!-- Botón de cambio de estado en vista show -->
                                <div class="mt-3">
                                    @if(($proveedor['estado'] ?? 'activo') == 'activo')
                                    <form action="{{ route('proveedores.changeStatus', $proveedor['id']) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de desactivar este proveedor?')">
                                        @csrf
                                        <input type="hidden" name="estado" value="inactivo">
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-ban me-2"></i>Desactivar Proveedor
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('proveedores.changeStatus', $proveedor['id']) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de activar este proveedor?')">
                                        @csrf
                                        <input type="hidden" name="estado" value="activo">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Activar Proveedor
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Información rápida -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="card-title mb-0">
                                        <i class="fas fa-chart-simple me-2"></i>Resumen
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-calendar-plus text-primary me-2"></i>Fecha registro</span>
                                            <span class="badge bg-primary rounded-pill">
                                                {{ isset($proveedor['created_at']) ? date('d/m/Y', strtotime($proveedor['created_at'])) : 'N/A' }}
                                            </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <span><i class="fas fa-rotate text-info me-2"></i>Última actualización</span>
                                            <span class="badge bg-info rounded-pill">
                                                {{ isset($proveedor['updated_at']) ? date('d/m/Y', strtotime($proveedor['updated_at'])) : 'N/A' }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Información detallada -->
                        <div class="col-md-8">
                            <!-- Tarjetas de información -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-primary">
                                        <div class="card-header bg-primary bg-opacity-10">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-building me-2"></i>Información Básica
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <th width="120"><i class="fas fa-tag text-primary me-2"></i>Nombre:</th>
                                                    <td><strong>{{ $proveedor['nombre'] }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-align-left text-info me-2"></i>Descripción:</th>
                                                    <td>
                                                        @if($proveedor['descripcion'])
                                                            <div class="p-2 bg-light rounded">
                                                                {{ $proveedor['descripcion'] }}
                                                            </div>
                                                        @else
                                                            <span class="text-muted fst-italic">No hay descripción disponible</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-success">
                                        <div class="card-header bg-success bg-opacity-10">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-address-card me-2"></i>Información de Contacto
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <th width="120"><i class="fas fa-envelope text-success me-2"></i>Email:</th>
                                                    <td>
                                                        @if($proveedor['email'])
                                                            <a href="mailto:{{ $proveedor['email'] }}" class="text-decoration-none">
                                                                <i class="fas fa-envelope me-1"></i>{{ $proveedor['email'] }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted fst-italic">No registrado</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-phone text-success me-2"></i>Teléfono:</th>
                                                    <td>
                                                        @if($proveedor['telefono'])
                                                            <a href="tel:{{ $proveedor['telefono'] }}" class="text-decoration-none">
                                                                <i class="fas fa-phone me-1"></i>{{ $proveedor['telefono'] }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted fst-italic">No registrado</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i class="fas fa-map-marker-alt text-success me-2"></i>Dirección:</th>
                                                    <td>
                                                        @if($proveedor['direccion'])
                                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $proveedor['direccion'] }}
                                                        @else
                                                            <span class="text-muted fst-italic">No registrada</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tarjeta de estadísticas adicionales -->
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-header bg-info bg-opacity-10">
                                            <h6 class="card-title mb-0">
                                                <i class="fas fa-chart-pie me-2"></i>Estadísticas
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-4">
                                                    <div class="p-3">
                                                        <div class="text-info mb-2">
                                                            <i class="fas fa-box fa-2x"></i>
                                                        </div>
                                                        <h4 class="mb-1">0</h4>
                                                        <span class="text-muted">Productos asociados</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="p-3">
                                                        <div class="text-success mb-2">
                                                            <i class="fas fa-shopping-cart fa-2x"></i>
                                                        </div>
                                                        <h4 class="mb-1">0</h4>
                                                        <span class="text-muted">Compras realizadas</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="p-3">
                                                        <div class="text-warning mb-2">
                                                            <i class="fas fa-clock fa-2x"></i>
                                                        </div>
                                                        <h4 class="mb-1">0</h4>
                                                        <span class="text-muted">Días como proveedor</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al listado
                        </a>
                        <div class="d-flex gap-2">
                            @if(($proveedor['estado'] ?? 'activo') == 'activo')
                            <form action="{{ route('proveedores.changeStatus', $proveedor['id']) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Estás seguro de desactivar este proveedor?')">
                                @csrf
                                <input type="hidden" name="estado" value="inactivo">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-ban me-2"></i>Desactivar
                                </button>
                            </form>
                            @else
                            <form action="{{ route('proveedores.changeStatus', $proveedor['id']) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Estás seguro de activar este proveedor?')">
                                @csrf
                                <input type="hidden" name="estado" value="activo">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i>Activar
                                </button>
                            </form>
                            @endif
                            
                            <a href="{{ route('proveedores.edit', $proveedor['id']) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a>
                            <form action="{{ route('proveedores.destroy', $proveedor['id']) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Estás seguro de eliminar este proveedor? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
}

.initials {
    font-size: 48px;
    color: white;
    font-weight: bold;
    text-transform: uppercase;
}

.border-primary.bg-opacity-10 {
    border-bottom: 3px solid #0d6efd;
}

.border-success.bg-opacity-10 {
    border-bottom: 3px solid #198754;
}

.border-info.bg-opacity-10 {
    border-bottom: 3px solid #0dcaf0;
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table-borderless tr {
    border-bottom: 1px solid #f0f0f0;
}

.table-borderless tr:last-child {
    border-bottom: none;
}
</style>
@endpush