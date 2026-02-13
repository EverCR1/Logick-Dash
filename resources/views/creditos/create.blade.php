@extends('layouts.app')

@section('title', 'Crear Crédito - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('creditos.index') }}">Créditos</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Crédito
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('creditos.store') }}" method="POST" id="creditoForm">
                        @csrf

                        <div class="row">
                            <!-- Nombre del Cliente -->
                            <div class="col-md-6 mb-3">
                                <label for="nombre_cliente" class="form-label">
                                    Nombre del Cliente <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nombre_cliente') is-invalid @enderror" 
                                       id="nombre_cliente" name="nombre_cliente" value="{{ old('nombre_cliente') }}" 
                                       placeholder="Ej: Juan Pérez" required>
                                <small class="form-text text-muted">Nombre completo del cliente</small>
                                @error('nombre_cliente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Capital -->
                            <div class="col-md-6 mb-3">
                                <label for="capital" class="form-label">
                                    Capital Total (Q) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" step="0.01" min="0.01" 
                                           class="form-control @error('capital') is-invalid @enderror" 
                                           id="capital" name="capital" 
                                           value="{{ old('capital') }}" required>
                                </div>
                                <small class="form-text text-muted">Monto total del crédito</small>
                                @error('capital')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Producto o Servicio -->
                        <div class="mb-3">
                            <label for="producto_o_servicio_dado" class="form-label">Producto o Servicio</label>
                            <textarea class="form-control @error('producto_o_servicio_dado') is-invalid @enderror" 
                                      id="producto_o_servicio_dado" name="producto_o_servicio_dado" rows="2" 
                                      placeholder="Describe el producto o servicio que se dio a crédito...">{{ old('producto_o_servicio_dado') }}</textarea>
                            @error('producto_o_servicio_dado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Fecha Crédito -->
                            <div class="col-md-6 mb-3">
                                <label for="fecha_credito" class="form-label">
                                    Fecha del Crédito <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('fecha_credito') is-invalid @enderror" 
                                       id="fecha_credito" name="fecha_credito" 
                                       value="{{ old('fecha_credito', date('Y-m-d')) }}" required>
                                @error('fecha_credito')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Capital Restante -->
                            <div class="col-md-6 mb-3">
                                <label for="capital_restante" class="form-label">
                                    Capital Restante (Q) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('capital_restante') is-invalid @enderror" 
                                           id="capital_restante" name="capital_restante" 
                                           value="{{ old('capital_restante') }}" required>
                                </div>
                                <small class="form-text text-muted">Lo que el cliente aún debe</small>
                                @error('capital_restante')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Información calculada -->
                        <div class="alert alert-info mb-3" id="infoCalculada" style="display: none;">
                            <div class="d-flex justify-content-between">
                                <span>Porcentaje pagado:</span>
                                <strong id="porcentajePagado">0%</strong>
                            </div>
                            <small class="text-muted" id="detallePago">Capital restante no puede ser mayor al capital total</small>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('creditos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Crédito
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
    const capitalInput = document.getElementById('capital');
    const capitalRestanteInput = document.getElementById('capital_restante');
    const infoDiv = document.getElementById('infoCalculada');
    const porcentajeSpan = document.getElementById('porcentajePagado');
    const detalleSpan = document.getElementById('detallePago');
    
    function calcularInformacion() {
        const capital = parseFloat(capitalInput.value) || 0;
        const capitalRestante = parseFloat(capitalRestanteInput.value) || 0;
        
        if (capital > 0) {
            const pagado = capital - capitalRestante;
            const porcentaje = capitalRestante > 0 ? ((pagado / capital) * 100) : 100;
            
            // Determinar clase CSS
            let claseColor = 'info';
            if (porcentaje >= 100) claseColor = 'success';
            else if (porcentaje >= 50) claseColor = 'info';
            else if (porcentaje > 0) claseColor = 'warning';
            else claseColor = 'danger';
            
            // Actualizar UI
            porcentajeSpan.textContent = porcentaje.toFixed(1) + '%';
            infoDiv.className = `alert alert-${claseColor} mb-3`;
            
            // Detalle
            let detalle = `Pagado: Q${pagado.toFixed(2)} | Restante: Q${capitalRestante.toFixed(2)}`;
            if (capitalRestante > capital) {
                detalle = '⚠️ El capital restante no puede ser mayor al capital total';
            }
            
            detalleSpan.textContent = detalle;
            infoDiv.style.display = 'block';
        } else {
            infoDiv.style.display = 'none';
        }
    }
    
    // Escuchar cambios en los inputs
    [capitalInput, capitalRestanteInput].forEach(input => {
        input.addEventListener('input', calcularInformacion);
    });
    
    // Calcular al cargar
    setTimeout(calcularInformacion, 100);
    
    // Validación de formulario
    const form = document.getElementById('creditoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const capital = parseFloat(capitalInput.value) || 0;
            const capitalRestante = parseFloat(capitalRestanteInput.value) || 0;
            
            // Validar que capital restante no sea mayor que capital
            if (capitalRestante > capital) {
                e.preventDefault();
                alert('El capital restante no puede ser mayor al capital total.');
                capitalRestanteInput.focus();
                return;
            }
            
            // Validar que capital sea mayor que 0
            if (capital <= 0) {
                e.preventDefault();
                alert('El capital debe ser mayor a 0.');
                capitalInput.focus();
                return;
            }
            
            // Mostrar confirmación
            if (!confirm('¿Estás seguro de crear este crédito?')) {
                e.preventDefault();
            }
        });
    }
    
    // Sincronizar capital y capital restante por defecto
    capitalInput.addEventListener('input', function() {
        const capital = parseFloat(this.value) || 0;
        const currentRestante = parseFloat(capitalRestanteInput.value) || 0;
        
        // Si capital restante está vacío o es mayor que el nuevo capital, ajustarlo
        if (!capitalRestanteInput.value || currentRestante > capital) {
            capitalRestanteInput.value = capital;
            calcularInformacion();
        }
    });
});
</script>
@endpush