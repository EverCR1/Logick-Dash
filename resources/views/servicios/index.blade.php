@extends('layouts.app')

@section('title', 'Servicios - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Servicios</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Gestión de Servicios</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('servicios.buscar') }}" class="btn btn-info">
                    <i class="fas fa-search me-2"></i> Buscar
                </a>
                <a href="{{ route('servicios.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nuevo Servicio
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @php
                // Extraer datos de manera segura
                $serviciosData = [];
                $serviciosLinks = [];
                $serviciosMeta = [];
                
                if (isset($servicios['data'])) {
                    $serviciosData = $servicios['data'];
                } elseif (isset($servicios) && is_array($servicios)) {
                    $serviciosData = $servicios;
                }
                
                if (isset($servicios['links']) && is_array($servicios['links'])) {
                    $serviciosLinks = $servicios['links'];
                }
                
                if (isset($servicios['meta']) && is_array($servicios['meta'])) {
                    $serviciosMeta = $servicios['meta'];
                }
            @endphp

            @if(empty($serviciosData))
                <div class="text-center py-5">
                    <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay servicios registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer servicio</p>
                    <a href="{{ route('servicios.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Servicio
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Imagen</th>
                                <th>Código</th>
                                <th>Servicio</th>
                                <th>Inversión</th>
                                <th>Precios</th>
                                <th>Margen</th>
                                <th>Estado</th>
                                <th style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviciosData as $servicio)
                            @php
                                // Obtener imagen
                                $urlImagen = null;
                                if(isset($servicio['imagenes']) && count($servicio['imagenes']) > 0) {
                                    $imagen = $servicio['imagenes'][0];
                                    if(!empty($imagen['url_thumb'])) {
                                        $urlImagen = $imagen['url_thumb'];
                                    } elseif(!empty($imagen['url_medium'])) {
                                        $urlImagen = $imagen['url_medium'];
                                    } elseif(!empty($imagen['url'])) {
                                        $urlImagen = $imagen['url'];
                                    }
                                }
                                
                                // Calcular margen
                                $precio_final = $servicio['precio_oferta'] ?? $servicio['precio_venta'];
                                $inversion = $servicio['inversion_estimada'];
                                $margen = $inversion > 0 ? (($precio_final - $inversion) / $inversion) * 100 : 0;
                                $margenClass = $margen >= 100 ? 'success' : ($margen >= 50 ? 'info' : ($margen >= 20 ? 'warning' : 'danger'));
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if($urlImagen)
                                        <img src="{{ $urlImagen }}" 
                                             alt="{{ $servicio['nombre'] ?? 'Servicio' }}" 
                                             class="img-thumbnail service-list-image"
                                             style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                             onclick="abrirModalImagen('{{ $urlImagen }}', '{{ $servicio['nombre'] ?? 'Servicio' }}')">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-concierge-bell text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $servicio['codigo'] ?? '' }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $servicio['nombre'] ?? '' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($servicio['descripcion'] ?? 'Sin descripción', 80) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        Q{{ number_format($servicio['inversion_estimada'] ?? 0, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small>Venta: 
                                            @if(!empty($servicio['precio_oferta']))
                                                <span class="text-decoration-line-through text-muted">Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}</span>
                                                <strong class="text-danger">Q{{ number_format($servicio['precio_oferta'], 2) }}</strong>
                                            @else
                                                <strong>Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}</strong>
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $margenClass }}">
                                        {{ number_format($margen, 1) }}%
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('servicios.change-status', $servicio['id'] ?? '#') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-{{ ($servicio['estado'] ?? '') == 'activo' ? 'success' : 'secondary' }}">
                                            {{ $servicio['estado'] ?? 'desconocido' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('servicios.show', $servicio['id'] ?? '#') }}" class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('servicios.edit', $servicio['id'] ?? '#') }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('servicios.destroy', $servicio['id'] ?? '#') }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este servicio?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if(!empty($serviciosLinks) && count($serviciosLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($serviciosMeta))
                            Mostrando 
                            {{ $serviciosMeta['from'] ?? 1 }} - 
                            {{ $serviciosMeta['to'] ?? count($serviciosData) }} de 
                            {{ $serviciosMeta['total'] ?? count($serviciosData) }} servicios
                        @else
                            Mostrando {{ count($serviciosData) }} servicios
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($serviciosLinks as $link)
                                @if(is_array($link))
                                    <li class="page-item {{ $link['active'] ?? false ? 'active' : '' }} {{ empty($link['url']) ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $link['url'] ?? '#' }}">
                                            {!! $link['label'] ?? '' !!}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </nav>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Modal para ver imagen en grande -->
<div class="modal fade" id="modalImagen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagenTitulo">Imagen del servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="" id="modalImagenSrc" class="img-fluid" alt="" style="max-height: 70vh; width: auto;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Función para abrir imagen en modal
function abrirModalImagen(src, titulo) {
    const modalElement = document.getElementById('modalImagen');
    const modalSrc = document.getElementById('modalImagenSrc');
    const modalTitle = document.getElementById('modalImagenTitulo');
    
    if (modalElement && modalSrc && modalTitle) {
        modalSrc.src = src;
        modalTitle.textContent = titulo || 'Imagen del servicio';
        
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        console.error('Elementos del modal no encontrados');
    }
}

// Agregar tooltips a los botones de acciones
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Confirmación antes de cambiar estado
    const statusForms = document.querySelectorAll('form[action*="cambiar-estado"]');
    statusForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            const estadoActual = button.textContent.trim();
            const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
            
            if (!confirm(`¿Cambiar estado del servicio de "${estadoActual}" a "${nuevoEstado}"?`)) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.service-list-image {
    transition: all 0.2s ease-in-out;
    border: 1px solid #dee2e6;
}

.service-list-image:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    cursor: pointer;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.table-hover tbody tr:hover .service-list-image {
    border-color: #0d6efd;
}

.badge {
    font-size: 0.85em;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Estilos para botones de estado */
.btn-status {
    min-width: 80px;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .service-list-image {
        width: 50px !important;
        height: 50px !important;
    }
    
    .btn-group .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
}
</style>
@endpush