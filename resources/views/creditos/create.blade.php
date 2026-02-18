@extends('layouts.app')

@section('title', 'Crear Crédito')

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

                            <!-- Capital Restante (Solo lectura) -->
                            <div class="col-md-6 mb-3">
                                <label for="capital_restante" class="form-label">
                                    Capital Restante (Q) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Q</span>
                                    <input type="number" step="0.01" 
                                           class="form-control @error('capital_restante') is-invalid @enderror" 
                                           id="capital_restante" name="capital_restante" 
                                           value="{{ old('capital_restante') }}" 
                                           readonly 
                                           style="background-color: #e9ecef; cursor: not-allowed;">
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Se sincroniza automáticamente con el capital total
                                </small>
                                @error('capital_restante')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Información calculada -->
                        <div class="alert alert-info mb-3" id="infoCalculada">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Estado del crédito:</span>
                                <span class="badge bg-success" id="estadoCredito">Nuevo</span>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <span>Capital restante:</span>
                                <strong id="capitalRestanteDisplay">Q 0.00</strong>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Al crear un crédito, el capital restante es igual al capital total.
                            </small>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('creditos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
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
    const capitalRestanteDisplay = document.getElementById('capitalRestanteDisplay');
    const estadoCredito = document.getElementById('estadoCredito');
    const btnSubmit = document.getElementById('btnSubmit');
    
    // Función para actualizar el capital restante y la información
    function actualizarCapitalRestante() {
        const capital = parseFloat(capitalInput.value) || 0;
        
        // Actualizar capital restante (siempre igual al capital)
        capitalRestanteInput.value = capital.toFixed(2);
        
        // Actualizar display
        capitalRestanteDisplay.textContent = `Q ${capital.toFixed(2)}`;
        
        // Actualizar estado
        if (capital > 0) {
            estadoCredito.textContent = 'Nuevo Crédito';
            estadoCredito.className = 'badge bg-success';
            infoDiv.className = 'alert alert-success mb-3';
        } else {
            estadoCredito.textContent = 'Sin Definir';
            estadoCredito.className = 'badge bg-secondary';
            infoDiv.className = 'alert alert-secondary mb-3';
        }
    }
    
    // Escuchar cambios en el capital
    capitalInput.addEventListener('input', actualizarCapitalRestante);
    
    // Validación de formulario
    const form = document.getElementById('creditoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const capital = parseFloat(capitalInput.value) || 0;
            
            // Validar que capital sea mayor que 0
            if (capital <= 0) {
                e.preventDefault();
                
                // Usar SweetAlert si está disponible
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El capital debe ser mayor a 0.',
                        confirmButtonColor: '#dc3545'
                    });
                } else {
                    alert('El capital debe ser mayor a 0.');
                }
                
                capitalInput.focus();
                return;
            }
            
            // Confirmación con SweetAlert
            if (typeof Swal !== 'undefined') {
                e.preventDefault();
                
                Swal.fire({
                    title: '¿Confirmar creación?',
                    html: `
                        <div class="text-start">
                            <p><strong>Cliente:</strong> ${document.getElementById('nombre_cliente').value}</p>
                            <p><strong>Capital:</strong> Q ${capital.toFixed(2)}</p>
                            <p><strong>Fecha:</strong> ${document.getElementById('fecha_credito').value}</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2E7D32',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, crear crédito',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Deshabilitar botón para evitar doble envío
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
                        
                        // Enviar formulario
                        form.submit();
                    }
                });
            } else {
                // Si no hay SweetAlert, usar confirm nativo
                if (!confirm('¿Estás seguro de crear este crédito?')) {
                    e.preventDefault();
                }
            }
        });
    }
    
    // Ejecutar al cargar
    actualizarCapitalRestante();
});
</script>
@endpush