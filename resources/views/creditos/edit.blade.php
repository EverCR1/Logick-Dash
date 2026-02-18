@extends('layouts.app')

@section('title', 'Editar Crédito')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('creditos.index') }}">Créditos</a></li>
    <li class="breadcrumb-item active">Editar: {{ $credito['nombre_cliente'] ?? '' }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Crédito
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($credito) && isset($credito['id']))
                    <form action="{{ route('creditos.update', $credito['id']) }}" method="POST" id="creditoForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Nombre del Cliente -->
                            <div class="col-md-6 mb-3">
                                <label for="nombre_cliente" class="form-label">
                                    Nombre del Cliente <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('nombre_cliente') is-invalid @enderror" 
                                       id="nombre_cliente" name="nombre_cliente" 
                                       value="{{ old('nombre_cliente', $credito['nombre_cliente'] ?? '') }}" 
                                       placeholder="Ej: Juan Pérez" required>
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
                                           value="{{ old('capital', $credito['capital'] ?? 0) }}" required>
                                </div>
                                @error('capital')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Producto o Servicio -->
                        <div class="mb-3">
                            <label for="producto_o_servicio_dado" class="form-label">Producto o Servicio</label>
                            <textarea class="form-control @error('producto_o_servicio_dado') is-invalid @enderror" 
                                      id="producto_o_servicio_dado" name="producto_o_servicio_dado" rows="2">{{ old('producto_o_servicio_dado', $credito['producto_o_servicio_dado'] ?? '') }}</textarea>
                            @error('producto_o_servicio_dado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Fecha Crédito -->
                        <div class="mb-3">
                            <label for="fecha_credito" class="form-label">
                                Fecha del Crédito <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('fecha_credito') is-invalid @enderror" 
                                   id="fecha_credito" name="fecha_credito" 
                                   value="{{ old('fecha_credito', isset($credito['fecha_credito']) ? \Carbon\Carbon::parse($credito['fecha_credito'])->format('Y-m-d') : date('Y-m-d')) }}" required>
                            @error('fecha_credito')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Información actual -->
                        <div class="alert alert-info mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Capital Restante Actual:</small>
                                    <h6 class="mb-0">
                                        @if(($credito['capital_restante'] ?? 0) > 0)
                                            <strong class="text-danger">Q{{ number_format($credito['capital_restante'], 2) }}</strong>
                                        @else
                                            <strong class="text-success">Q0.00</strong>
                                        @endif
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Estado Actual:</small>
                                    <h6 class="mb-0">
                                        @php
                                            $estadoColors = [
                                                'activo' => 'danger',
                                                'abonado' => 'warning',
                                                'pagado' => 'success'
                                            ];
                                            $estadoLabels = [
                                                'activo' => 'Activo',
                                                'abonado' => 'Abonado',
                                                'pagado' => 'Pagado'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $estadoColors[$credito['estado'] ?? 'activo'] }}">
                                            {{ $estadoLabels[$credito['estado'] ?? 'activo'] }}
                                        </span>
                                    </h6>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                Nota: El capital restante y estado se actualizan automáticamente al registrar pagos.
                            </small>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('creditos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <div class="d-flex gap-2">
                                <a href="{{ route('creditos.show', $credito['id']) }}" class="btn btn-info">
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
                        No se pudo cargar la información del crédito.
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
    // Validación de formulario
    const form = document.getElementById('creditoForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const capital = parseFloat(document.getElementById('capital').value) || 0;
            
            // Validar que capital sea mayor que 0
            if (capital <= 0) {
                e.preventDefault();
                alert('El capital debe ser mayor a 0.');
                document.getElementById('capital').focus();
                return;
            }
            
            // Mostrar confirmación
            if (!confirm('¿Guardar los cambios en este crédito?')) {
                e.preventDefault();
            }
        });
    }
});
</script>
@endpush