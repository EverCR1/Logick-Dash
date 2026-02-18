@extends('layouts.app')

@section('title', 'Editar Servicio')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
    <li class="breadcrumb-item active">Editar: {{ $servicio['nombre'] ?? '' }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Servicio
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($servicio) && isset($servicio['id']))
                    <form action="{{ route('servicios.update', $servicio['id']) }}" method="POST" id="servicioForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Código y Nombre -->
                            <div class="col-md-6 mb-3">
                                <label for="codigo" class="form-label">
                                    Código <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                       id="codigo" name="codigo" 
                                       value="{{ old('codigo', $servicio['codigo'] ?? '') }}" 
                                       placeholder="Ej: SER-INST-WIN11" required>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    Nombre del Servicio <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" 
                                       value="{{ old('nombre', $servicio['nombre'] ?? '') }}" 
                                       placeholder="Ej: Instalación Windows 11 Pro" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $servicio['descripcion'] ?? '') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Inversión Estimada -->
                            <div class="col-md-4 mb-3">
                                <label for="inversion_estimada" class="form-label">
                                    Inversión Estimada (Q) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('inversion_estimada') is-invalid @enderror" 
                                           id="inversion_estimada" name="inversion_estimada" 
                                           value="{{ old('inversion_estimada', $servicio['inversion_estimada'] ?? 0) }}" required>
                                </div>
                                @error('inversion_estimada')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Precio de Venta -->
                            <div class="col-md-4 mb-3">
                                <label for="precio_venta" class="form-label">
                                    Precio de Venta (Q) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('precio_venta') is-invalid @enderror" 
                                           id="precio_venta" name="precio_venta" 
                                           value="{{ old('precio_venta', $servicio['precio_venta'] ?? 0) }}" required>
                                </div>
                                @error('precio_venta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Precio de Oferta -->
                            <div class="col-md-4 mb-3">
                                <label for="precio_oferta" class="form-label">Precio de Oferta (Q)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('precio_oferta') is-invalid @enderror" 
                                           id="precio_oferta" name="precio_oferta" 
                                           value="{{ old('precio_oferta', $servicio['precio_oferta'] ?? '') }}">
                                </div>
                                @error('precio_oferta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Cálculo de margen -->
                        <div class="alert alert-info mb-3" id="margenAlert" style="display: none;">
                            <div class="d-flex justify-content-between">
                                <span>Margen de ganancia:</span>
                                <strong id="margenValue">0%</strong>
                            </div>
                            <small class="text-muted" id="margenDetail"></small>
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="estado" 
                                           id="estado_activo" value="activo" 
                                           {{ old('estado', $servicio['estado'] ?? 'activo') == 'activo' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado_activo">
                                        <span class="badge bg-success">Activo</span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="estado" 
                                           id="estado_inactivo" value="inactivo" 
                                           {{ old('estado', $servicio['estado'] ?? 'activo') == 'inactivo' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado_inactivo">
                                        <span class="badge bg-secondary">Inactivo</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Imagen actual -->
                        @if(isset($servicio['imagenes']) && count($servicio['imagenes']) > 0)
                        <div class="mb-3">
                            <label class="form-label">Imagen Actual</label>
                            <div class="d-flex align-items-start gap-3">
                                @php
                                    $imagen = $servicio['imagenes'][0];
                                    $urlImagen = !empty($imagen['url_thumb']) ? $imagen['url_thumb'] : 
                                               (!empty($imagen['url_medium']) ? $imagen['url_medium'] : 
                                               (!empty($imagen['url']) ? $imagen['url'] : ''));
                                @endphp
                                @if($urlImagen)
                                <div class="position-relative">
                                    <img src="{{ $urlImagen }}" alt="Imagen del servicio" 
                                         class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1">
                                        <small class="text-muted">Nombre: {{ $imagen['nombre_original'] ?? 'Sin nombre' }}</small>
                                    </p>
                                    <p class="mb-2">
                                        <small class="text-muted">Tamaño: {{ number_format(($imagen['tamaño'] ?? 0) / 1024, 1) }} KB</small>
                                    </p>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="eliminarImagen('{{ $servicio['id'] }}', '{{ $imagen['id'] ?? '' }}')">
                                        <i class="fas fa-trash me-1"></i> Eliminar Imagen
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Subir nueva imagen -->
                        <div class="mb-3">
                            <label for="nueva_imagen" class="form-label">Nueva Imagen (Reemplazar)</label>
                            <input type="file" class="form-control" 
                                   id="nueva_imagen" name="nueva_imagen" accept="image/*">
                            <small class="form-text text-muted">
                                Deja en blanco para mantener la imagen actual
                            </small>
                            
                            <div class="mt-2" id="imagenPreview" style="display: none;">
                                <img id="preview" src="#" alt="Vista previa" 
                                     class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                            </div>
                        </div>

                        <!-- Notas Internas -->
                        <div class="mb-3">
                            <label for="notas_internas" class="form-label">Notas Internas</label>
                            <textarea class="form-control @error('notas_internas') is-invalid @enderror" 
                                      id="notas_internas" name="notas_internas" rows="3">{{ old('notas_internas', $servicio['notas_internas'] ?? '') }}</textarea>
                            @error('notas_internas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('servicios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <div class="d-flex gap-2">
                                <a href="{{ route('servicios.show', $servicio['id']) }}" class="btn btn-info">
                                    <i class="fas fa-eye me-2"></i>Ver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                    @else
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No se pudo cargar la información del servicio.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cálculo de margen
    function calcularMargen() {
        const inversion = parseFloat(document.getElementById('inversion_estimada').value) || 0;
        const precioVenta = parseFloat(document.getElementById('precio_venta').value) || 0;
        const precioOferta = parseFloat(document.getElementById('precio_oferta').value) || 0;
        
        const precioFinal = precioOferta > 0 ? precioOferta : precioVenta;
        
        if (inversion > 0 && precioFinal > 0) {
            const margen = ((precioFinal - inversion) / inversion) * 100;
            
            // Determinar clase CSS
            let claseColor = 'info';
            if (margen >= 100) claseColor = 'success';
            else if (margen >= 50) claseColor = 'info';
            else if (margen >= 20) claseColor = 'warning';
            else claseColor = 'danger';
            
            // Actualizar UI
            document.getElementById('margenValue').textContent = margen.toFixed(1) + '%';
            document.getElementById('margenAlert').className = `alert alert-${claseColor} mb-3`;
            
            // Detalle
            let detalle = `Precio final: Q${precioFinal.toFixed(2)}`;
            if (precioOferta > 0) {
                detalle += ` (Oferta: Q${precioOferta.toFixed(2)} | Regular: Q${precioVenta.toFixed(2)})`;
            }
            
            document.getElementById('margenDetail').textContent = detalle;
            document.getElementById('margenAlert').style.display = 'block';
        } else {
            document.getElementById('margenAlert').style.display = 'none';
        }
    }
    
    // Calcular margen al cargar
    calcularMargen();
    
    // Escuchar cambios en los inputs de precio
    ['inversion_estimada', 'precio_venta', 'precio_oferta'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', calcularMargen);
        }
    });
    
    // Vista previa de nueva imagen
    const nuevaImagenInput = document.getElementById('nueva_imagen');
    if (nuevaImagenInput) {
        nuevaImagenInput.addEventListener('change', function() {
            const previewDiv = document.getElementById('imagenPreview');
            const previewImg = document.getElementById('preview');
            
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewDiv.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            } else {
                previewDiv.style.display = 'none';
                previewImg.src = '#';
            }
        });
    }
    
    // Validación de formulario
    const form = document.getElementById('servicioForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const inversion = parseFloat(document.getElementById('inversion_estimada').value) || 0;
            const precioVenta = parseFloat(document.getElementById('precio_venta').value) || 0;
            const precioOferta = parseFloat(document.getElementById('precio_oferta').value) || 0;
            
            // Validar que precio de venta sea mayor que inversión
            if (precioVenta <= inversion) {
                e.preventDefault();
                alert('El precio de venta debe ser mayor que la inversión estimada.');
                document.getElementById('precio_venta').focus();
                return;
            }
            
            // Validar que precio de oferta sea menor que precio regular
            if (precioOferta > 0 && precioOferta >= precioVenta) {
                e.preventDefault();
                alert('El precio de oferta debe ser menor que el precio regular.');
                document.getElementById('precio_oferta').focus();
                return;
            }
            
            // Mostrar confirmación
            if (!confirm('¿Guardar los cambios en este servicio?')) {
                e.preventDefault();
            }
        });
    }
});

// Eliminar imagen
function eliminarImagen(servicioId, imagenId) {
    if (!confirm('¿Estás seguro de eliminar esta imagen?')) {
        return;
    }
    
    fetch(`/servicios/${servicioId}/imagenes/${imagenId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload(); // Recargar para ver los cambios
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar la imagen');
    });
}
</script>
@endpush