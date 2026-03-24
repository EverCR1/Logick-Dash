@extends('layouts.app')

@section('title', 'Servicios')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Servicios</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-concierge-bell me-2"></i>Gestión de Servicios
            </h5>
            <a href="{{ route('servicios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Servicio
            </a>
        </div>

        <div class="card-body">

            {{-- Fila 1: Estado, Margen, Ordenamiento, Precio --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap gap-2 align-items-center">

                        {{-- Filtro estado --}}
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">Todos</button>
                            <button class="btn btn-outline-success  btn-sm filter-btn" data-filter="activo">Activos</button>
                            <button class="btn btn-outline-danger   btn-sm filter-btn" data-filter="inactivo">Inactivos</button>
                        </div>

                        {{-- Filtro margen --}}
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-info    btn-sm filter-margen-btn active" data-margen="todos">Todo margen</button>
                            <button class="btn btn-outline-success  btn-sm filter-margen-btn" data-margen="alto">Alto (&gt;100%)</button>
                            <button class="btn btn-outline-primary  btn-sm filter-margen-btn" data-margen="medio">Medio (50-100%)</button>
                            <button class="btn btn-outline-warning  btn-sm filter-margen-btn" data-margen="bajo">Bajo (20-50%)</button>
                            <button class="btn btn-outline-danger   btn-sm filter-margen-btn" data-margen="minimo">Mínimo (&lt;20%)</button>
                        </div>

                        {{-- Ordenamiento --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="sortDropdownBtn" data-bs-toggle="dropdown">
                                <i class="fas fa-sort me-1"></i><span id="sortLabel">Nombre A-Z</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item sort-option active" href="#" data-sort="nombre_asc">Nombre A-Z</a></li>
                                <li><a class="dropdown-item sort-option"        href="#" data-sort="nombre_desc">Nombre Z-A</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="precio_asc">Precio menor a mayor</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="precio_desc">Precio mayor a menor</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="inversion_asc">Inversión menor a mayor</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="inversion_desc">Inversión mayor a menor</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="margen_asc">Margen menor a mayor</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="margen_desc">Margen mayor a menor</a></li>
                            </ul>
                        </div>

                        {{-- Rango de precio --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="precioDropdownBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <i class="fas fa-chart-line me-1"></i> Precio
                            </button>
                            <div class="dropdown-menu p-3" style="min-width:260px;">
                                <div class="mb-2">
                                    <label class="form-label form-label-sm">Precio mínimo</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" class="form-control" id="precioMin" min="0" step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label form-label-sm">Precio máximo</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" class="form-control" id="precioMax" min="0" step="0.01" placeholder="9999.99">
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary w-100" id="btnAplicarPrecio">
                                    <i class="fas fa-filter me-1"></i>Aplicar rango
                                </button>
                            </div>
                        </div>

                        {{-- Limpiar --}}
                        <button class="btn btn-sm btn-outline-danger" id="btnLimpiarFiltros">
                            <i class="fas fa-undo me-1"></i> Limpiar filtros
                        </button>

                    </div>
                </div>
            </div>

            {{-- Fila 2: Búsqueda --}}
            <div class="row mb-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput"
                               placeholder="Buscar por código, nombre o descripción...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Datos iniciales PHP --}}
            @php
                $serviciosData  = $servicios['data']  ?? (is_array($servicios) ? $servicios : []);
                $serviciosLinks = $servicios['links'] ?? [];
                $serviciosMeta  = [
                    'total' => $servicios['total'] ?? 0,
                    'from'  => $servicios['from']  ?? 0,
                    'to'    => $servicios['to']    ?? 0,
                ];
            @endphp

            {{-- Tabla siempre en el DOM --}}
            <div id="tabla-container">

                <div id="empty-state" class="text-center py-5"
                     style="{{ empty($serviciosData) ? '' : 'display:none;' }}">
                    <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay servicios registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer servicio</p>
                    <a href="{{ route('servicios.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Servicio
                    </a>
                </div>

                <div class="table-responsive" id="table-wrapper"
                     style="{{ empty($serviciosData) ? 'display:none;' : '' }}">
                    <table class="table table-hover table-striped" id="serviciosTable">
                        <thead class="table-head-dark">
                            <tr>
                                <th style="width:70px;">Imagen</th>
                                <th>Código</th>
                                <th>Servicio</th>
                                <th>Inversión</th>
                                <th>Precio</th>
                                <th>Margen</th>
                                <th>Estado</th>
                                <th style="width:150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviciosData as $servicio)
                            @php
                                $urlImagen = null;
                                if (!empty($servicio['imagenes'])) {
                                    $img = $servicio['imagenes'][0];
                                    $urlImagen = $img['url_thumb'] ?? $img['url_medium'] ?? $img['url'] ?? null;
                                }
                                $precioFinal = $servicio['precio_oferta'] ?? $servicio['precio_venta'];
                                $inversion   = $servicio['inversion_estimada'] ?? 0;
                                $margen      = $inversion > 0 ? (($precioFinal - $inversion) / $inversion) * 100 : 0;
                                $margenClass = $margen >= 100 ? 'success' : ($margen >= 50 ? 'info' : ($margen >= 20 ? 'warning' : 'danger'));
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if($urlImagen)
                                        <img src="{{ $urlImagen }}" alt="{{ $servicio['nombre'] ?? '' }}"
                                             class="img-thumbnail service-list-image"
                                             style="width:60px;height:60px;object-fit:cover;cursor:pointer;"
                                             onclick="abrirModalImagen('{{ $urlImagen }}','{{ $servicio['nombre'] ?? '' }}')">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                             style="width:60px;height:60px;">
                                            <i class="fas fa-concierge-bell text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td><strong>{{ $servicio['codigo'] ?? '' }}</strong></td>
                                <td>
                                    <strong>{{ $servicio['nombre'] ?? '' }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($servicio['descripcion'] ?? 'Sin descripción', 80) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        Q{{ number_format($inversion, 2) }}
                                    </span>
                                </td>
                                <td>
                                    @if(!empty($servicio['precio_oferta']))
                                        <small><span class="text-decoration-line-through text-muted">Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}</span></small><br>
                                        <strong class="text-danger">Q{{ number_format($servicio['precio_oferta'], 2) }}</strong>
                                    @else
                                        <strong>Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}</strong>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $margenClass }}">
                                        <i class="fas fa-chart-line me-1"></i>{{ number_format($margen, 1) }}%
                                    </span>
                                    <small class="d-block text-muted">Ganancia: Q{{ number_format($precioFinal - $inversion, 2) }}</small>
                                </td>
                                <td>
                                    <form action="{{ route('servicios.change-status', $servicio['id'] ?? '#') }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Cambiar el estado de este servicio?')">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm btn-{{ ($servicio['estado'] ?? '') === 'activo' ? 'success' : 'secondary' }}">
                                            <i class="fas fa-{{ ($servicio['estado'] ?? '') === 'activo' ? 'check-circle' : 'times-circle' }} me-1"></i>
                                            {{ ucfirst($servicio['estado'] ?? 'desconocido') }}
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
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar este servicio? Esta acción no se puede deshacer.')">
                                            @csrf @method('DELETE')
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
                     style="{{ empty($serviciosData) ? 'display:none;' : '' }}">
                    <div class="text-muted" id="contador-wrap">
                        @if(($serviciosMeta['total'] ?? 0) > 0)
                            Mostrando {{ $serviciosMeta['from'] }} - {{ $serviciosMeta['to'] }} de
                            <strong>{{ $serviciosMeta['total'] }}</strong> servicios
                        @else
                            Mostrando {{ count($serviciosData) }} servicios
                        @endif
                    </div>
                    <nav>
                        <div id="paginacion-wrap">
                            <ul class="pagination mb-0">
                                @foreach($serviciosLinks as $link)
                                    @if(is_array($link))
                                    <li class="page-item {{ ($link['active'] ?? false) ? 'active' : '' }} {{ empty($link['url']) ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $link['url'] ?? '#' }}">{!! $link['label'] ?? '' !!}</a>
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

{{-- Modal imagen --}}
<div class="modal fade" id="modalImagen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagenTitulo">Imagen del servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="" id="modalImagenSrc" class="img-fluid" style="max-height:70vh;width:auto;">
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
document.addEventListener('DOMContentLoaded', function () {

    // Estado actual de todos los filtros activos
    let estado      = 'todos';
    let margen      = 'todos';
    let sort        = 'nombre_asc';
    let search      = '';
    let precioMin   = '';
    let precioMax   = '';
    let searchTimeout = null;

    // Referencias al DOM reutilizadas en múltiples funciones
    const getTabla       = () => document.querySelector('#serviciosTable tbody');
    const paginacionWrap = document.getElementById('paginacion-wrap');
    const contadorWrap   = document.getElementById('contador-wrap');
    const tableWrapper   = document.getElementById('table-wrapper');
    const paginacionCont = document.getElementById('paginacion-container');
    const emptyState     = document.getElementById('empty-state');

    // Muestra la tabla y oculta el estado vacío
    function mostrarTabla() {
        if (tableWrapper)   tableWrapper.style.display   = '';
        if (paginacionCont) paginacionCont.style.display = '';
        if (emptyState)     emptyState.style.display     = 'none';
    }

    // Muestra mensaje de sin resultados con botón de limpiar
    function mostrarEmptyState(msg) {
        if (tableWrapper)   tableWrapper.style.display   = 'none';
        if (paginacionCont) paginacionCont.style.display = 'none';
        if (emptyState) {
            emptyState.style.display = '';
            emptyState.innerHTML = `
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">${msg}</h5>
                <p class="text-muted mb-3">Intenta con otros términos o filtros</p>
                <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                    <i class="fas fa-undo me-2"></i>Limpiar filtros
                </button>`;
        }
    }

    // Envía la petición AJAX con todos los filtros activos al endpoint de filtrado
    function fetchFiltrado(page = 1) {
        mostrarLoader();

        const params = new URLSearchParams({ estado, margen, sort, search, page,
            precio_min: precioMin, precio_max: precioMax });

        fetch(`{{ route('servicios.filter') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) renderTabla(data.servicios);
            else mostrarError('Error al filtrar servicios');
        })
        .catch(() => mostrarError('Error de conexión'));
    }

    // Construye la miniatura de imagen para cada fila
    function buildImagen(imagenes, nombre) {
        if (!imagenes || imagenes.length === 0) {
            return `<div class="bg-light d-flex align-items-center justify-content-center rounded" style="width:60px;height:60px;">
                        <i class="fas fa-concierge-bell text-muted"></i>
                    </div>`;
        }
        const img = imagenes[0];
        const url = img.url_thumb ?? img.url_medium ?? img.url ?? '';
        return url
            ? `<img src="${url}" alt="${nombre}" class="img-thumbnail service-list-image"
                    style="width:60px;height:60px;object-fit:cover;cursor:pointer;"
                    onclick="abrirModalImagen('${url}','${nombre}')">`
            : `<div class="bg-light d-flex align-items-center justify-content-center rounded" style="width:60px;height:60px;">
                   <i class="fas fa-concierge-bell text-muted"></i>
               </div>`;
    }

    // Transforma los datos paginados de la API en filas HTML e inyecta en la tabla
    function renderTabla(paginado) {
        const registros = paginado.data  ?? [];
        const links     = paginado.links ?? [];
        const total     = paginado.total ?? 0;
        const from      = paginado.from  ?? 0;
        const to        = paginado.to    ?? 0;

        if (registros.length === 0) {
            mostrarEmptyState('No se encontraron servicios');
            actualizarContador(0, 0, 0);
            actualizarPaginacion([]);
            return;
        }

        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;

        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const fmt  = val => parseFloat(val ?? 0).toLocaleString('es-GT', { minimumFractionDigits: 2 });

        tbody.innerHTML = '';

        registros.forEach(s => {
            const precioFinal  = parseFloat(s.precio_oferta ?? s.precio_venta ?? 0);
            const inversion    = parseFloat(s.inversion_estimada ?? 0);
            const margenVal    = inversion > 0 ? ((precioFinal - inversion) / inversion) * 100 : 0;
            const margenClass  = margenVal >= 100 ? 'success' : (margenVal >= 50 ? 'info' : (margenVal >= 20 ? 'warning' : 'danger'));
            const activo       = (s.estado ?? '') === 'activo';

            const precioHtml = s.precio_oferta
                ? `<small><span class="text-decoration-line-through text-muted">Q${fmt(s.precio_venta)}</span></small><br>
                   <strong class="text-danger">Q${fmt(s.precio_oferta)}</strong>`
                : `<strong>Q${fmt(s.precio_venta)}</strong>`;

            const statusUrl = `/servicios/${s.id}/cambiar-estado`;
            const showUrl   = `/servicios/${s.id}`;
            const editUrl   = `/servicios/${s.id}/editar`;
            const deleteUrl = `/servicios/${s.id}`;

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="text-center">${buildImagen(s.imagenes, s.nombre ?? '')}</td>
                    <td><strong>${s.codigo ?? ''}</strong></td>
                    <td>
                        <strong>${s.nombre ?? ''}</strong><br>
                        <small class="text-muted">${(s.descripcion ?? 'Sin descripción').substring(0, 80)}</small>
                    </td>
                    <td><span class="badge bg-light text-dark">Q${fmt(inversion)}</span></td>
                    <td>${precioHtml}</td>
                    <td>
                        <span class="badge bg-${margenClass}">
                            <i class="fas fa-chart-line me-1"></i>${margenVal.toFixed(1)}%
                        </span>
                        <small class="d-block text-muted">Ganancia: Q${fmt(precioFinal - inversion)}</small>
                    </td>
                    <td>
                        <form action="${statusUrl}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Cambiar el estado de este servicio?')">
                            <input type="hidden" name="_token" value="${csrf}">
                            <button type="submit" class="btn btn-sm btn-${activo ? 'success' : 'secondary'}">
                                <i class="fas fa-${activo ? 'check-circle' : 'times-circle'} me-1"></i>
                                ${activo ? 'Activo' : 'Inactivo'}
                            </button>
                        </form>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="${showUrl}"  class="btn btn-sm btn-info"    title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="${editUrl}"  class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                            <form action="${deleteUrl}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este servicio? Esta acción no se puede deshacer.')">
                                <input type="hidden" name="_token"  value="${csrf}">
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

    // Actualiza el texto del contador de resultados
    function actualizarContador(from, to, total) {
        if (!contadorWrap) return;
        contadorWrap.innerHTML = total > 0
            ? `Mostrando ${from} - ${to} de <strong>${total}</strong> servicios encontrados`
            : 'Sin resultados';
    }

    // Renderiza los botones de paginación según los links devueltos por la API
    function actualizarPaginacion(links) {
        if (!paginacionWrap) return;
        if (!links || links.length <= 3) { paginacionWrap.innerHTML = ''; return; }
        let html = '<ul class="pagination mb-0">';
        links.forEach(link => {
            const active   = link.active ? 'active'   : '';
            const disabled = !link.url   ? 'disabled' : '';
            let pageNum = null;
            if (link.url) {
                const m = link.url.match(/[?&]page=(\d+)/);
                pageNum = m ? m[1] : null;
            }
            const click = pageNum ? `onclick="cambiarPagina(${pageNum}); return false;"` : '';
            html += `<li class="page-item ${active} ${disabled}">
                        <a class="page-link" href="#" ${click}>${link.label}</a>
                     </li>`;
        });
        html += '</ul>';
        paginacionWrap.innerHTML = html;
    }

    function mostrarLoader() {
        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;
        tbody.innerHTML = `
            <tr><td colspan="8" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="text-muted mt-2 mb-0">Buscando servicios...</p>
            </td></tr>`;
    }

    function mostrarError(msg) {
        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;
        tbody.innerHTML = `
            <tr><td colspan="8" class="text-center py-4 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>${msg}
            </td></tr>`;
    }

    // Cambia a la página indicada manteniendo los filtros activos
    window.cambiarPagina = function (page) {
        fetchFiltrado(page);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Resetea todos los filtros y recarga el index original
    window.limpiarFiltros = function () {
        estado = 'todos'; margen = 'todos'; sort = 'nombre_asc';
        search = ''; precioMin = ''; precioMax = '';

        document.getElementById('searchInput').value = '';
        document.getElementById('precioMin').value   = '';
        document.getElementById('precioMax').value   = '';

        document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === 'todos'));
        document.querySelectorAll('.filter-margen-btn').forEach(b => b.classList.toggle('active', b.dataset.margen === 'todos'));
        document.querySelectorAll('.sort-option').forEach(b => b.classList.toggle('active', b.dataset.sort === 'nombre_asc'));
        document.getElementById('sortLabel').textContent = 'Nombre A-Z';

        window.location.href = "{{ route('servicios.index') }}";
    };

    // Event listeners
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            estado = this.dataset.filter;
            fetchFiltrado();
        });
    });

    document.querySelectorAll('.filter-margen-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-margen-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            margen = this.dataset.margen;
            fetchFiltrado();
        });
    });

    document.querySelectorAll('.sort-option').forEach(opt => {
        opt.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.sort-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('sortLabel').textContent = this.textContent.trim();
            sort = this.dataset.sort;
            fetchFiltrado();
        });
    });

    document.getElementById('btnAplicarPrecio').addEventListener('click', function () {
        precioMin = document.getElementById('precioMin').value;
        precioMax = document.getElementById('precioMax').value;
        fetchFiltrado();
        bootstrap.Dropdown.getOrCreateInstance(document.getElementById('precioDropdownBtn')).hide();
    });

    document.getElementById('searchInput').addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        search = this.value;
        searchTimeout = setTimeout(() => fetchFiltrado(), 350);
    });

    document.getElementById('clearSearch').addEventListener('click', function () {
        document.getElementById('searchInput').value = '';
        search = '';
        fetchFiltrado();
        document.getElementById('searchInput').focus();
    });

    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);
});

// Abre el modal con la imagen del servicio ampliada
function abrirModalImagen(src, titulo) {
    const el = document.getElementById('modalImagen');
    if (!el) return;
    document.getElementById('modalImagenSrc').src            = src;
    document.getElementById('modalImagenTitulo').textContent = titulo || 'Imagen del servicio';
    new bootstrap.Modal(el).show();
}
</script>
@endpush

@push('styles')
<style>
.service-list-image {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.2s;
    object-fit: cover;
}
.service-list-image:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 12px rgba(34,197,94,0.2);
    border-color: #22c55e;
    cursor: pointer;
}
.table-hover tbody tr:hover { background: #f0fdf4; }
.table-hover tbody tr:hover .service-list-image { border-color: #22c55e; }
 
/* Filtros estado */
.filter-btn.active                         { background: #22c55e; color: white; border-color: #22c55e; }
.filter-btn[data-filter="inactivo"].active { background: #ef4444; border-color: #ef4444; }
 
/* Filtros margen */
.filter-margen-btn.active                          { background: #0284c7; color: white; border-color: #0284c7; }
.filter-margen-btn[data-margen="alto"].active      { background: #22c55e; border-color: #22c55e; color: white; }
.filter-margen-btn[data-margen="medio"].active     { background: #3b82f6; border-color: #3b82f6; color: white; }
.filter-margen-btn[data-margen="bajo"].active      { background: #f59e0b; border-color: #f59e0b; color: white; }
.filter-margen-btn[data-margen="minimo"].active    { background: #ef4444; border-color: #ef4444; color: white; }
 
.dropdown-menu { max-height: 300px; overflow-y: auto; }
 
@media (max-width: 768px) {
    .service-list-image { width: 50px !important; height: 50px !important; }
    .d-flex.flex-wrap   { flex-direction: column; gap: 0.5rem; }
    .dropdown           { width: 100%; }
    .dropdown .btn      { width: 100%; text-align: left; }
}
</style>
@endpush