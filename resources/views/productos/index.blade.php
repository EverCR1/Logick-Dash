@extends('layouts.app')

@section('title', 'Productos - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Productos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Gestión de Productos</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('productos.buscar') }}" class="btn btn-info">
                    <i class="fas fa-search me-2"></i> Buscar
                </a>
                <a href="{{ route('productos.stock-bajo') }}" class="btn btn-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i> Stock Bajo
                </a>
                <a href="{{ route('productos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nuevo Producto
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
                $productosData = [];
                $productosLinks = [];
                $productosMeta = [];
                
                if (isset($productos['data'])) {
                    $productosData = $productos['data'];
                } elseif (isset($productos) && is_array($productos)) {
                    $productosData = $productos;
                }
                
                if (isset($productos['links']) && is_array($productos['links'])) {
                    $productosLinks = $productos['links'];
                }
                
                if (isset($productos['meta']) && is_array($productos['meta'])) {
                    $productosMeta = $productos['meta'];
                }
            @endphp

            @if(empty($productosData))
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay productos registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer producto</p>
                    <a href="{{ route('productos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Producto
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Producto</th>
                                <th>Proveedor</th>
                                <th>Precios</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosData as $producto)
                            <tr>
                                <td>
                                    <strong>{{ $producto['sku'] ?? '' }}</strong>
                                    @if(!empty($producto['codigo_barras']))
                                    <br>
                                    <small class="text-muted">{{ $producto['codigo_barras'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $producto['nombre'] ?? '' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $producto['marca'] ?? 'Sin marca' }} | {{ $producto['color'] ?? 'Sin color' }}</small>
                                    <br>
                                    @if(!empty($producto['categorias']) && count($producto['categorias']) > 0)
                                        @foreach($producto['categorias'] as $categoria)
                                            <span class="badge bg-secondary me-1">{{ $categoria['nombre'] ?? '' }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    {{ $producto['proveedor']['nombre'] ?? 'N/A' }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small>Compra: <strong>Q{{ number_format($producto['precio_compra'] ?? 0, 2) }}</strong></small>
                                        <small>Venta: 
                                            @if(!empty($producto['precio_oferta']))
                                                <span class="text-decoration-line-through text-muted">Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</span>
                                                <strong class="text-danger">Q{{ number_format($producto['precio_oferta'], 2) }}</strong>
                                            @else
                                                <strong>Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</strong>
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $stock = $producto['stock'] ?? 0;
                                        $stockMinimo = $producto['stock_minimo'] ?? 1;
                                        $stockClass = $stock <= $stockMinimo ? 'danger' : ($stock <= $stockMinimo * 2 ? 'warning' : 'success');
                                    @endphp
                                    <span class="badge bg-{{ $stockClass }}">
                                        {{ $stock }}
                                    </span>
                                    <small class="d-block text-muted">Mín: {{ $stockMinimo }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ ($producto['estado'] ?? '') == 'activo' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $producto['estado'] ?? 'desconocido' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('productos.show', $producto['id'] ?? '#') }}" class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('productos.edit', $producto['id'] ?? '#') }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('productos.destroy', $producto['id'] ?? '#') }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
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

                <!-- Paginación - Solo si hay links -->
                @if(!empty($productosLinks) && count($productosLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($productosMeta))
                            Mostrando 
                            {{ $productosMeta['from'] ?? 1 }} - 
                            {{ $productosMeta['to'] ?? count($productosData) }} de 
                            {{ $productosMeta['total'] ?? count($productosData) }} productos
                        @else
                            Mostrando {{ count($productosData) }} productos
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($productosLinks as $link)
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