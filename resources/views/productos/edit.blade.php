@extends('layouts.app')

@section('title', 'Editar Producto - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">Editar Producto</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Editar Producto: {{ $producto['nombre'] ?? '' }}</h5>
            <a href="{{ route('productos.show', $producto['id'] ?? '#') }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye me-1"></i> Ver Detalle
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('productos.update', $producto['id'] ?? '') }}" method="POST" id="productoForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-md-8">
                        <!-- Información básica -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Información Básica</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="sku" class="form-label">SKU *</label>
                                        <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                               id="sku" name="sku" value="{{ old('sku', $producto['sku'] ?? '') }}" required>
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre del Producto *</label>
                                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                               id="nombre" name="nombre" value="{{ old('nombre', $producto['nombre'] ?? '') }}" required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                                  id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $producto['descripcion'] ?? '') }}</textarea>
                                        @error('descripcion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="marca" class="form-label">Marca</label>
                                        <input type="text" class="form-control @error('marca') is-invalid @enderror" 
                                               id="marca" name="marca" value="{{ old('marca', $producto['marca'] ?? '') }}">
                                        @error('marca')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="color" class="form-label">Color</label>
                                        <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                               id="color" name="color" value="{{ old('color', $producto['color'] ?? '') }}">
                                        @error('color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Especificaciones -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Especificaciones</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="especificaciones" class="form-label">Especificaciones Técnicas</label>
                                    <textarea class="form-control @error('especificaciones') is-invalid @enderror" 
                                              id="especificaciones" name="especificaciones" rows="4">{{ old('especificaciones', $producto['especificaciones'] ?? '') }}</textarea>
                                    @error('especificaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna derecha -->
                    <div class="col-md-4">
                        <!-- Precios y Stock -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Precios y Stock</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="precio_compra" class="form-label">Precio de Compra *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" step="0.01" class="form-control @error('precio_compra') is-invalid @enderror" 
                                               id="precio_compra" name="precio_compra" value="{{ old('precio_compra', $producto['precio_compra'] ?? 0) }}" required>
                                        @error('precio_compra')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="precio_venta" class="form-label">Precio de Venta *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" step="0.01" class="form-control @error('precio_venta') is-invalid @enderror" 
                                               id="precio_venta" name="precio_venta" value="{{ old('precio_venta', $producto['precio_venta'] ?? 0) }}" required>
                                        @error('precio_venta')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="precio_oferta" class="form-label">Precio de Oferta</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" step="0.01" class="form-control @error('precio_oferta') is-invalid @enderror" 
                                               id="precio_oferta" name="precio_oferta" value="{{ old('precio_oferta', $producto['precio_oferta'] ?? '') }}">
                                        @error('precio_oferta')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="stock" class="form-label">Stock Actual *</label>
                                        <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                               id="stock" name="stock" value="{{ old('stock', $producto['stock'] ?? 1) }}" required min="0">
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="stock_minimo" class="form-label">Stock Mínimo *</label>
                                        <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror" 
                                               id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', $producto['stock_minimo'] ?? 1) }}" required min="1">
                                        @error('stock_minimo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <!-- Margen calculado -->
                                <div class="mt-3 pt-2 border-top">
                                    <small class="text-muted">Margen estimado:</small>
                                    <span id="margen" class="badge bg-secondary ms-2">0%</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Proveedor y Categorías -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Clasificación</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor *</label>
                                    <select class="form-select @error('proveedor_id') is-invalid @enderror" id="proveedor_id" name="proveedor_id" required>
                                        <option value="">Seleccionar proveedor</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor['id'] }}" {{ (old('proveedor_id', $producto['proveedor_id'] ?? '') == $proveedor['id']) ? 'selected' : '' }}>
                                                {{ $proveedor['nombre'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('proveedor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="categorias" class="form-label">Categorías *</label>
                                    <select class="form-select @error('categorias') is-invalid @enderror" 
                                            id="categorias" name="categorias[]" multiple required>
                                        @php
                                            $categoriasSeleccionadas = old('categorias', isset($producto['categorias']) ? array_column($producto['categorias'], 'id') : []);
                                        @endphp
                                        @foreach($categorias as $id => $nombre)
                                            <option value="{{ $id }}" {{ in_array($id, $categoriasSeleccionadas) ? 'selected' : '' }}>
                                                {{ $nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Mantén presionada la tecla Ctrl (Cmd en Mac) para seleccionar múltiples categorías
                                    </small>
                                    @error('categorias')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información adicional -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Información Adicional</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="codigo_barras" class="form-label">Código de Barras</label>
                                    <input type="text" class="form-control @error('codigo_barras') is-invalid @enderror" 
                                           id="codigo_barras" name="codigo_barras" value="{{ old('codigo_barras', $producto['codigo_barras'] ?? '') }}">
                                    @error('codigo_barras')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="ubicacion" class="form-label">Ubicación (Estante)</label>
                                    <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" 
                                           id="ubicacion" name="ubicacion" value="{{ old('ubicacion', $producto['ubicacion'] ?? '') }}" placeholder="Ej: E1-A2">
                                    @error('ubicacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado *</label>
                                    <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                        <option value="">Seleccionar estado</option>
                                        <option value="activo" {{ (old('estado', $producto['estado'] ?? '') == 'activo') ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ (old('estado', $producto['estado'] ?? '') == 'inactivo') ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notas internas -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Notas Internas</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <textarea class="form-control @error('notas_internas') is-invalid @enderror" 
                                      id="notas_internas" name="notas_internas" rows="2">{{ old('notas_internas', $producto['notas_internas'] ?? '') }}</textarea>
                            @error('notas_internas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Sección de imágenes -->
                @include('productos.partials.imagenes', ['producto' => $producto])
                
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Actualizar Producto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.5rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    select[multiple] {
        height: 150px;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcular margen automáticamente
    const precioCompra = document.getElementById('precio_compra');
    const precioVenta = document.getElementById('precio_venta');
    
    function calcularMargen() {
        const compra = parseFloat(precioCompra.value) || 0;
        const venta = parseFloat(precioVenta.value) || 0;
        
        if (compra > 0 && venta > 0) {
            const margen = ((venta - compra) / compra) * 100;
            const margenElement = document.getElementById('margen');
            if (margenElement) {
                margenElement.textContent = margen.toFixed(2) + '%';
                margenElement.className = 'badge ' + (margen >= 30 ? 'bg-success' : margen >= 15 ? 'bg-warning' : 'bg-danger');
            }
        }
    }
    
    if (precioCompra && precioVenta) {
        precioCompra.addEventListener('input', calcularMargen);
        precioVenta.addEventListener('input', calcularMargen);
        calcularMargen();
    }
    
    // Validación de precios
    const form = document.getElementById('productoForm');
    form.addEventListener('submit', function(e) {
        const compra = parseFloat(precioCompra.value) || 0;
        const venta = parseFloat(precioVenta.value) || 0;
        
        if (venta < compra) {
            e.preventDefault();
            alert('El precio de venta no puede ser menor que el precio de compra');
            precioVenta.focus();
        }
        
        // Validar que al menos una categoría esté seleccionada
        const categoriasSelect = document.getElementById('categorias');
        const categoriasSeleccionadas = Array.from(categoriasSelect.selectedOptions).map(option => option.value);
        
        if (categoriasSeleccionadas.length === 0) {
            e.preventDefault();
            alert('Debes seleccionar al menos una categoría');
            categoriasSelect.focus();
        }
    });
    
    // Mejorar la experiencia del select múltiple
    const categoriasSelect = document.getElementById('categorias');
    if (categoriasSelect) {
        // Agregar buscador
        const searchDiv = document.createElement('div');
        searchDiv.className = 'mb-2';
        searchDiv.innerHTML = `
            <input type="text" id="categoria-search" class="form-control form-control-sm" placeholder="Buscar categoría...">
        `;
        categoriasSelect.parentNode.insertBefore(searchDiv, categoriasSelect);
        
        // Función de búsqueda
        document.getElementById('categoria-search').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const options = categoriasSelect.options;
            
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                const text = option.text.toLowerCase();
                option.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });
    }
    
    // Auto-generar SKU si está vacío
    const skuInput = document.getElementById('sku');
    const nombreInput = document.getElementById('nombre');
    
    if (skuInput && nombreInput && !skuInput.value) {
        nombreInput.addEventListener('blur', function() {
            if (!skuInput.value && this.value) {
                // Generar SKU a partir del nombre
                const nombre = this.value
                    .toUpperCase()
                    .replace(/[^A-Z0-9]/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '')
                    .substring(0, 20);
                    
                const timestamp = Date.now().toString().slice(-4);
                skuInput.value = nombre + '-' + timestamp;
            }
        });
    }
    
    // Auto-generar código de barras si está vacío
    const codigoBarrasInput = document.getElementById('codigo_barras');
    if (codigoBarrasInput && !codigoBarrasInput.value) {
        const generateBarcodeBtn = document.createElement('button');
        generateBarcodeBtn.type = 'button';
        generateBarcodeBtn.className = 'btn btn-sm btn-outline-secondary mt-1';
        generateBarcodeBtn.innerHTML = '<i class="fas fa-barcode me-1"></i> Generar código';
        generateBarcodeBtn.onclick = function() {
            // Generar código de barras aleatorio (EAN-13 like)
            const prefix = '789'; // Prefijo para Guatemala
            const random = Math.floor(Math.random() * 1000000000).toString().padStart(9, '0');
            const fullCode = prefix + random;
            
            // Calcular dígito de control
            let sum = 0;
            for (let i = 0; i < fullCode.length; i++) {
                const digit = parseInt(fullCode[i]);
                sum += (i % 2 === 0) ? digit : digit * 3;
            }
            const checkDigit = (10 - (sum % 10)) % 10;
            
            codigoBarrasInput.value = fullCode + checkDigit;
        };
        
        codigoBarrasInput.parentNode.appendChild(generateBarcodeBtn);
    }
});
</script>
@endsection