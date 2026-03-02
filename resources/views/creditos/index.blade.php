@extends('layouts.app')

@section('title', 'Créditos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Créditos</li>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Estadísticas -->
    @if(!empty($estadisticas))
    <div class="row mb-4" id="estadisticas-wrap">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Total Créditos</h6>
                            <h4 class="mb-0" id="stat-total">{{ $estadisticas['total_creditos'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-file-invoice-dollar fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Activos</h6>
                            <h4 class="mb-0" id="stat-activos">{{ $estadisticas['activos'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock fa-2x text-success"></i>
                        </div>
                    </div>
                    <small class="text-muted" id="stat-capital-activos">
                        Q{{ number_format($estadisticas['capital_pendiente_activos'] ?? 0, 2) }} pendiente
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Abonados</h6>
                            <h4 class="mb-0" id="stat-abonados">{{ $estadisticas['abonados'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                        </div>
                    </div>
                    <small class="text-muted" id="stat-capital-abonados">
                        Q{{ number_format($estadisticas['capital_pendiente_abonados'] ?? 0, 2) }} pendiente
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Recuperado</h6>
                            <h4 class="mb-0" id="stat-recuperado">Q{{ number_format($estadisticas['total_recuperado'] ?? 0, 2) }}</h4>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-file-invoice-dollar me-2"></i>Gestión de Créditos
            </h5>
            <a href="{{ route('creditos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Crédito
            </a>
        </div>
        <div class="card-body">

            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-7">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">Todos</button>
                        <button type="button" class="btn btn-outline-danger btn-sm filter-btn"  data-filter="activo">Activos</button>
                        <button type="button" class="btn btn-outline-warning btn-sm filter-btn" data-filter="abonado">Abonados</button>
                        <button type="button" class="btn btn-outline-success btn-sm filter-btn" data-filter="pagado">Pagados</button>
                    </div>
                    <div class="btn-group ms-2" role="group">
                        <button type="button" class="btn btn-outline-info btn-sm sort-btn active" data-sort="fecha_desc">
                            <i class="fas fa-sort-amount-down me-1"></i>Más recientes
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm sort-btn" data-sort="fecha_asc">
                            <i class="fas fa-sort-amount-up me-1"></i>Más antiguos
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm sort-btn" data-sort="monto_desc">
                            <i class="fas fa-sort-amount-down-alt me-1"></i>Mayor monto
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput"
                               placeholder="Buscar por cliente, producto/servicio...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <i class="fas fa-info-circle"></i> Búsqueda en nombre del cliente y producto/servicio
                    </small>
                </div>
            </div>

            @php
                $creditosData  = $creditos['data']  ?? (is_array($creditos) ? $creditos : []);
                $creditosLinks = $creditos['links'] ?? [];
                $creditosMeta  = [
                    'current_page' => $creditos['current_page'] ?? 1,
                    'per_page'     => $creditos['per_page'] ?? 20,
                    'total'        => $creditos['total'] ?? 0,
                    'from'         => $creditos['from'] ?? 1,
                    'to'           => $creditos['to'] ?? 0,
                ];
            @endphp

            {{-- Tabla siempre en el DOM --}}
            <div id="tabla-container">

                <div id="empty-state" class="text-center py-5" style="{{ empty($creditosData) ? '' : 'display:none;' }}">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay créditos registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer crédito</p>
                    <a href="{{ route('creditos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Crédito
                    </a>
                </div>

                <div class="table-responsive" id="table-wrapper" style="{{ empty($creditosData) ? 'display:none;' : '' }}">
                    <table class="table table-hover table-striped" id="creditosTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Cliente</th>
                                <th>Producto/Servicio</th>
                                <th>Capital</th>
                                <th>Restante</th>
                                <th>Progreso</th>
                                <th>Fecha Crédito</th>
                                <th>Último Pago</th>
                                <th>Estado</th>
                                <th style="width: 180px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($creditosData as $credito)
                            @php
                                $porcentajePagado = $credito['capital'] > 0
                                    ? (($credito['capital'] - $credito['capital_restante']) / $credito['capital']) * 100
                                    : 0;
                                $estadoColors = ['activo' => 'danger', 'abonado' => 'warning', 'pagado' => 'success'];
                                $estadoLabels = ['activo' => 'Activo', 'abonado' => 'Abonado', 'pagado' => 'Pagado'];
                                $estadoIconos = ['activo' => 'clock', 'abonado' => 'money-bill', 'pagado' => 'check-circle'];
                                $estado = $credito['estado'] ?? 'activo';
                            @endphp
                            <tr>
                                <td><strong>{{ $credito['nombre_cliente'] ?? 'N/A' }}</strong></td>
                                <td><small>{{ Str::limit($credito['producto_o_servicio_dado'] ?: 'No especificado', 40) }}</small></td>
                                <td><strong>Q{{ number_format($credito['capital'] ?? 0, 2) }}</strong></td>
                                <td>
                                    @if(($credito['capital_restante'] ?? 0) > 0)
                                        <strong class="text-danger">Q{{ number_format($credito['capital_restante'], 2) }}</strong>
                                    @else
                                        <span class="text-success">Q0.00</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $estadoColors[$estado] ?? 'info' }}"
                                                 role="progressbar"
                                                 style="width: {{ $porcentajePagado }}%">
                                            </div>
                                        </div>
                                        <small class="ms-2">{{ number_format($porcentajePagado, 0) }}%</small>
                                    </div>
                                </td>
                                <td><small>{{ isset($credito['fecha_credito']) ? \Carbon\Carbon::parse($credito['fecha_credito'])->format('d/m/Y') : 'N/A' }}</small></td>
                                <td>
                                    @if($credito['fecha_ultimo_pago'] ?? null)
                                        <small>{{ \Carbon\Carbon::parse($credito['fecha_ultimo_pago'])->format('d/m/Y') }}</small>
                                        <br><small class="text-muted">Q{{ number_format($credito['ultima_cantidad_pagada'] ?? 0, 2) }}</small>
                                    @else
                                        <span class="badge bg-light text-dark">Sin pagos</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $estadoColors[$estado] ?? 'secondary' }} p-2">
                                        <i class="fas fa-{{ $estadoIconos[$estado] ?? 'circle' }} me-1"></i>
                                        {{ $estadoLabels[$estado] ?? $estado }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('creditos.show', $credito['id'] ?? '#') }}" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('creditos.edit', $credito['id'] ?? '#') }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(($credito['capital_restante'] ?? 0) > 0)
                                        <button type="button" class="btn btn-sm btn-success btn-registrar-pago"
                                                data-credito-id="{{ $credito['id'] }}"
                                                data-cliente="{{ $credito['nombre_cliente'] ?? 'N/A' }}"
                                                data-capital-restante="{{ $credito['capital_restante'] ?? 0 }}"
                                                title="Registrar pago">
                                            <i class="fas fa-money-bill"></i>
                                        </button>
                                        @else
                                        <button type="button" class="btn btn-sm btn-secondary" disabled title="Crédito pagado">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        <form action="{{ route('creditos.change-status', $credito['id'] ?? '#') }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Cambiar el estado de este crédito?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $estadoColors[$estado] ?? 'secondary' }}" title="Cambiar estado">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Contador y paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3"
                     id="paginacion-container"
                     style="{{ empty($creditosData) ? 'display:none;' : '' }}">
                    <div class="text-muted" id="contador-wrap">
                        @if(($creditosMeta['total'] ?? 0) > 0)
                            Mostrando {{ $creditosMeta['from'] }} - {{ $creditosMeta['to'] }} de
                            <strong>{{ $creditosMeta['total'] }}</strong> créditos
                        @else
                            Mostrando {{ count($creditosData) }} créditos
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <div id="paginacion-wrap">
                            <ul class="pagination mb-0">
                                @foreach($creditosLinks as $link)
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

@include('creditos.partials._modal_registrar_pago_dinamico')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Referencias DOM ───────────────────────────────────────────────
    const getTabla       = () => document.querySelector('#creditosTable tbody');
    const paginacionWrap = document.getElementById('paginacion-wrap');
    const contadorWrap   = document.getElementById('contador-wrap');
    const tableWrapper   = document.getElementById('table-wrapper');
    const paginacionCont = document.getElementById('paginacion-container');
    const emptyState     = document.getElementById('empty-state');

    // ── Estado de filtros ─────────────────────────────────────────────
    let currentEstado = 'todos';
    let currentSort   = 'fecha_desc';
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
        return currentEstado !== 'todos' || currentSearch.trim() !== '' || currentSort !== 'fecha_desc';
    }

    // ── Fetch al endpoint /creditos/filter ────────────────────────────
    function fetchFiltrado(page = 1) {
        if (!hayFiltrosActivos()) {
            window.location.href = "{{ route('creditos.index') }}";
            return;
        }

        mostrarLoader();

        const params = new URLSearchParams({
            search: currentSearch,
            estado: currentEstado,
            sort:   currentSort,
            page:   page
        });

        fetch(`{{ route('creditos.filter') }}?${params.toString()}`, {
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
                renderTabla(data.creditos);
                if (data.estadisticas) actualizarEstadisticas(data.estadisticas);
            } else {
                mostrarError('Error al filtrar créditos');
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

        if (registros.length === 0) {
            mostrarEmptyState('No se encontraron créditos');
            actualizarContador(0, 0, 0);
            actualizarPaginacion([]);
            return;
        }

        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;

        const csrfToken    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const estadoColors = { activo: 'danger', abonado: 'warning', pagado: 'success' };
        const estadoLabels = { activo: 'Activo', abonado: 'Abonado', pagado: 'Pagado' };
        const estadoIconos = { activo: 'clock', abonado: 'money-bill', pagado: 'check-circle' };

        tbody.innerHTML = '';

        registros.forEach(credito => {
            const estado     = credito.estado ?? 'activo';
            const color      = estadoColors[estado] ?? 'secondary';
            const label      = estadoLabels[estado] ?? estado;
            const icono      = estadoIconos[estado] ?? 'circle';
            const capital    = parseFloat(credito.capital ?? 0);
            const restante   = parseFloat(credito.capital_restante ?? 0);
            const porcentaje = capital > 0 ? ((capital - restante) / capital * 100).toFixed(0) : 0;
            const showUrl    = `/creditos/${credito.id}`;
            const editUrl    = `/creditos/${credito.id}/editar`;
            const statusUrl  = `/creditos/${credito.id}/cambiar-estado`;

            const fmt = val => parseFloat(val ?? 0).toLocaleString('es-GT', { minimumFractionDigits: 2 });

            const formatearFecha = (fechaStr) => {
                if (!fechaStr) return 'N/A';
                const partes = fechaStr.split('T')[0].split('-'); // "2026-02-11" → ["2026","02","11"]
                if (partes.length < 3) return 'N/A';
                return `${partes[2]}/${partes[1]}/${partes[0]}`; // → "11/02/2026"
            };

            const fechaCredito = formatearFecha(credito.fecha_credito);

            const ultimoPago = credito.fecha_ultimo_pago
                ? `<small>${formatearFecha(credito.fecha_ultimo_pago)}</small>
                <br><small class="text-muted">Q${fmt(credito.ultima_cantidad_pagada)}</small>`
                : '<span class="badge bg-light text-dark">Sin pagos</span>';

            const btnPago = restante > 0
                ? `<button type="button" class="btn btn-sm btn-success btn-registrar-pago"
                       data-credito-id="${credito.id}"
                       data-cliente="${credito.nombre_cliente ?? 'N/A'}"
                       data-capital-restante="${restante}"
                       title="Registrar pago">
                       <i class="fas fa-money-bill"></i>
                   </button>`
                : `<button type="button" class="btn btn-sm btn-secondary" disabled title="Crédito pagado">
                       <i class="fas fa-check"></i>
                   </button>`;

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td><strong>${credito.nombre_cliente ?? 'N/A'}</strong></td>
                    <td><small>${(credito.producto_o_servicio_dado || 'No especificado').substring(0, 40)}</small></td>
                    <td><strong>Q${fmt(capital)}</strong></td>
                    <td>${restante > 0
                        ? `<strong class="text-danger">Q${fmt(restante)}</strong>`
                        : '<span class="text-success">Q0.00</span>'}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1" style="height:8px;">
                                <div class="progress-bar bg-${color}" role="progressbar" style="width:${porcentaje}%"></div>
                            </div>
                            <small class="ms-2">${porcentaje}%</small>
                        </div>
                    </td>
                    <td><small>${fechaCredito}</small></td>
                    <td>${ultimoPago}</td>
                    <td>
                        <span class="badge bg-${color} p-2">
                            <i class="fas fa-${icono} me-1"></i>${label}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="${showUrl}" class="btn btn-sm btn-info"    title="Ver detalles"><i class="fas fa-eye"></i></a>
                            <a href="${editUrl}" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                            ${btnPago}
                            <form action="${statusUrl}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Cambiar el estado de este crédito?')">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <button type="submit" class="btn btn-sm btn-${color}" title="Cambiar estado">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>`);
        });

        enlazarBotonesPago();
        actualizarContador(from, to, total);
        actualizarPaginacion(links);
    }

    // ── Actualizar estadísticas ───────────────────────────────────────
    function actualizarEstadisticas(est) {
        const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        const fmt = val => 'Q' + parseFloat(val ?? 0).toLocaleString('es-GT', { minimumFractionDigits: 2 });

        set('stat-total',    est.total_creditos ?? 0);
        set('stat-activos',  est.activos ?? 0);
        set('stat-abonados', est.abonados ?? 0);
        set('stat-recuperado', fmt(est.total_recuperado));

        const ca = document.getElementById('stat-capital-activos');
        if (ca) ca.textContent = fmt(est.capital_pendiente_activos) + ' pendiente';
        const cab = document.getElementById('stat-capital-abonados');
        if (cab) cab.textContent = fmt(est.capital_pendiente_abonados) + ' pendiente';
    }

    // ── Contador ──────────────────────────────────────────────────────
    function actualizarContador(from, to, total) {
        if (!contadorWrap) return;
        contadorWrap.innerHTML = total > 0
            ? `Mostrando ${from} - ${to} de <strong>${total}</strong> créditos encontrados`
            : 'Sin resultados';
    }

    // ── Paginación ────────────────────────────────────────────────────
    function actualizarPaginacion(links) {
        if (!paginacionWrap) return;
        if (!links || links.length <= 3) { paginacionWrap.innerHTML = ''; return; }
        let html = '<ul class="pagination mb-0">';
        links.forEach(link => {
            const active   = link.active ? 'active'   : '';
            const disabled = !link.url   ? 'disabled' : '';
            let pageNum = null;
            if (link.url) {
                const match = link.url.match(/[?&]page=(\d+)/);
                pageNum = match ? match[1] : null;
            }
            const click = pageNum ? `onclick="cambiarPagina(${pageNum}); return false;"` : '';
            html += `<li class="page-item ${active} ${disabled}">
                        <a class="page-link" href="#" ${click}>${link.label}</a>
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
                <td colspan="9" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted mt-2 mb-0">Buscando créditos...</p>
                </td>
            </tr>`;
    }

    function mostrarError(msg) {
        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4 text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>${msg}
                </td>
            </tr>`;
    }

    // ── Enlazar botones de pago tras render AJAX ──────────────────────
    function enlazarBotonesPago() {
        document.querySelectorAll('.btn-registrar-pago').forEach(btn => {
            btn.addEventListener('click', function () {
                if (window.abrirModalPago) {
                    window.abrirModalPago(
                        this.dataset.creditoId,
                        this.dataset.cliente,
                        parseFloat(this.dataset.capitalRestante)
                    );
                }
            });
        });
    }

    // ── Cambiar página ────────────────────────────────────────────────
    window.cambiarPagina = function (page) {
        fetchFiltrado(page);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // ── Limpiar filtros ───────────────────────────────────────────────
    window.limpiarFiltros = function () {
        currentEstado = 'todos';
        currentSort   = 'fecha_desc';
        currentSearch = '';
        document.getElementById('searchInput').value = '';
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === 'todos'));
        document.querySelectorAll('.sort-btn').forEach(b => b.classList.toggle('active', b.dataset.sort === 'fecha_desc'));
        window.location.href = "{{ route('creditos.index') }}";
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

    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentSort = this.dataset.sort;
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

    // ── Enlazar botones en carga inicial ──────────────────────────────
    enlazarBotonesPago();
});
</script>
@endpush

@push('styles')
<style>
.progress {
    min-width: 100px;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Estilos para botones de filtro activos */
.filter-btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.filter-btn[data-filter="activo"].active {
    background-color: #dc3545;
    border-color: #dc3545;
}

.filter-btn[data-filter="abonado"].active {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.filter-btn[data-filter="pagado"].active {
    background-color: #198754;
    border-color: #198754;
}

.sort-btn.active {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: #000;
}

/* Estilos para cards de estadísticas */
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
    
    .col-md-7, .col-md-5 {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .btn-group {
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }
    
    .ms-2 {
        margin-left: 0 !important;
        margin-top: 0.25rem;
    }
}
</style>
@endpush