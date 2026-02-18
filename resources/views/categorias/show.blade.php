@extends('layouts.app')

@section('title', 'Detalles de Categoría')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
    <li class="breadcrumb-item active">Detalles de Categoría</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Tarjeta principal -->
            <div class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-tag text-primary me-2"></i>
                            Detalles de la Categoría
                        </h5>
                        @if(in_array($userRole, ['administrador', 'vendedor']))
                        <div class="d-flex gap-2">
                            <a href="{{ route('categorias.edit', $categoria['id']) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a>
                            <form action="{{ route('categorias.destroy', $categoria['id']) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('¿Estás seguro de eliminar esta categoría? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Columna izquierda - Información principal -->
                        <div class="col-md-6">
                            <div class="card mb-3 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Información General
                                    </h6>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150" class="text-muted">ID:</td>
                                            <td class="fw-bold">#{{ $categoria['id'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Nombre:</td>
                                            <td>
                                                <span class="fw-bold fs-5">{{ $categoria['nombre'] }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Descripción:</td>
                                            <td>
                                                @if(!empty($categoria['descripcion']))
                                                    <p class="mb-0">{{ $categoria['descripcion'] }}</p>
                                                @else
                                                    <span class="text-muted fst-italic">Sin descripción</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Estado:</td>
                                            <td>
                                                @php
                                                    $estadoClass = $categoria['estado'] == 'activo' ? 'success' : 'danger';
                                                    $estadoIcon = $categoria['estado'] == 'activo' ? 'check-circle' : 'times-circle';
                                                @endphp
                                                <span class="badge bg-{{ $estadoClass }}">
                                                    <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                                    {{ ucfirst($categoria['estado']) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Jerarquía -->
                            <div class="card mb-3 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-sitemap me-2"></i>Jerarquía
                                    </h6>
                                    
                                    <div class="hierarchy-tree">
                                        @if(isset($categoria['parent']))
                                            <div class="hierarchy-item parent">
                                                <i class="fas fa-level-up-alt text-warning me-2"></i>
                                                <span class="text-muted me-2">Categoría padre:</span>
                                                <a href="{{ route('categorias.show', $categoria['parent']['id']) }}" 
                                                   class="text-decoration-none">
                                                    {{ $categoria['parent']['nombre'] }}
                                                </a>
                                            </div>
                                        @endif
                                        
                                        <div class="hierarchy-item current">
                                            <i class="fas fa-tag text-primary me-2"></i>
                                            <span class="fw-bold">{{ $categoria['nombre'] }}</span>
                                            <span class="badge bg-light text-dark ms-2">Actual</span>
                                        </div>
                                        
                                        @if(!empty($categoria['children']) && count($categoria['children']) > 0)
                                            <div class="hierarchy-item children">
                                                <i class="fas fa-level-down-alt text-info me-2"></i>
                                                <span class="text-muted me-2">Subcategorías ({{ count($categoria['children']) }}):</span>
                                                <div class="mt-2 ms-4">
                                                    @foreach($categoria['children'] as $child)
                                                        <a href="{{ route('categorias.show', $child['id']) }}" 
                                                           class="badge bg-info text-white text-decoration-none me-1 mb-1 p-2">
                                                            {{ $child['nombre'] }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Columna derecha - Estadísticas -->
                        <div class="col-md-6">
                            <div class="card mb-3 border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-chart-pie me-2"></i>Estadísticas
                                    </h6>
                                    
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="stat-card p-3 rounded" style="background: linear-gradient(135deg, #e3f2fd, #bbdefb);">
                                                <div class="d-flex align-items-center">
                                                    <div class="stat-icon me-3">
                                                        <i class="fas fa-box text-primary fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-primary text-uppercase">Productos</small>
                                                        <h3 class="mb-0">{{ $categoria['productos_count'] ?? 0 }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="stat-card p-3 rounded" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9);">
                                                <div class="d-flex align-items-center">
                                                    <div class="stat-icon me-3">
                                                        <i class="fas fa-sitemap text-success fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-success text-uppercase">Subcategorías</small>
                                                        <h3 class="mb-0">{{ count($categoria['children'] ?? []) }}</h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="stat-card p-3 rounded" style="background: linear-gradient(135deg, #fff3e0, #ffe0b2);">
                                                <div class="d-flex align-items-center">
                                                    <div class="stat-icon me-3">
                                                        <i class="fas fa-calendar-plus text-warning fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-warning text-uppercase">Creada</small>
                                                        <h6 class="mb-0">{{ isset($categoria['created_at']) ? \Carbon\Carbon::parse($categoria['created_at'])->format('d/m/Y') : 'N/A' }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <div class="stat-card p-3 rounded" style="background: linear-gradient(135deg, #f3e5f5, #e1bee7);">
                                                <div class="d-flex align-items-center">
                                                    <div class="stat-icon me-3">
                                                        <i class="fas fa-rotate text-purple fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <small class="text-purple text-uppercase">Actualizada</small>
                                                        <h6 class="mb-0">{{ isset($categoria['updated_at']) ? \Carbon\Carbon::parse($categoria['updated_at'])->format('d/m/Y') : 'N/A' }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Productos asociados (si existen) -->
                            @if(!empty($categoria['productos']) && count($categoria['productos']) > 0)
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="text-primary mb-3">
                                        <i class="fas fa-boxes me-2"></i>Productos en esta categoría
                                    </h6>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>SKU</th>
                                                    <th>Precio</th>
                                                    <th>Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(array_slice($categoria['productos'], 0, 5) as $producto)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('productos.show', $producto['id']) }}" class="text-decoration-none">
                                                            {{ $producto['nombre'] }}
                                                        </a>
                                                    </td>
                                                    <td><small>{{ $producto['sku'] ?? 'N/A' }}</small></td>
                                                    <td>Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ ($producto['stock'] ?? 0) > 0 ? 'success' : 'danger' }}">
                                                            {{ $producto['stock'] ?? 0 }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    @if(count($categoria['productos']) > 5)
                                    <div class="text-center mt-2">
                                        <a href="{{ route('productos.index', ['categoria' => $categoria['id']]) }}" class="btn btn-sm btn-link">
                                            Ver todos los {{ count($categoria['productos']) }} productos
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al listado
                        </a>
                        
                        @if(in_array($userRole, ['administrador', 'vendedor']))
                        <div class="d-flex gap-2">
                            <a href="{{ route('categorias.create', ['parent_id' => $categoria['id']]) }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>Agregar Subcategoría
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.hierarchy-tree {
    position: relative;
    padding-left: 20px;
}

.hierarchy-item {
    padding: 10px;
    margin: 5px 0;
    border-radius: 8px;
    position: relative;
}

.hierarchy-item.parent {
    background: #fff3e0;
    border-left: 4px solid #ff9800;
}

.hierarchy-item.current {
    background: #e3f2fd;
    border-left: 4px solid #2196f3;
}

.hierarchy-item.children {
    background: #f3e5f5;
    border-left: 4px solid #9c27b0;
}

.text-purple {
    color: #9c27b0;
}

.bg-purple {
    background-color: #9c27b0;
}
</style>
@endpush