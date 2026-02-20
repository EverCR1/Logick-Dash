@extends('layouts.app')

@section('title', 'Gestión de Clientes')

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
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <i class="fas fa-info-circle"></i> Búsqueda en nombre, NIT, email y teléfono
                    </small>
                </div>
            </div>

            @php
                // Extraer datos de manera segura
                $clientesData = [];
                $clientesLinks = [];
                $clientesMeta = [];
                
                if (isset($clientes) && is_array($clientes)) {
                    $clientesData = $clientes['data'] ?? [];
                    $clientesLinks = $clientes['links'] ?? [];
                    $clientesMeta = [
                        'current_page' => $clientes['current_page'] ?? 1,
                        'per_page' => $clientes['per_page'] ?? 20,
                        'total' => $clientes['total'] ?? 0,
                        'from' => $clientes['from'] ?? 1,
                        'to' => $clientes['to'] ?? 0
                    ];
                }
                
                // Calcular el número inicial para la paginación
                $currentPage = $clientesMeta['current_page'] ?? 1;
                $perPage = $clientesMeta['per_page'] ?? 20;
                $startNumber = ($currentPage - 1) * $perPage + 1;
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
                                <th style="width: 60px;">No.</th>
                                <th>Nombre</th>
                                <th>NIT</th>
                                <th>Contacto</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th style="width: 200px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientesData as $index => $cliente)
                            @php
                                // Preparar datos para búsqueda en minúsculas
                                $nombreBusqueda = strtolower($cliente['nombre'] ?? '');
                                $nitBusqueda = strtolower($cliente['nit'] ?? '');
                                $emailBusqueda = strtolower($cliente['email'] ?? '');
                                $telefonoBusqueda = strtolower($cliente['telefono'] ?? '');
                                $notasBusqueda = strtolower($cliente['notas'] ?? '');
                            @endphp
                            <tr data-estado="{{ $cliente['estado'] ?? 'activo' }}" 
                                data-tipo="{{ $cliente['tipo'] ?? 'natural' }}"
                                data-nombre="{{ $nombreBusqueda }}"
                                data-nit="{{ $nitBusqueda }}"
                                data-email="{{ $emailBusqueda }}"
                                data-telefono="{{ $telefonoBusqueda }}"
                                data-notas="{{ $notasBusqueda }}">
                                <td>
                                    <span class="fw-bold">{{ $startNumber + $index }}</span>
                                </td>
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

                <!-- Paginación -->
                @if(!empty($clientesLinks) && count($clientesLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($clientesMeta))
                            Mostrando 
                            {{ $clientesMeta['from'] ?? $startNumber }} - 
                            {{ $clientesMeta['to'] ?? ($startNumber + count($clientesData) - 1) }} de 
                            {{ $clientesMeta['total'] ?? count($clientesData) }} clientes
                        @else
                            Mostrando {{ count($clientesData) }} clientes
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($clientesLinks as $link)
                                @if(is_array($link))
                                    <li class="page-item {{ ($link['active'] ?? false) ? 'active' : '' }} {{ empty($link['url']) ? 'disabled' : '' }}">
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
    let currentSearch = '';
    
    // Función para aplicar todos los filtros
    function aplicarFiltros() {
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
        currentSearch = searchText;
        
        const tbody = document.querySelector('#clientesTable tbody');
        if (!tbody) return;
        
        let rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Excluir fila de no resultados si existe
        rows = rows.filter(row => row.id !== 'no-results-row');
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            // Obtener datos específicos para búsqueda desde data-* attributes
            const nombre = row.getAttribute('data-nombre') || '';
            const nit = row.getAttribute('data-nit') || '';
            const email = row.getAttribute('data-email') || '';
            const telefono = row.getAttribute('data-telefono') || '';
            const notas = row.getAttribute('data-notas') || '';
            
            const estado = row.getAttribute('data-estado');
            const tipo = row.getAttribute('data-tipo');
            
            // Filtro de búsqueda en múltiples campos
            let searchMatch = true;
            if (searchText !== '') {
                searchMatch = nombre.includes(searchText) || 
                            nit.includes(searchText) || 
                            email.includes(searchText) || 
                            telefono.includes(searchText) || 
                            notas.includes(searchText);
            }
            
            // Filtro de estado
            const estadoMatch = currentEstadoFilter === 'todos' || estado === currentEstadoFilter;
            
            // Filtro de tipo
            const tipoMatch = currentTipoFilter === 'todos' || tipo === currentTipoFilter;
            
            // Mostrar u ocultar fila
            if (estadoMatch && tipoMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Mostrar mensaje si no hay resultados
        mostrarMensajeNoResultados(visibleCount, rows.length);
    }
    
    // Función para mostrar mensaje cuando no hay resultados
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const table = document.getElementById('clientesTable');
        const tbody = table.querySelector('tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron clientes</h5>
                        <p class="text-muted mb-3">Intenta con otros términos de búsqueda o filtros</p>
                        <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                            <i class="fas fa-undo me-2"></i>Limpiar filtros
                        </button>
                    </td>
                `;
                tbody.appendChild(noResultsRow);
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
    
    // Búsqueda en tiempo real con debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(aplicarFiltros, 300);
    });
    
    // Botón limpiar búsqueda
    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        aplicarFiltros();
        document.getElementById('searchInput').focus();
    });
    
    // Inicializar botones activos según los filtros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const estadoParam = urlParams.get('estado') || 'todos';
    const tipoParam = urlParams.get('tipo') || 'todos';
    
    // Activar botón de estado correspondiente
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if (btn.dataset.filter === estadoParam) {
            btn.classList.add('active');
        } else if (estadoParam === 'todos' && btn.dataset.filter === 'todos') {
            btn.classList.add('active');
        }
    });
    
    // Activar botón de tipo correspondiente
    document.querySelectorAll('.type-filter').forEach(btn => {
        if (btn.dataset.type === tipoParam) {
            btn.classList.add('active');
        } else if (tipoParam === 'todos' && btn.dataset.type === 'todos') {
            btn.classList.add('active');
        }
    });
    
    currentEstadoFilter = estadoParam;
    currentTipoFilter = tipoParam;
    
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
    /* Estilo para el número correlativo */
    th:first-child, td:first-child {
        font-weight: 500;
        color: #495057;
    }
    
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