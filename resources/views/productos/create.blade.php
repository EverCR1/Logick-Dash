@extends('layouts.app')

@section('title', 'Crear Producto - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
    <li class="breadcrumb-item active">Crear Producto</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Crear Nuevo Producto</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('productos.store') }}" method="POST" id="productoForm" enctype="multipart/form-data">
                @csrf
                
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
                                               id="sku" name="sku" value="{{ old('sku') }}" required>
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre del Producto *</label>
                                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                               id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                                  id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                                        @error('descripcion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="marca" class="form-label">Marca</label>
                                        <input type="text" class="form-control @error('marca') is-invalid @enderror" 
                                               id="marca" name="marca" value="{{ old('marca') }}">
                                        @error('marca')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="color" class="form-label">Color</label>
                                        <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                               id="color" name="color" value="{{ old('color') }}">
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
                                              id="especificaciones" name="especificaciones" rows="4">{{ old('especificaciones') }}</textarea>
                                    @error('especificaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sección para subir imágenes -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Imágenes del Producto</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="imagenes" class="form-label">Subir imágenes (opcional)</label>
                                    <input type="file" 
                                        class="form-control" 
                                        id="imagenes" 
                                        name="imagenes[]" 
                                        multiple 
                                        accept="image/*">
                                    <div class="form-text">
                                        Puedes subir imágenes ahora o después de crear el producto. 
                                        Formatos aceptados: JPEG, PNG, GIF, WebP. Máximo 5MB por imagen.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="establecer_principal" name="establecer_principal" value="1" checked>
                                        <label class="form-check-label" for="establecer_principal">
                                            Establecer la primera imagen como principal
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Vista previa -->
                                <div class="row mt-3 d-none" id="imagenes-preview"></div>
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
                                               id="precio_compra" name="precio_compra" value="{{ old('precio_compra') }}" required>
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
                                               id="precio_venta" name="precio_venta" value="{{ old('precio_venta') }}" required>
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
                                               id="precio_oferta" name="precio_oferta" value="{{ old('precio_oferta') }}">
                                        @error('precio_oferta')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="stock" class="form-label">Stock Actual *</label>
                                        <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                               id="stock" name="stock" value="{{ old('stock', 1) }}" required min="0">
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="stock_minimo" class="form-label">Stock Mínimo *</label>
                                        <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror" 
                                               id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', 1) }}" required min="1">
                                        @error('stock_minimo')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
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
                                            <option value="{{ $proveedor['id'] }}" {{ old('proveedor_id') == $proveedor['id'] ? 'selected' : '' }}>
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
                                        @foreach($categorias as $id => $nombre)
                                            <option value="{{ $id }}" {{ in_array($id, old('categorias', [])) ? 'selected' : '' }}>
                                                {{ $nombre }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                           id="codigo_barras" name="codigo_barras" value="{{ old('codigo_barras') }}">
                                    @error('codigo_barras')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="ubicacion" class="form-label">Ubicación (Estante)</label>
                                    <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" 
                                           id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}" placeholder="Ej: E1-A2">
                                    @error('ubicacion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado *</label>
                                    <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                        <option value="">Seleccionar estado</option>
                                        <option value="activo" {{ old('estado') == 'activo' ? 'selected' : 'selected' }}>Activo</option>
                                        <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
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
                                      id="notas_internas" name="notas_internas" rows="2">{{ old('notas_internas') }}</textarea>
                            @error('notas_internas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Producto</button>
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
            
            // Crear elemento para mostrar margen
            const margenDiv = document.createElement('div');
            margenDiv.className = 'mt-2';
            margenDiv.innerHTML = `
                <small class="text-muted">Margen estimado:</small>
                <span id="margen" class="badge bg-secondary ms-2">0%</span>
            `;
            precioVenta.parentNode.appendChild(margenDiv);
            
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
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vista previa de imágenes seleccionadas
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('imagenes-preview');
        previewContainer.innerHTML = '';
        previewContainer.classList.remove('d-none');
        
        const files = e.target.files;
        
        if (files.length > 0) {
            previewContainer.innerHTML = '<h6 class="mb-3">Vista previa de imágenes:</h6><div class="row">';
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-6 mb-3';
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" 
                                 class="card-img-top" 
                                 style="height: 100px; object-fit: cover;"
                                 alt="Vista previa ${i+1}">
                            <div class="card-body p-2 text-center">
                                <small class="text-muted">${file.name}</small>
                            </div>
                        </div>
                    `;
                    previewContainer.querySelector('.row').appendChild(col);
                };
                
                reader.readAsDataURL(file);
            }
            
            previewContainer.innerHTML += '</div>';
        }
    });
});
</script>
@endsection