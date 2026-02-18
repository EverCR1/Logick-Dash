@extends('layouts.app')

@section('title', 'Crear Servicio')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Servicio
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('servicios.store') }}" method="POST" enctype="multipart/form-data" id="servicioForm">
                        @csrf

                        <div class="row">
                            <!-- Código y Nombre -->
                            <div class="col-md-6 mb-3">
                                <label for="codigo" class="form-label">
                                    Código <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                                       id="codigo" name="codigo" value="{{ old('codigo') }}" 
                                       placeholder="Ej: SER-INST-WIN11" required>
                                <small class="form-text text-muted">Código único para identificar el servicio</small>
                                @error('codigo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">
                                    Nombre del Servicio <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" name="nombre" value="{{ old('nombre') }}" 
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
                                      id="descripcion" name="descripcion" rows="3" 
                                      placeholder="Describe detalladamente el servicio...">{{ old('descripcion') }}</textarea>
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
                                           value="{{ old('inversion_estimada', 0) }}" required>
                                </div>
                                <small class="form-text text-muted">Costo estimado de proveer el servicio</small>
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
                                           value="{{ old('precio_venta', 0) }}" required>
                                </div>
                                <small class="form-text text-muted">Precio regular al cliente</small>
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
                                           value="{{ old('precio_oferta') }}">
                                </div>
                                <small class="form-text text-muted">Precio promocional (opcional)</small>
                                @error('precio_oferta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Cálculo de margen -->
                        <div class="alert alert-info mb-3" id="margenAlert" style="display: none;">
                            <div class="d-flex justify-content-between">
                                <span>Margen de ganancia estimado:</span>
                                <strong id="margenValue">0%</strong>
                            </div>
                            <small class="text-muted" id="margenDetail">Cálculo basado en inversión y precio de venta</small>
                        </div>

                        <!-- Estado -->
                        <div class="mb-3">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="estado" 
                                           id="estado_activo" value="activo" 
                                           {{ old('estado', 'activo') == 'activo' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado_activo">
                                        <span class="badge bg-success">Activo</span>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="estado" 
                                           id="estado_inactivo" value="inactivo" 
                                           {{ old('estado') == 'inactivo' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="estado_inactivo">
                                        <span class="badge bg-secondary">Inactivo</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Imagen -->
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen del Servicio</label>
                            <input type="file" class="form-control @error('imagen') is-invalid @enderror" 
                                   id="imagen" name="imagen" accept="image/*">
                            <small class="form-text text-muted">
                                Formatos: JPEG, PNG, JPG, GIF, WEBP. Máximo: 5MB
                            </small>
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Vista previa de imagen -->
                            <div class="mt-2" id="imagenPreview" style="display: none;">
                                <img id="preview" src="#" alt="Vista previa" 
                                     class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>

                        <!-- Notas Internas -->
                        <div class="mb-3">
                            <label for="notas_internas" class="form-label">Notas Internas</label>
                            <textarea class="form-control @error('notas_internas') is-invalid @enderror" 
                                      id="notas_internas" name="notas_internas" rows="3" 
                                      placeholder="Información interna para el equipo...">{{ old('notas_internas') }}</textarea>
                            <small class="form-text text-muted">Esta información no es visible para los clientes</small>
                            @error('notas_internas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('servicios.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Servicio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vista previa de imagen
    const imagenInput = document.getElementById('imagen');
    const imagenPreview = document.getElementById('imagenPreview');
    const preview = document.getElementById('preview');
    
    if (imagenInput) {
        imagenInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    imagenPreview.style.display = 'block';
                }
                
                reader.readAsDataURL(this.files[0]);
            } else {
                imagenPreview.style.display = 'none';
                preview.src = '#';
            }
        });
    }
    
    // Cálculo de margen en tiempo real
    const inversionInput = document.getElementById('inversion_estimada');
    const precioVentaInput = document.getElementById('precio_venta');
    const precioOfertaInput = document.getElementById('precio_oferta');
    const margenAlert = document.getElementById('margenAlert');
    const margenValue = document.getElementById('margenValue');
    const margenDetail = document.getElementById('margenDetail');
    
    function calcularMargen() {
        const inversion = parseFloat(inversionInput.value) || 0;
        const precioVenta = parseFloat(precioVentaInput.value) || 0;
        const precioOferta = parseFloat(precioOfertaInput.value) || 0;
        
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
            margenValue.textContent = margen.toFixed(1) + '%';
            margenAlert.className = `alert alert-${claseColor} mb-3`;
            
            // Detalle
            let detalle = `Precio final: Q${precioFinal.toFixed(2)}`;
            if (precioOferta > 0) {
                detalle += ` (Oferta: Q${precioOferta.toFixed(2)} | Regular: Q${precioVenta.toFixed(2)})`;
            }
            
            margenDetail.textContent = detalle;
            margenAlert.style.display = 'block';
        } else {
            margenAlert.style.display = 'none';
        }
    }
    
    // Escuchar cambios en los inputs de precio
    if (inversionInput && precioVentaInput && precioOfertaInput) {
        [inversionInput, precioVentaInput, precioOfertaInput].forEach(input => {
            input.addEventListener('input', calcularMargen);
        });
        
        // Calcular margen inicial si hay valores
        calcularMargen();
    }
    
    // Validación de formulario
    const form = document.getElementById('servicioForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const inversion = parseFloat(inversionInput.value) || 0;
            const precioVenta = parseFloat(precioVentaInput.value) || 0;
            const precioOferta = parseFloat(precioOfertaInput.value) || 0;
            
            // Validar que precio de venta sea mayor que inversión
            if (precioVenta <= inversion) {
                e.preventDefault();
                alert('El precio de venta debe ser mayor que la inversión estimada.');
                precioVentaInput.focus();
                return;
            }
            
            // Validar que precio de oferta sea menor que precio regular
            if (precioOferta > 0 && precioOferta >= precioVenta) {
                e.preventDefault();
                alert('El precio de oferta debe ser menor que el precio regular.');
                precioOfertaInput.focus();
                return;
            }
            
            // Mostrar confirmación
            if (!confirm('¿Estás seguro de crear este servicio?')) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
/* Estilos para el formulario */
.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-text {
    font-size: 0.85rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.form-control:focus + .input-group-text {
    border-color: #86b7fe;
    background-color: #e7f1ff;
}

/* Vista previa de imagen */
.img-thumbnail {
    border: 2px dashed #dee2e6;
    padding: 5px;
    transition: all 0.3s ease;
}

.img-thumbnail:hover {
    border-color: #0d6efd;
}

/* Alertas */
.alert {
    border-left: 4px solid transparent;
}

.alert-success {
    border-left-color: #198754;
}

.alert-info {
    border-left-color: #0dcaf0;
}

.alert-warning {
    border-left-color: #ffc107;
}

.alert-danger {
    border-left-color: #dc3545;
}
</style>
@endpush