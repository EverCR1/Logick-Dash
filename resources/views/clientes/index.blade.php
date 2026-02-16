@extends('layouts.app')

@section('title', 'Gestión de Clientes - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Clientes</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>Gestión de Clientes
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('clientes.estadisticas') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar me-2"></i> Estadísticas
                </a>
                <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nuevo Cliente
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filtros y búsqueda en tiempo real -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-filter="todos">
                            Todos
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm filter-btn" data-filter="activo">
                            Activos
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm filter-btn" data-filter="inactivo">
                            Inactivos
                        </button>
                    </div>
                    
                    <div class="btn-group ms-2" role="group">
                        <button type="button" class="btn btn-outline-info btn-sm type-filter" data-type="todos">
                            Todos
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm type-filter" data-type="natural">
                            Natural
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm type-filter" data-type="juridico">
                            Jurídico
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Buscar por nombre, NIT, email o teléfono..."
                               value="{{ $search ?? '' }}">
                    </div>
                </div>
            </div>

            @php
                // Extraer datos de manera segura - Mismo patrón que servicios
                $clientesData = [];
                $clientesLinks = [];
                $clientesMeta = [];
                
                if (isset($clientes['data'])) {
                    $clientesData = $clientes['data'];
                } elseif (isset($clientes) && is_array($clientes)) {
                    $clientesData = $clientes;
                }
                
                if (isset($clientes['links']) && is_array($clientes['links'])) {
                    $clientesLinks = $clientes['links'];
                }
                
                if (isset($clientes['meta']) && is_array($clientes['meta'])) {
                    $clientesMeta = $clientes['meta'];
                }
            @endphp

            @if(empty($clientesData))
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay clientes registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer cliente</p>
                    <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Cliente
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="clientesTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>NIT</th>
                                <th>Contacto</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th style="width: 200px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientesData as $cliente)
                            <tr data-estado="{{ $cliente['estado'] ?? 'activo' }}" 
                                data-tipo="{{ $cliente['tipo'] ?? 'natural' }}"
                                data-search="{{ strtolower(
                                    ($cliente['nombre'] ?? '') . ' ' . 
                                    ($cliente['nit'] ?? '') . ' ' . 
                                    ($cliente['email'] ?? '') . ' ' . 
                                    ($cliente['telefono'] ?? '')
                                ) }}">
                                <td>{{ $cliente['id'] ?? 'N/A' }}</td>
                                <td>
                                    <strong>{{ $cliente['nombre'] ?? 'N/A' }}</strong>
                                    @if(isset($cliente['notas']) && !empty($cliente['notas']))
                                        <small class="text-muted d-block">{{ Str::limit($cliente['notas'], 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $cliente['nit'] ?? 'N/A' }}</td>
                                <td>
                                    @if(isset($cliente['email']) && !empty($cliente['email']))
                                        <div><i class="fas fa-envelope me-1"></i> {{ $cliente['email'] }}</div>
                                    @endif
                                    @if(isset($cliente['telefono']) && !empty($cliente['telefono']))
                                        <div><i class="fas fa-phone me-1"></i> {{ $cliente['telefono'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $tipoClass = ($cliente['tipo'] ?? 'natural') == 'natural' ? 'bg-info' : 'bg-warning';
                                        $tipoIcon = ($cliente['tipo'] ?? 'natural') == 'natural' ? 'user' : 'building';
                                    @endphp
                                    <span class="badge {{ $tipoClass }}">
                                        <i class="fas fa-{{ $tipoIcon }} me-1"></i>
                                        {{ ($cliente['tipo'] ?? 'natural') == 'natural' ? 'Natural' : 'Jurídico' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $estadoClass = ($cliente['estado'] ?? 'activo') == 'activo' ? 'bg-success' : 'bg-secondary';
                                        $estadoIcon = ($cliente['estado'] ?? 'activo') == 'activo' ? 'check-circle' : 'times-circle';
                                    @endphp
                                    <span class="badge {{ $estadoClass }}">
                                        <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                        {{ $cliente['estado'] ?? 'activo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('clientes.show', $cliente['id'] ?? '#') }}" 
                                           class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clientes.edit', $cliente['id'] ?? '#') }}" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(($cliente['estado'] ?? 'activo') == 'activo')
                                        <form action="{{ route('clientes.changeStatus', $cliente['id'] ?? '#') }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de desactivar este cliente?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Desactivar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('clientes.changeStatus', $cliente['id'] ?? '#') }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de activar este cliente?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Activar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('clientes.destroy', $cliente['id'] ?? '#') }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este cliente? Esta acción no se puede deshacer.')">
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

                <!-- Paginación (mantener para navegación) -->
                @if(!empty($clientesLinks) && count($clientesLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($clientesMeta))
                            Mostrando 
                            {{ $clientesMeta['from'] ?? 1 }} - 
                            {{ $clientesMeta['to'] ?? count($clientesData) }} de 
                            {{ $clientesMeta['total'] ?? count($clientesData) }} clientes
                        @else
                            Mostrando {{ count($clientesData) }} clientes
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($clientesLinks as $link)
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables para filtros
    let currentEstadoFilter = 'todos';
    let currentTipoFilter = 'todos';
    
    // Función para aplicar todos los filtros
    function aplicarFiltros() {
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#clientesTable tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const estado = row.dataset.estado;
            const tipo = row.dataset.tipo;
            const searchData = row.dataset.search || '';
            
            // Verificar filtro de estado
            const estadoMatch = currentEstadoFilter === 'todos' || estado === currentEstadoFilter;
            
            // Verificar filtro de tipo
            const tipoMatch = currentTipoFilter === 'todos' || tipo === currentTipoFilter;
            
            // Verificar búsqueda
            const searchMatch = searchText === '' || searchData.includes(searchText);
            
            // Mostrar u ocultar fila
            if (estadoMatch && tipoMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mostrar mensaje si no hay resultados
        mostrarMensajeNoResultados(visibleCount);
    }
    
    // Función para mostrar mensaje cuando no hay resultados
    function mostrarMensajeNoResultados(visibleCount) {
        const table = document.getElementById('clientesTable');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No se encontraron clientes con los filtros aplicados</p>
                        <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                            <i class="fas fa-undo me-2"></i>Limpiar filtros
                        </button>
                    </td>
                `;
                table.querySelector('tbody').appendChild(noResultsRow);
            }
        } else {
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }
    }
    
    // Función para limpiar filtros
    window.limpiarFiltros = function() {
        // Resetear filtros
        currentEstadoFilter = 'todos';
        currentTipoFilter = 'todos';
        
        // Actualizar botones de estado
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Actualizar botones de tipo
        document.querySelectorAll('.type-filter').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.type === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Limpiar búsqueda
        document.getElementById('searchInput').value = '';
        
        // Aplicar filtros
        aplicarFiltros();
    };
    
    // Filtrado por estado
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentEstadoFilter = this.dataset.filter;
            aplicarFiltros();
        });
    });
    
    // Filtrado por tipo
    document.querySelectorAll('.type-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.type-filter').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentTipoFilter = this.dataset.type;
            aplicarFiltros();
        });
    });
    
    // Búsqueda en tiempo real
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(aplicarFiltros, 300); // Debounce de 300ms
    });
    
    // Inicializar botones activos
    document.querySelector('.filter-btn[data-filter="todos"]').classList.add('active');
    document.querySelector('.type-filter[data-type="todos"]').classList.add('active');
    
    // Agregar tooltips a los botones
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

@push('styles')
<style>
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Estilos para botones de filtro activos */
.filter-btn.active, .type-filter.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.filter-btn[data-filter="activo"].active {
    background-color: #198754;
    border-color: #198754;
}

.filter-btn[data-filter="inactivo"].active {
    background-color: #dc3545;
    border-color: #dc3545;
}

.type-filter[data-type="natural"].active {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: #000;
}

.type-filter[data-type="juridico"].active {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .btn-group .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
    
    .row.mb-4 {
        flex-direction: column;
    }
    
    .col-md-6 {
        margin-bottom: 0.5rem;
    }
    
    .btn-group {
        flex-wrap: wrap;
        margin-bottom: 0.25rem;
    }
    
    .ms-2 {
        margin-left: 0 !important;
        margin-top: 0.25rem;
    }
}
</style>
@endpush