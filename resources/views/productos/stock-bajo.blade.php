@extends('layouts.app')

@section('title', 'Productos con Stock Bajo - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">Stock Bajo</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Productos con Stock Bajo</h5>
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
        <div class="card-body">
            @if(count($productos) > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Hay <strong>{{ count($productos) }}</strong> producto(s) con stock por debajo del mínimo
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>SKU</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Diferencia</th>
                                <th>Proveedor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productos as $producto)
                            @php
                                $diferencia = $producto['stock_minimo'] - $producto['stock'];
                                $urgencia = $diferencia > 5 ? 'danger' : ($diferencia > 2 ? 'warning' : 'info');
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $producto['nombre'] }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $producto['marca'] ?? '' }}</small>
                                </td>
                                <td>{{ $producto['sku'] }}</td>
                                <td>
                                    <span class="badge bg-danger">{{ $producto['stock'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $producto['stock_minimo'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $urgencia }}">
                                        {{ $diferencia }} unidades
                                    </span>
                                </td>
                                <td>
                                    {{ $producto['proveedor']['nombre'] ?? 'N/A' }}
                                    @if($producto['proveedor']['telefono'] ?? false)
                                        <br>
                                        <small class="text-muted">{{ $producto['proveedor']['telefono'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('productos.show', $producto['id']) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('productos.edit', $producto['id']) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                onclick="abrirModalReabastecer({{ $producto['id'] }}, '{{ $producto['nombre'] }}', {{ $producto['stock'] }}, {{ $producto['stock_minimo'] }})">
                                            <i class="fas fa-boxes"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-success">¡Todo en orden!</h5>
                    <p class="text-muted">No hay productos con stock por debajo del mínimo</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para reabastecer -->
<div class="modal fade" id="modalReabastecer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reabastecer Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Producto: <strong id="productoNombre"></strong></p>
                <p>Stock actual: <span id="stockActual" class="badge bg-danger"></span></p>
                <p>Stock mínimo: <span id="stockMinimo" class="badge bg-secondary"></span></p>
                
                <div class="mb-3">
                    <label for="cantidadReabastecer" class="form-label">Cantidad a agregar:</label>
                    <input type="number" class="form-control" id="cantidadReabastecer" min="1" value="10">
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    El nuevo stock será: <strong id="nuevoStock">0</strong> unidades
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="reabastecerProducto()">
                    <i class="fas fa-boxes me-2"></i> Reabastecer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let productoIdActual = null;
    let stockActualVal = 0;
    
    function abrirModalReabastecer(id, nombre, stockActual, stockMinimo) {
        productoIdActual = id;
        stockActualVal = stockActual;
        
        document.getElementById('productoNombre').textContent = nombre;
        document.getElementById('stockActual').textContent = stockActual;
        document.getElementById('stockMinimo').textContent = stockMinimo;
        
        actualizarNuevoStock();
        
        const modal = new bootstrap.Modal(document.getElementById('modalReabastecer'));
        modal.show();
    }
    
    function actualizarNuevoStock() {
        const cantidad = parseInt(document.getElementById('cantidadReabastecer').value) || 0;
        const nuevoStock = stockActualVal + cantidad;
        document.getElementById('nuevoStock').textContent = nuevoStock;
    }
    
    document.getElementById('cantidadReabastecer').addEventListener('input', actualizarNuevoStock);
    
    function reabastecerProducto() {
        const cantidad = parseInt(document.getElementById('cantidadReabastecer').value) || 0;
        
        if (cantidad <= 0) {
            alert('Ingresa una cantidad válida');
            return;
        }
        
        // Aquí implementarías la llamada a la API para actualizar el stock
        // Por ahora solo cerramos el modal
        alert(`Se agregarían ${cantidad} unidades al producto ID: ${productoIdActual}`);
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalReabastecer'));
        modal.hide();
        
        // Recargar la página para ver los cambios
        setTimeout(() => location.reload(), 500);
    }
</script>
@endsection