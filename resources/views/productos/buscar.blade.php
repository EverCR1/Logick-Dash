@extends('layouts.app')

@section('title', 'Buscar Productos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">Buscar Productos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Buscar Productos</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('productos.buscar') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" 
                                   name="q" 
                                   value="{{ $query ?? '' }}" 
                                   placeholder="Buscar por nombre, SKU, código de barras o ubicación...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="tipo">
                            <option value="todos" {{ ($tipo ?? 'todos') == 'todos' ? 'selected' : '' }}>Buscar en todo</option>
                            <option value="nombre" {{ ($tipo ?? '') == 'nombre' ? 'selected' : '' }}>Solo por nombre</option>
                            <option value="sku" {{ ($tipo ?? '') == 'sku' ? 'selected' : '' }}>Solo por SKU</option>
                            <option value="codigo_barras" {{ ($tipo ?? '') == 'codigo_barras' ? 'selected' : '' }}>Solo por código de barras</option>
                            <option value="ubicacion" {{ ($tipo ?? '') == 'ubicacion' ? 'selected' : '' }}>Solo por ubicación</option>
                        </select>
                    </div>
                </div>
            </form>

            @if(isset($query))
                @if(!empty($productos) && count($productos) > 0)
                    <div class="alert alert-info">
                        Se encontraron {{ count($productos) }} resultado(s) para "<strong>{{ $query }}</strong>"
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>SKU</th>
                                    <th>Producto</th>
                                    <th>Stock</th>
                                    <th>Precio Venta</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $producto)
                                <tr>
                                    <td><strong>{{ $producto['sku'] ?? '' }}</strong></td>
                                    <td>
                                        <strong>{{ $producto['nombre'] ?? '' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $producto['marca'] ?? '' }} {{ $producto['color'] ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ ($producto['stock'] ?? 0) <= ($producto['stock_minimo'] ?? 1) ? 'bg-danger' : 'bg-success' }}">
                                            {{ $producto['stock'] ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</strong>
                                        @if(!empty($producto['precio_oferta']))
                                            <br>
                                            <small class="text-danger">Oferta: Q{{ number_format($producto['precio_oferta'], 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('productos.show', $producto['id'] ?? '#') }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('productos.edit', $producto['id'] ?? '#') }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron resultados</h5>
                        <p class="text-muted">No hay productos que coincidan con "{{ $query }}"</p>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Buscar Productos</h5>
                    <p class="text-muted">Ingresa un término de búsqueda para comenzar</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection