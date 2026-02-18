@extends('layouts.app')

@section('title', 'Ver Servicio')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
    <li class="breadcrumb-item active">{{ $servicio['nombre'] ?? 'Servicio' }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Información del Servicio</h5>
                    <div>
                        <a href="{{ route('servicios.edit', $servicio['id'] ?? '#') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($servicio))
                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Código:</th>
                                    <td>
                                        <span class="badge bg-primary">{{ $servicio['codigo'] ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        <span class="badge {{ ($servicio['estado'] ?? '') == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $servicio['estado'] ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Inversión Estimada:</th>
                                    <td>
                                        <strong>Q{{ number_format($servicio['inversion_estimada'] ?? 0, 2) }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Precio de Venta:</th>
                                    <td>
                                        @if(!empty($servicio['precio_oferta']))
                                            <div>
                                                <span class="text-decoration-line-through text-muted">
                                                    Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}
                                                </span>
                                            </div>
                                            <div class="text-danger">
                                                <strong>Q{{ number_format($servicio['precio_oferta'], 2) }}</strong>
                                                <small class="badge bg-danger ms-2">OFERTA</small>
                                            </div>
                                        @else
                                            <strong>Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}</strong>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Margen:</th>
                                    <td>
                                        @php
                                            $precio_final = $servicio['precio_oferta'] ?? $servicio['precio_venta'] ?? 0;
                                            $inversion = $servicio['inversion_estimada'] ?? 0;
                                            $margen = $inversion > 0 ? (($precio_final - $inversion) / $inversion) * 100 : 0;
                                            $margenClass = $margen >= 100 ? 'success' : ($margen >= 50 ? 'info' : ($margen >= 20 ? 'warning' : 'danger'));
                                        @endphp
                                        <span class="badge bg-{{ $margenClass }}">
                                            {{ number_format($margen, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Fecha Creación:</th>
                                    <td>
                                        {{ isset($servicio['created_at']) ? \Carbon\Carbon::parse($servicio['created_at'])->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Descripción</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $servicio['descripcion'] ?? 'Sin descripción' }}
                        </div>
                    </div>

                    <!-- Imagen -->
                    @if(isset($servicio['imagenes']) && count($servicio['imagenes']) > 0)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Imagen del Servicio</h6>
                        @php
                            $imagen = $servicio['imagenes'][0];
                            $urlImagen = !empty($imagen['url_medium']) ? $imagen['url_medium'] : 
                                       (!empty($imagen['url']) ? $imagen['url'] : '');
                        @endphp
                        @if($urlImagen)
                        <div class="text-center">
                            <img src="{{ $urlImagen }}" alt="{{ $servicio['nombre'] ?? 'Servicio' }}" 
                                 class="img-fluid rounded" style="max-height: 400px; max-width: 100%;">
                            <p class="text-muted mt-2">
                                <small>
                                    {{ $imagen['nombre_original'] ?? 'Sin nombre' }} | 
                                    {{ number_format(($imagen['tamaño'] ?? 0) / 1024, 1) }} KB
                                </small>
                            </p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Notas Internas -->
                    @if(!empty($servicio['notas_internas']))
                    <div>
                        <h6 class="text-muted mb-2">Notas Internas</h6>
                        <div class="p-3 bg-light rounded border">
                            <small>{{ $servicio['notas_internas'] }}</small>
                        </div>
                    </div>
                    @endif

                    @else
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No se pudo cargar la información del servicio.
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('servicios.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver a la lista
                        </a>
                        <div class="d-flex gap-2">
                            <form action="{{ route('servicios.change-status', $servicio['id'] ?? '#') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-{{ ($servicio['estado'] ?? '') == 'activo' ? 'warning' : 'success' }}">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    Cambiar a {{ ($servicio['estado'] ?? '') == 'activo' ? 'Inactivo' : 'Activo' }}
                                </button>
                            </form>
                            
                            <form action="{{ route('servicios.destroy', $servicio['id'] ?? '#') }}" method="POST" 
                                  class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este servicio?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar con información adicional -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Detalles Financieros</h6>
                </div>
                <div class="card-body">
                    @php
                        $precio_final = $servicio['precio_oferta'] ?? $servicio['precio_venta'] ?? 0;
                        $inversion = $servicio['inversion_estimada'] ?? 0;
                        $ganancia_neta = $precio_final - $inversion;
                        $margen = $inversion > 0 ? ($ganancia_neta / $inversion) * 100 : 0;
                    @endphp
                    
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Inversión:</span>
                            <strong>Q{{ number_format($inversion, 2) }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Precio Final:</span>
                            <strong class="{{ !empty($servicio['precio_oferta']) ? 'text-danger' : '' }}">
                                Q{{ number_format($precio_final, 2) }}
                            </strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Ganancia Neta:</span>
                            <strong class="{{ $ganancia_neta >= 0 ? 'text-success' : 'text-danger' }}">
                                Q{{ number_format($ganancia_neta, 2) }}
                            </strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Margen:</span>
                            <span class="badge bg-{{ $margen >= 100 ? 'success' : ($margen >= 50 ? 'info' : ($margen >= 20 ? 'warning' : 'danger')) }}">
                                {{ number_format($margen, 1) }}%
                            </span>
                        </div>
                        @if(!empty($servicio['precio_oferta']))
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Descuento:</span>
                            <span class="badge bg-danger">
                                {{ number_format((($servicio['precio_venta'] - $servicio['precio_oferta']) / $servicio['precio_venta']) * 100, 1) }}%
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Subir nueva imagen -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-2">Actualizar Imagen</h6>
                        <form id="uploadImageForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-2">
                                <input type="file" class="form-control form-control-sm" 
                                       id="imagen" name="imagen" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-upload me-1"></i>Subir Nueva Imagen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas rápidas -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Resumen</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Estado:</span>
                        <span class="badge {{ ($servicio['estado'] ?? '') == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $servicio['estado'] ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>En Oferta:</span>
                        <span class="badge {{ !empty($servicio['precio_oferta']) ? 'bg-danger' : 'bg-secondary' }}">
                            {{ !empty($servicio['precio_oferta']) ? 'Sí' : 'No' }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Última Actualización:</span>
                        <small class="text-muted">
                            {{ isset($servicio['updated_at']) ? \Carbon\Carbon::parse($servicio['updated_at'])->diffForHumans() : 'N/A' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formulario para subir nueva imagen
    const uploadForm = document.getElementById('uploadImageForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const servicioId = '{{ $servicio["id"] ?? "" }}';
            
            if (!servicioId) {
                alert('Error: No se encontró el ID del servicio');
                return;
            }
            
            if (!confirm('¿Subir nueva imagen? La imagen actual será reemplazada.')) {
                return;
            }
            
            // Mostrar loading
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Subiendo...';
            button.disabled = true;
            
            fetch(`/servicios/${servicioId}/subir-imagen`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload(); // Recargar para mostrar la nueva imagen
                } else {
                    alert('Error: ' + data.message);
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al subir la imagen');
                button.innerHTML = originalText;
                button.disabled = false;
            });
        });
    }
});
</script>
@endpush