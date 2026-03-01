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
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">
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
                        <button type="button" class="btn btn-outline-info btn-sm type-filter active" data-type="todos">
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
                $clientesData  = [];
                $clientesLinks = [];
                $clientesMeta  = [];
                
                if (isset($clientes) && is_array($clientes)) {
                    $clientesData  = $clientes['data'] ?? [];
                    $clientesLinks = $clientes['links'] ?? [];
                    $clientesMeta  = [
                        'current_page' => $clientes['current_page'] ?? 1,
                        'per_page'     => $clientes['per_page'] ?? 20,
                        'total'        => $clientes['total'] ?? 0,
                        'from'         => $clientes['from'] ?? 1,
                        'to'           => $clientes['to'] ?? 0
                    ];
                }
                
                $currentPage = $clientesMeta['current_page'] ?? 1;
                $perPage     = $clientesMeta['per_page'] ?? 20;
                $startNumber = ($currentPage - 1) * $perPage + 1;
            @endphp

            {{-- Contenedor principal de la tabla (siempre presente en el DOM) --}}
            <div id="tabla-container">
                @if(empty($clientesData))
                    <div class="text-center py-5" id="empty-state">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay clientes registrados</h5>
                        <p class="text-muted">Comienza agregando tu primer cliente</p>
                        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Crear Primer Cliente
                        </a>
                    </div>
                @endif

                <div class="table-responsive" id="table-wrapper" style="{{ empty($clientesData) ? 'display:none;' : '' }}">
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
                            <tr>
                                <td><span class="fw-bold">{{ $startNumber + $index }}</span></td>
                                <td>
                                    <strong>{{ $cliente['nombre'] ?? 'N/A' }}</strong>
                                    @if(!empty($cliente['notas']))
                                        <small class="text-muted d-block">{{ Str::limit($cliente['notas'], 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $cliente['nit'] ?? 'N/A' }}</td>
                                <td>
                                    @if(!empty($cliente['email']))
                                        <div><i class="fas fa-envelope me-1"></i> {{ $cliente['email'] }}</div>
                                    @endif
                                    @if(!empty($cliente['telefono']))
                                        <div><i class="fas fa-phone me-1"></i> {{ $cliente['telefono'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $tipoClass = ($cliente['tipo'] ?? 'natural') === 'natural' ? 'bg-info' : 'bg-warning';
                                        $tipoIcon  = ($cliente['tipo'] ?? 'natural') === 'natural' ? 'user' : 'building';
                                        $tipoLabel = ($cliente['tipo'] ?? 'natural') === 'natural' ? 'Natural' : 'Jurídico';
                                    @endphp
                                    <span class="badge {{ $tipoClass }}">
                                        <i class="fas fa-{{ $tipoIcon }} me-1"></i>{{ $tipoLabel }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $estadoClass = ($cliente['estado'] ?? 'activo') === 'activo' ? 'bg-success' : 'bg-secondary';
                                        $estadoIcon  = ($cliente['estado'] ?? 'activo') === 'activo' ? 'check-circle' : 'times-circle';
                                    @endphp
                                    <span class="badge {{ $estadoClass }}">
                                        <i class="fas fa-{{ $estadoIcon }} me-1"></i>{{ $cliente['estado'] ?? 'activo' }}
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
                                        @if(($cliente['estado'] ?? 'activo') === 'activo')
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
                                              onsubmit="return confirm('¿Estás seguro de eliminar este cliente?')">
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
                <div class="d-flex justify-content-between align-items-center mt-3" 
                     id="paginacion-container" 
                     style="{{ empty($clientesData) ? 'display:none;' : '' }}">
                    <div class="text-muted" id="contador-wrap">
                        @if(!empty($clientesMeta) && ($clientesMeta['total'] ?? 0) > 0)
                            Mostrando 
                            {{ $clientesMeta['from'] ?? $startNumber }} - 
                            {{ $clientesMeta['to'] ?? ($startNumber + count($clientesData) - 1) }} de 
                            {{ $clientesMeta['total'] ?? count($clientesData) }} clientes
                        @else
                            Mostrando {{ count($clientesData) }} clientes
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <div id="paginacion-wrap">
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
                        </div>
                    </nav>
                </div>
            </div>{{-- fin #tabla-container --}}

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Referencias al DOM ────────────────────────────────────────────
    // Se usan funciones getter para que siempre busquen el elemento actual
    const getTabla         = () => document.querySelector('#clientesTable tbody');
    const paginacionWrap   = document.getElementById('paginacion-wrap');
    const contadorWrap     = document.getElementById('contador-wrap');
    const tableWrapper     = document.getElementById('table-wrapper');
    const paginacionCont   = document.getElementById('paginacion-container');
    const emptyState       = document.getElementById('empty-state');

    // ── Estado de filtros ──────────────────────────────────────────────
    let currentEstado = 'todos';
    let currentTipo   = 'todos';
    let currentSearch = '';
    let searchTimeout = null;
    let isFiltering   = false;

    // ── Detectar si hay filtros activos ───────────────────────────────
    function hayFiltrosActivos() {
        return currentEstado !== 'todos' || currentTipo !== 'todos' || currentSearch.trim() !== '';
    }

    // ── Mostrar/ocultar tabla y empty state ───────────────────────────
    function mostrarTabla() {
        if (tableWrapper)   tableWrapper.style.display   = '';
        if (paginacionCont) paginacionCont.style.display = '';
        if (emptyState)     emptyState.style.display     = 'none';
    }

    function mostrarEmptyState(mensaje = null) {
        if (tableWrapper)   tableWrapper.style.display   = 'none';
        if (paginacionCont) paginacionCont.style.display = 'none';
        if (emptyState) {
            emptyState.style.display = '';
            if (mensaje) {
                emptyState.innerHTML = `
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">${mensaje}</h5>
                    <p class="text-muted mb-3">Intenta con otros términos o filtros</p>
                    <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                        <i class="fas fa-undo me-2"></i>Limpiar filtros
                    </button>`;
            }
        }
    }

    // ── Llamar al endpoint /clientes/filter ───────────────────────────
    function fetchFiltrado(page = 1) {
        if (!hayFiltrosActivos()) {
            window.location.href = "{{ route('clientes.index') }}";
            return;
        }

        isFiltering = true;
        mostrarLoader();

        const params = new URLSearchParams({
            search: currentSearch,
            estado: currentEstado,
            tipo:   currentTipo,
            page:   page
        });

        fetch(`{{ route('clientes.filter') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderTabla(data.clientes);
            } else {
                mostrarError('Error al filtrar clientes');
            }
        })
        .catch(() => mostrarError('Error de conexión'))
        .finally(() => { isFiltering = false; });
    }

    // ── Renderizar filas de la tabla ──────────────────────────────────
    function renderTabla(paginado) {
        const registros   = paginado.data ?? [];
        const links       = paginado.links ?? [];
        const currentPage = paginado.current_page ?? 1;
        const perPage     = paginado.per_page ?? 20;
        const total       = paginado.total ?? 0;
        const from        = paginado.from ?? 0;
        const to          = paginado.to ?? 0;
        const startNumber = (currentPage - 1) * perPage + 1;

        if (registros.length === 0) {
            mostrarEmptyState('No se encontraron clientes');
            actualizarContador(0, 0, 0);
            actualizarPaginacion([]);
            return;
        }

        // Asegurar que la tabla esté visible
        mostrarTabla();

        const tbody = getTabla();
        if (!tbody) return;

        tbody.innerHTML = '';

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        registros.forEach((cliente, index) => {
            const numero      = startNumber + index;
            const tipoClass   = cliente.tipo === 'natural' ? 'bg-info' : 'bg-warning';
            const tipoIcon    = cliente.tipo === 'natural' ? 'user' : 'building';
            const tipoLabel   = cliente.tipo === 'natural' ? 'Natural' : 'Jurídico';
            const estadoClass = cliente.estado === 'activo' ? 'bg-success' : 'bg-secondary';
            const estadoIcon  = cliente.estado === 'activo' ? 'check-circle' : 'times-circle';
            const showUrl     = `/clientes/${cliente.id}`;
            const editUrl     = `/clientes/${cliente.id}/editar`;
            const statusUrl   = `/clientes/${cliente.id}/cambiar-estado`;
            const deleteUrl   = `/clientes/${cliente.id}`;

            const btnEstado = cliente.estado === 'activo'
                ? `<form action="${statusUrl}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar este cliente?')">
                       <input type="hidden" name="_token" value="${csrfToken}">
                       <button type="submit" class="btn btn-sm btn-secondary" title="Desactivar">
                           <i class="fas fa-ban"></i>
                       </button>
                   </form>`
                : `<form action="${statusUrl}" method="POST" class="d-inline" onsubmit="return confirm('¿Activar este cliente?')">
                       <input type="hidden" name="_token" value="${csrfToken}">
                       <button type="submit" class="btn btn-sm btn-success" title="Activar">
                           <i class="fas fa-check"></i>
                       </button>
                   </form>`;

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td><span class="fw-bold">${numero}</span></td>
                    <td>
                        <strong>${cliente.nombre ?? 'N/A'}</strong>
                        ${cliente.notas ? `<small class="text-muted d-block">${cliente.notas.substring(0, 50)}</small>` : ''}
                    </td>
                    <td>${cliente.nit ?? 'N/A'}</td>
                    <td>
                        ${cliente.email    ? `<div><i class="fas fa-envelope me-1"></i>${cliente.email}</div>`   : ''}
                        ${cliente.telefono ? `<div><i class="fas fa-phone me-1"></i>${cliente.telefono}</div>`   : ''}
                    </td>
                    <td><span class="badge ${tipoClass}"><i class="fas fa-${tipoIcon} me-1"></i>${tipoLabel}</span></td>
                    <td><span class="badge ${estadoClass}"><i class="fas fa-${estadoIcon} me-1"></i>${cliente.estado}</span></td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="${showUrl}" class="btn btn-sm btn-info"    title="Ver detalles"><i class="fas fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                            ${btnEstado}
                            <form action="${deleteUrl}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este cliente? Esta acción no se puede deshacer.')">
                                <input type="hidden" name="_token"  value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>`);
        });

        actualizarContador(from, to, total);
        actualizarPaginacion(links);
    }

    // ── Actualizar contador ───────────────────────────────────────────
    function actualizarContador(from, to, total) {
        if (!contadorWrap) return;
        contadorWrap.innerHTML = total > 0
            ? `Mostrando ${from} - ${to} de ${total} clientes`
            : 'Sin resultados';
    }

    // ── Renderizar paginación ─────────────────────────────────────────
    function actualizarPaginacion(links) {
        if (!paginacionWrap) return;

        // Si solo hay 3 links (prev, pág1, next) no hay necesidad de mostrar paginación
        if (!links || links.length <= 3) {
            paginacionWrap.innerHTML = '';
            return;
        }

        let html = '<ul class="pagination mb-0">';
        links.forEach(link => {
            const active   = link.active ? 'active' : '';
            const disabled = !link.url   ? 'disabled' : '';
            let pageNum    = null;

            if (link.url) {
                const match = link.url.match(/[?&]page=(\d+)/);
                pageNum = match ? match[1] : null;
            }

            const clickHandler = pageNum
                ? `onclick="cambiarPagina(${pageNum}); return false;"`
                : '';

            html += `<li class="page-item ${active} ${disabled}">
                        <a class="page-link" href="#" ${clickHandler}>${link.label}</a>
                     </li>`;
        });
        html += '</ul>';
        paginacionWrap.innerHTML = html;
    }

    // ── Loader mientras carga ─────────────────────────────────────────
    function mostrarLoader() {
        // Mostrar tabla si estaba oculta para poder poner el loader
        mostrarTabla();

        const tbody = getTabla();
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted mt-2 mb-0">Buscando clientes...</p>
                </td>
            </tr>`;
    }

    function mostrarError(msg) {
        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;

        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>${msg}
                </td>
            </tr>`;
    }

    // ── Función pública para cambiar página ───────────────────────────
    window.cambiarPagina = function (page) {
        fetchFiltrado(page);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // ── Limpiar filtros ───────────────────────────────────────────────
    window.limpiarFiltros = function () {
        currentEstado = 'todos';
        currentTipo   = 'todos';
        currentSearch = '';
        document.getElementById('searchInput').value = '';

        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.toggle('active', b.dataset.filter === 'todos');
        });
        document.querySelectorAll('.type-filter').forEach(b => {
            b.classList.toggle('active', b.dataset.type === 'todos');
        });

        window.location.href = "{{ route('clientes.index') }}";
    };

    // ── Event listeners ───────────────────────────────────────────────
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentEstado = this.dataset.filter;
            fetchFiltrado();
        });
    });

    document.querySelectorAll('.type-filter').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.type-filter').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentTipo = this.dataset.type;
            fetchFiltrado();
        });
    });

    document.getElementById('searchInput').addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        currentSearch = this.value;
        searchTimeout = setTimeout(() => fetchFiltrado(), 350);
    });

    document.getElementById('clearSearch').addEventListener('click', function () {
        document.getElementById('searchInput').value = '';
        currentSearch = '';
        fetchFiltrado();
        document.getElementById('searchInput').focus();
    });

    // ── Activar botones según URL al cargar ───────────────────────────
    const urlParams   = new URLSearchParams(window.location.search);
    const estadoParam = urlParams.get('estado') || 'todos';
    const tipoParam   = urlParams.get('tipo')   || 'todos';

    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.filter === estadoParam);
    });
    document.querySelectorAll('.type-filter').forEach(b => {
        b.classList.toggle('active', b.dataset.type === tipoParam);
    });

    currentEstado = estadoParam;
    currentTipo   = tipoParam;
});
</script>
@endpush

@push('styles')
<style>
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

    /* Botones de filtro activos */
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

    /* Responsivo */
    @media (max-width: 768px) {
        .table-responsive { font-size: 0.9rem; }

        .btn-group .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.8rem;
        }

        .row.mb-4 { flex-direction: column; }

        .col-md-6 { margin-bottom: 0.5rem; }

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