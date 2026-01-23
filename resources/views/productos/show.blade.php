@extends('layouts.app')

@section('title', 'Detalle Producto - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">Detalle Producto</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Información principal -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $producto['nombre'] }}</h5>
                    <div class="btn-group">
                        <a href="{{ route('productos.edit', $producto['id']) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i> Editar
                        </a>
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <!-- Imagen del producto -->
                            <div class="text-center mb-3">
                                @if(isset($producto['imagen_principal']) && $producto['imagen_principal'])
                                    <img src="{{ $producto['imagen_principal']['url'] }}" 
                                         alt="{{ $producto['nombre'] }}" 
                                         class="img-fluid rounded" 
                                         style="max-height: 250px;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="height: 250px;">
                                        <i class="fas fa-box-open fa-4x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="d-grid gap-2">
                                <span class="badge {{ $producto['estado'] == 'activo' ? 'bg-success' : 'bg-danger' }} fs-6 py-2">
                                    {{ $producto['estado'] }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <!-- Información detallada -->
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 30%;">SKU:</th>
                                    <td><strong>{{ $producto['sku'] }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Código de Barras:</th>
                                    <td>{{ $producto['codigo_barras'] ?? 'No asignado' }}</td>
                                </tr>
                                <tr>
                                    <th>Marca:</th>
                                    <td>{{ $producto['marca'] ?? 'Sin especificar' }}</td>
                                </tr>
                                <tr>
                                    <th>Color:</th>
                                    <td>{{ $producto['color'] ?? 'Sin especificar' }}</td>
                                </tr>
                                <tr>
                                    <th>Proveedor:</th>
                                    <td>{{ $producto['proveedor']['nombre'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Ubicación:</th>
                                    <td>{{ $producto['ubicacion'] ?? 'No asignada' }}</td>
                                </tr>
                            </table>
                            
                            <!-- Categorías -->
                            @if(isset($producto['categorias']) && count($producto['categorias']) > 0)
                                <div class="mb-3">
                                    <strong>Categorías:</strong>
                                    <div class="mt-1">
                                        @foreach($producto['categorias'] as $categoria)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $categoria['nombre'] }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Descripción -->
                    @if($producto['descripcion'])
                        <div class="mt-4">
                            <h6>Descripción</h6>
                            <p class="text-muted">{{ $producto['descripcion'] }}</p>
                        </div>
                    @endif
                    
                    <!-- Especificaciones -->
                    @if($producto['especificaciones'])
                        <div class="mt-4">
                            <h6>Especificaciones Técnicas</h6>
                            <p class="text-muted">{!! nl2br(e($producto['especificaciones'])) !!}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Información financiera y stock -->
        <div class="col-lg-4">
            <!-- Precios -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Información de Precios</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Precio de Compra:</span>
                        <strong class="text-primary">Q{{ number_format($producto['precio_compra'], 2) }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Precio de Venta:</span>
                        <strong class="text-primary">Q{{ number_format($producto['precio_venta'], 2) }}</strong>
                    </div>
                    
                    @if($producto['precio_oferta'])
                        <div class="d-flex justify-content-between mb-2">
                            <span>Precio de Oferta:</span>
                            <strong class="text-danger">Q{{ number_format($producto['precio_oferta'], 2) }}</strong>
                        </div>
                    @endif
                    
                    @php
                        $precioFinal = $producto['precio_oferta'] ?? $producto['precio_venta'];
                        $margen = (($precioFinal - $producto['precio_compra']) / $producto['precio_compra']) * 100;
                        $margenClass = $margen >= 30 ? 'success' : ($margen >= 15 ? 'warning' : 'danger');
                    @endphp
                    
                    <div class="d-flex justify-content-between mt-3 pt-2 border-top">
                        <span>Margen de Ganancia:</span>
                        <span class="badge bg-{{ $margenClass }}">{{ number_format($margen, 2) }}%</span>
                    </div>
                </div>
            </div>
            
            <!-- Stock -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Control de Stock</h6>
                </div>
                <div class="card-body">
                    @php
                        $stockClass = $producto['stock'] <= $producto['stock_minimo'] ? 'danger' : ($producto['stock'] <= $producto['stock_minimo'] * 2 ? 'warning' : 'success');
                    @endphp
                    
                    <div class="text-center mb-3">
                        <div class="display-4 text-{{ $stockClass }}">{{ $producto['stock'] }}</div>
                        <small class="text-muted">Unidades disponibles</small>
                    </div>
                    
                    <div class="progress mb-3" style="height: 20px;">
                        @php
                            $porcentajeStock = min(100, ($producto['stock'] / ($producto['stock_minimo'] * 4)) * 100);
                        @endphp
                        <div class="progress-bar bg-{{ $stockClass }}" role="progressbar" 
                             style="width: {{ $porcentajeStock }}%;" 
                             aria-valuenow="{{ $porcentajeStock }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($porcentajeStock, 0) }}%
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">Stock Mínimo:</small>
                        <small class="text-muted">{{ $producto['stock_minimo'] }}</small>
                    </div>
                    
                    @if($producto['stock'] <= $producto['stock_minimo'])
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>¡Alerta!</strong> Stock por debajo del mínimo
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Información Adicional</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Creado:</small>
                        <div>{{ date('d/m/Y H:i', strtotime($producto['created_at'])) }}</div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Última actualización:</small>
                        <div>{{ date('d/m/Y H:i', strtotime($producto['updated_at'])) }}</div>
                    </div>
                    
                    @if($producto['notas_internas'])
                        <div class="mt-3">
                            <small class="text-muted">Notas Internas:</small>
                            <p class="small text-muted mb-0">{{ $producto['notas_internas'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Imágenes adicionales (si existen) -->
    @if(isset($producto['imagenes']) && count($producto['imagenes']) > 1)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Galería de Imágenes</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($producto['imagenes'] as $imagen)
                        @if(!$imagen['es_principal'])
                            <div class="col-md-3 col-6 mb-3">
                                <div class="card">
                                    <img src="{{ $imagen['url'] }}" 
                                         class="card-img-top" 
                                         alt="Imagen {{ $loop->iteration }}"
                                         style="height: 150px; object-fit: cover;">
                                    <div class="card-body text-center p-2">
                                        <small class="text-muted">{{ $imagen['nombre_original'] }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection