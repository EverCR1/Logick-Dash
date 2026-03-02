@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Proveedores</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-truck me-2"></i>Gestión de Proveedores
            </h5>
            <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Proveedor
            </a>
        </div>
        <div class="card-body">

            <!-- Filtros y búsqueda -->
            <div class="row mb-4">
                <div class="col-md-8">
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
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput"
                               placeholder="Buscar por nombre, email o teléfono...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tabla siempre presente en el DOM --}}
            <div id="tabla-container">

                @php
                    $proveedoresData = is_array($proveedores) ? $proveedores : [];
                @endphp

                {{-- Empty state --}}
                <div id="empty-state" class="text-center py-5" style="{{ empty($proveedoresData) ? '' : 'display:none;' }}">
                    <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay proveedores registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer proveedor</p>
                    <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Proveedor
                    </a>
                </div>

                {{-- Tabla --}}
                <div class="table-responsive" id="table-wrapper" style="{{ empty($proveedoresData) ? 'display:none;' : '' }}">
                    <table class="table table-hover table-striped" id="proveedoresTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 60px;">No.</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Estado</th>
                                <th style="width: 200px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proveedoresData as $index => $proveedor)
                            <tr>
                                <td><span class="fw-bold">{{ $index + 1 }}</span></td>
                                <td>
                                    <strong>{{ $proveedor['nombre'] ?? 'N/A' }}</strong>
                                    @if(!empty($proveedor['descripcion']))
                                        <small class="text-muted d-block">{{ Str::limit($proveedor['descripcion'], 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($proveedor['email']))
                                        <a href="mailto:{{ $proveedor['email'] }}">
                                            <i class="fas fa-envelope me-1"></i>{{ $proveedor['email'] }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($proveedor['telefono']))
                                        <i class="fas fa-phone me-1"></i>{{ $proveedor['telefono'] }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($proveedor['direccion']))
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($proveedor['direccion'], 30) }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $estadoClass = ($proveedor['estado'] ?? 'activo') === 'activo' ? 'bg-success' : 'bg-danger';
                                        $estadoIcon  = ($proveedor['estado'] ?? 'activo') === 'activo' ? 'check-circle' : 'times-circle';
                                    @endphp
                                    <span class="badge {{ $estadoClass }}">
                                        <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                        {{ ucfirst($proveedor['estado'] ?? 'activo') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('proveedores.show', $proveedor['id']) }}"
                                           class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('proveedores.edit', $proveedor['id']) }}"
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(($proveedor['estado'] ?? 'activo') === 'activo')
                                        <form action="{{ route('proveedores.changeStatus', $proveedor['id']) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Desactivar este proveedor?')">
                                            @csrf
                                            <input type="hidden" name="estado" value="inactivo">
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Desactivar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('proveedores.changeStatus', $proveedor['id']) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Activar este proveedor?')">
                                            @csrf
                                            <input type="hidden" name="estado" value="activo">
                                            <button type="submit" class="btn btn-sm btn-success" title="Activar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('proveedores.destroy', $proveedor['id']) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Eliminar este proveedor? Esta acción no se puede deshacer.')">
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

                {{-- Contador y paginación --}}
                <div class="d-flex justify-content-between align-items-center mt-3"
                     id="paginacion-container"
                     style="{{ empty($proveedoresData) ? 'display:none;' : '' }}">
                    <div class="text-muted" id="contador-wrap">
                        Mostrando {{ count($proveedoresData) }} proveedores
                    </div>
                    <nav aria-label="Page navigation">
                        <div id="paginacion-wrap"></div>
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

    // ── Referencias DOM ───────────────────────────────────────────────
    const getTabla       = () => document.querySelector('#proveedoresTable tbody');
    const paginacionWrap = document.getElementById('paginacion-wrap');
    const contadorWrap   = document.getElementById('contador-wrap');
    const tableWrapper   = document.getElementById('table-wrapper');
    const paginacionCont = document.getElementById('paginacion-container');
    const emptyState     = document.getElementById('empty-state');

    // ── Estado de filtros ─────────────────────────────────────────────
    let currentEstado = 'todos';
    let currentSearch = '';
    let searchTimeout = null;

    // ── Mostrar / ocultar tabla ───────────────────────────────────────
    function mostrarTabla() {
        if (tableWrapper)   tableWrapper.style.display   = '';
        if (paginacionCont) paginacionCont.style.display = '';
        if (emptyState)     emptyState.style.display     = 'none';
    }

    function mostrarEmptyState(mensaje) {
        if (tableWrapper)   tableWrapper.style.display   = 'none';
        if (paginacionCont) paginacionCont.style.display = 'none';
        if (emptyState) {
            emptyState.style.display = '';
            emptyState.innerHTML = `
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">${mensaje}</h5>
                <p class="text-muted mb-3">Intenta con otros términos o filtros</p>
                <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                    <i class="fas fa-undo me-2"></i>Limpiar filtros
                </button>`;
        }
    }

    // ── Detectar filtros activos ──────────────────────────────────────
    function hayFiltrosActivos() {
        return currentEstado !== 'todos' || currentSearch.trim() !== '';
    }

    // ── Fetch al endpoint /proveedores/filter ─────────────────────────
    function fetchFiltrado(page = 1) {
        if (!hayFiltrosActivos()) {
            window.location.href = "{{ route('proveedores.index') }}";
            return;
        }

        mostrarLoader();

        const params = new URLSearchParams({
            search: currentSearch,
            estado: currentEstado,
            page:   page
        });

        fetch(`{{ route('proveedores.filter') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderTabla(data.proveedores);
            } else {
                mostrarError('Error al filtrar proveedores');
            }
        })
        .catch(() => mostrarError('Error de conexión'));
    }

    // ── Renderizar filas ──────────────────────────────────────────────
    function renderTabla(paginado) {
        const registros   = paginado.data      ?? [];
        const links       = paginado.links     ?? [];
        const currentPage = paginado.current_page ?? 1;
        const perPage     = paginado.per_page  ?? 20;
        const total       = paginado.total     ?? 0;
        const from        = paginado.from      ?? 0;
        const to          = paginado.to        ?? 0;
        const startNumber = (currentPage - 1) * perPage + 1;

        if (registros.length === 0) {
            mostrarEmptyState('No se encontraron proveedores');
            actualizarContador(0, 0, 0);
            actualizarPaginacion([]);
            return;
        }

        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        tbody.innerHTML = '';

        registros.forEach((proveedor, index) => {
            const numero     = startNumber + index;
            const estadoClass = proveedor.estado === 'activo' ? 'bg-success' : 'bg-danger';
            const estadoIcon  = proveedor.estado === 'activo' ? 'check-circle' : 'times-circle';
            const estadoLabel = proveedor.estado === 'activo' ? 'Activo' : 'Inactivo';
            const showUrl    = `/proveedores/${proveedor.id}`;
            const editUrl    = `/proveedores/${proveedor.id}/editar`;
            const statusUrl  = `/proveedores/${proveedor.id}/cambiar-estado`;
            const deleteUrl  = `/proveedores/${proveedor.id}`;

            const btnEstado = proveedor.estado === 'activo'
                ? `<form action="${statusUrl}" method="POST" class="d-inline" onsubmit="return confirm('¿Desactivar este proveedor?')">
                       <input type="hidden" name="_token" value="${csrfToken}">
                       <input type="hidden" name="estado" value="inactivo">
                       <button type="submit" class="btn btn-sm btn-secondary" title="Desactivar">
                           <i class="fas fa-ban"></i>
                       </button>
                   </form>`
                : `<form action="${statusUrl}" method="POST" class="d-inline" onsubmit="return confirm('¿Activar este proveedor?')">
                       <input type="hidden" name="_token" value="${csrfToken}">
                       <input type="hidden" name="estado" value="activo">
                       <button type="submit" class="btn btn-sm btn-success" title="Activar">
                           <i class="fas fa-check"></i>
                       </button>
                   </form>`;

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td><span class="fw-bold">${numero}</span></td>
                    <td>
                        <strong>${proveedor.nombre ?? 'N/A'}</strong>
                        ${proveedor.descripcion ? `<small class="text-muted d-block">${proveedor.descripcion.substring(0, 50)}</small>` : ''}
                    </td>
                    <td>
                        ${proveedor.email
                            ? `<a href="mailto:${proveedor.email}"><i class="fas fa-envelope me-1"></i>${proveedor.email}</a>`
                            : '<span class="text-muted">N/A</span>'}
                    </td>
                    <td>
                        ${proveedor.telefono
                            ? `<i class="fas fa-phone me-1"></i>${proveedor.telefono}`
                            : '<span class="text-muted">N/A</span>'}
                    </td>
                    <td>
                        ${proveedor.direccion
                            ? `<i class="fas fa-map-marker-alt me-1"></i>${proveedor.direccion.substring(0, 30)}`
                            : '<span class="text-muted">N/A</span>'}
                    </td>
                    <td>
                        <span class="badge ${estadoClass}">
                            <i class="fas fa-${estadoIcon} me-1"></i>${estadoLabel}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="${showUrl}" class="btn btn-sm btn-info"    title="Ver detalles"><i class="fas fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                            ${btnEstado}
                            <form action="${deleteUrl}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este proveedor? Esta acción no se puede deshacer.')">
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

    // ── Contador ──────────────────────────────────────────────────────
    function actualizarContador(from, to, total) {
        if (!contadorWrap) return;
        contadorWrap.innerHTML = total > 0
            ? `Mostrando ${from} - ${to} de <strong>${total}</strong> proveedores encontrados`
            : 'Sin resultados';
    }

    // ── Paginación ────────────────────────────────────────────────────
    function actualizarPaginacion(links) {
        if (!paginacionWrap) return;
        if (!links || links.length <= 3) {
            paginacionWrap.innerHTML = '';
            return;
        }
        let html = '<ul class="pagination mb-0">';
        links.forEach(link => {
            const active   = link.active ? 'active'    : '';
            const disabled = !link.url   ? 'disabled'  : '';
            let pageNum = null;
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

    // ── Loader / Error ────────────────────────────────────────────────
    function mostrarLoader() {
        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted mt-2 mb-0">Buscando proveedores...</p>
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

    // ── Cambiar página ────────────────────────────────────────────────
    window.cambiarPagina = function (page) {
        fetchFiltrado(page);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // ── Limpiar filtros ───────────────────────────────────────────────
    window.limpiarFiltros = function () {
        currentEstado = 'todos';
        currentSearch = '';
        document.getElementById('searchInput').value = '';
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.toggle('active', b.dataset.filter === 'todos');
        });
        window.location.href = "{{ route('proveedores.index') }}";
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

    .filter-btn.active {
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

    @media (max-width: 768px) {
        .table-responsive { font-size: 0.9rem; }

        .btn-group .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.8rem;
        }

        .row.mb-4 { flex-direction: column; }

        .col-md-4, .col-md-8 {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .btn-group { flex-wrap: wrap; }
    }
</style>
@endpush