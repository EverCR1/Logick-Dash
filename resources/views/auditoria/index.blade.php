@extends('layouts.app')

@section('title', 'Módulo de Auditoría')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Auditoría</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Módulo de Auditoría
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('auditoria.estadisticas') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar me-2"></i> Estadísticas
                </a>
                <button type="button" class="btn btn-secondary" id="btnActualizar">
                    <i class="fas fa-sync-alt me-2"></i> Actualizar
                </button>
            </div>
        </div>

        <div class="card-body">

            <!-- Filtros -->
            <div class="row g-3 mb-4" id="filtros-wrap">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Módulo</label>
                    <select id="filtroModulo" class="form-select form-select-sm">
                        <option value="todos">Todos</option>
                        @foreach($modulos as $modulo)
                            <option value="{{ $modulo }}" {{ ($params['modulo'] ?? 'todos') === $modulo ? 'selected' : '' }}>
                                {{ ucfirst($modulo) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Acción</label>
                    <select id="filtroAccion" class="form-select form-select-sm">
                        <option value="todos">Todas</option>
                        <option value="CREAR"         {{ ($params['accion'] ?? 'todos') === 'CREAR'         ? 'selected' : '' }}>Creaciones</option>
                        <option value="EDITAR"        {{ ($params['accion'] ?? 'todos') === 'EDITAR'        ? 'selected' : '' }}>Ediciones</option>
                        <option value="ELIMINAR"      {{ ($params['accion'] ?? 'todos') === 'ELIMINAR'      ? 'selected' : '' }}>Eliminaciones</option>
                        <option value="CAMBIO_ESTADO" {{ ($params['accion'] ?? 'todos') === 'CAMBIO_ESTADO' ? 'selected' : '' }}>Cambios de estado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha inicio</label>
                    <input type="date" id="filtroFechaInicio" class="form-control form-control-sm"
                           value="{{ $params['fecha_inicio'] ?? now()->subDays(7)->format('Y-m-d') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Fecha fin</label>
                    <input type="date" id="filtroFechaFin" class="form-control form-control-sm"
                           value="{{ $params['fecha_fin'] ?? now()->format('Y-m-d') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Búsqueda</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="filtroBusqueda" class="form-control"
                               placeholder="Buscar en descripción, usuario..."
                               value="{{ $params['busqueda'] ?? '' }}">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="btnLimpiar">
                        <i class="fas fa-undo me-1"></i>Limpiar
                    </button>
                </div>
            </div>

            {{-- Tabla siempre en el DOM --}}
            <div id="tabla-container">

                {{-- Tabla --}}
                @php
                    $auditoriaData  = $auditoria['data']  ?? [];
                    $auditoriaLinks = $auditoria['links'] ?? [];
                    $auditoriaMeta  = [
                        'current_page' => $auditoria['current_page'] ?? 1,
                        'per_page'     => $auditoria['per_page']     ?? 20,
                        'total'        => $auditoria['total']        ?? 0,
                        'from'         => $auditoria['from']         ?? 0,
                        'to'           => $auditoria['to']           ?? 0,
                    ];
                @endphp

                {{-- Empty state --}}
                <div id="empty-state" class="text-center py-5"
                     style="{{ empty($auditoriaData ?? []) ? '' : 'display:none;' }}">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay registros de auditoría</h5>
                    <p class="text-muted">Los movimientos del sistema aparecerán aquí</p>
                </div>

                <div class="table-responsive" id="table-wrapper"
                     style="{{ empty($auditoriaData) ? 'display:none;' : '' }}">
                    <table class="table table-hover" id="auditoriaTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Módulo</th>
                                <th>Descripción</th>
                                <th>IP</th>
                                <th style="width:80px;">Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditoriaData as $log)
                            @php
                                $accionConfig = [
                                    'CREAR'         => ['badge' => 'bg-success',   'icono' => 'fa-plus-circle'],
                                    'EDITAR'        => ['badge' => 'bg-warning text-dark', 'icono' => 'fa-edit'],
                                    'ELIMINAR'      => ['badge' => 'bg-danger',    'icono' => 'fa-trash'],
                                    'CAMBIO_ESTADO' => ['badge' => 'bg-info text-dark',   'icono' => 'fa-sync-alt'],
                                ];
                                $accion = $log['accion'] ?? 'N/A';
                                $cfg    = $accionConfig[$accion] ?? ['badge' => 'bg-secondary', 'icono' => 'fa-history'];
                                $accionLabel = [
                                    'CREAR'         => 'Creación',
                                    'EDITAR'        => 'Edición',
                                    'ELIMINAR'      => 'Eliminación',
                                    'CAMBIO_ESTADO' => 'Cambio estado',
                                ][$accion] ?? $accion;
                            @endphp
                            <tr>
                                <td>
                                    <small class="fw-semibold">
                                        {{ \Carbon\Carbon::parse($log['created_at'])->timezone('America/Guatemala')->format('d/m/Y H:i:s') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        {{ \Carbon\Carbon::parse($log['created_at'])->timezone('America/Guatemala')->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ $log['usuario_nombre'] ?? 'Sistema' }}</strong>
                                    <small class="text-muted d-block">{{ ucfirst($log['usuario_rol'] ?? 'N/A') }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $cfg['badge'] }}">
                                        <i class="fas {{ $cfg['icono'] }} me-1"></i>{{ $accionLabel }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-cube me-1"></i>{{ ucfirst($log['modulo'] ?? 'N/A') }}
                                    </span>
                                </td>
                                <td><small>{{ $log['descripcion'] ?? 'N/A' }}</small></td>
                                <td><code>{{ $log['ip_address'] ?? 'N/A' }}</code></td>
                                <td>
                                    <a href="{{ route('auditoria.show', $log['id']) }}"
                                       class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Contador y paginación -->
                <div class="d-flex justify-content-between align-items-center mt-3"
                     id="paginacion-container"
                     style="{{ empty($auditoriaData) ? 'display:none;' : '' }}">
                    <div class="text-muted" id="contador-wrap">
                        @if(($auditoriaMeta['total'] ?? 0) > 0)
                            Mostrando {{ $auditoriaMeta['from'] }} - {{ $auditoriaMeta['to'] }} de
                            <strong>{{ $auditoriaMeta['total'] }}</strong> registros
                        @else
                            Mostrando {{ count($auditoriaData) }} registros
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <div id="paginacion-wrap">
                            <ul class="pagination mb-0">
                                @foreach($auditoriaLinks as $link)
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Referencias DOM ───────────────────────────────────────────────
    const getTabla       = () => document.querySelector('#auditoriaTable tbody');
    const paginacionWrap = document.getElementById('paginacion-wrap');
    const contadorWrap   = document.getElementById('contador-wrap');
    const tableWrapper   = document.getElementById('table-wrapper');
    const paginacionCont = document.getElementById('paginacion-container');
    const emptyState     = document.getElementById('empty-state');

    // ── Filtros ───────────────────────────────────────────────────────
    const getFiltroModulo       = () => document.getElementById('filtroModulo').value;
    const getFiltroAccion       = () => document.getElementById('filtroAccion').value;
    const getFiltroFechaInicio  = () => document.getElementById('filtroFechaInicio').value;
    const getFiltroFechaFin     = () => document.getElementById('filtroFechaFin').value;
    const getFiltroBusqueda     = () => document.getElementById('filtroBusqueda').value.trim();

    let searchTimeout = null;

    // ── Config de acciones (igual que PHP) ────────────────────────────
    const accionConfig = {
        'CREAR':         { badge: 'bg-success',            icono: 'fa-plus-circle',  label: 'Creación'       },
        'EDITAR':        { badge: 'bg-warning text-dark',  icono: 'fa-edit',         label: 'Edición'        },
        'ELIMINAR':      { badge: 'bg-danger',             icono: 'fa-trash',        label: 'Eliminación'    },
        'CAMBIO_ESTADO': { badge: 'bg-info text-dark',     icono: 'fa-sync-alt',     label: 'Cambio estado'  },
    };

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

    // ── Fetch al endpoint /auditoria/filter ───────────────────────────
    function fetchFiltrado(page = 1) {
        mostrarLoader();

        const params = new URLSearchParams({
            modulo:       getFiltroModulo(),
            accion:       getFiltroAccion(),
            fecha_inicio: getFiltroFechaInicio(),
            fecha_fin:    getFiltroFechaFin(),
            busqueda:     getFiltroBusqueda(),
            page:         page
        });

        fetch(`{{ route('auditoria.filter') }}?${params.toString()}`, {
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
                renderTabla(data.auditoria);
            } else {
                mostrarError('Error al cargar registros de auditoría');
            }
        })
        .catch(() => mostrarError('Error de conexión'));
    }

    // ── Formatear fecha/hora ──────────────────────────────────────────
    function formatearFechaHora(isoStr) {
        if (!isoStr) return 'N/A';
        // Parsear manualmente para evitar problemas de timezone
        const [fecha, hora] = isoStr.split('T');
        const [y, m, d]    = fecha.split('-');
        const horaSola      = hora ? hora.substring(0, 8) : '00:00:00';
        return `${d}/${m}/${y} ${horaSola}`;
    }

    function tiempoRelativo(isoStr) {
        if (!isoStr) return '';
        const ahora = new Date();
        const fecha = new Date(isoStr);
        const diff  = Math.floor((ahora - fecha) / 1000); // segundos

        if (diff < 60)          return 'Hace un momento';
        if (diff < 3600)        return `Hace ${Math.floor(diff / 60)} min`;
        if (diff < 86400)       return `Hace ${Math.floor(diff / 3600)} h`;
        if (diff < 2592000)     return `Hace ${Math.floor(diff / 86400)} días`;
        return formatearFechaHora(isoStr).split(' ')[0];
    }

    // ── Renderizar filas ──────────────────────────────────────────────
    function renderTabla(paginado) {
        const registros   = paginado.data          ?? [];
        const links       = paginado.links         ?? [];
        const total       = paginado.total         ?? 0;
        const from        = paginado.from          ?? 0;
        const to          = paginado.to            ?? 0;

        if (registros.length === 0) {
            mostrarEmptyState('No se encontraron registros');
            actualizarContador(0, 0, 0);
            actualizarPaginacion([]);
            return;
        }

        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;

        tbody.innerHTML = '';

        registros.forEach(log => {
            const accion = log.accion ?? 'N/A';
            const cfg    = accionConfig[accion] ?? { badge: 'bg-secondary', icono: 'fa-history', label: accion };
            const showUrl = `/auditoria/${log.id}`;

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>
                        <small class="fw-semibold">${formatearFechaHora(log.created_at)}</small>
                        <small class="text-muted d-block">${tiempoRelativo(log.created_at)}</small>
                    </td>
                    <td>
                        <strong>${log.usuario_nombre ?? 'Sistema'}</strong>
                        <small class="text-muted d-block">${log.usuario_rol ? log.usuario_rol.charAt(0).toUpperCase() + log.usuario_rol.slice(1) : 'N/A'}</small>
                    </td>
                    <td>
                        <span class="badge ${cfg.badge}">
                            <i class="fas ${cfg.icono} me-1"></i>${cfg.label}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-info text-dark">
                            <i class="fas fa-cube me-1"></i>${log.modulo ? log.modulo.charAt(0).toUpperCase() + log.modulo.slice(1) : 'N/A'}
                        </span>
                    </td>
                    <td><small>${log.descripcion ?? 'N/A'}</small></td>
                    <td><code>${log.ip_address ?? 'N/A'}</code></td>
                    <td>
                        <a href="${showUrl}" class="btn btn-sm btn-info" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
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
            ? `Mostrando ${from} - ${to} de <strong>${total}</strong> registros`
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
                <td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="text-muted mt-2 mb-0">Cargando registros...</p>
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
        document.getElementById('filtroModulo').value      = 'todos';
        document.getElementById('filtroAccion').value      = 'todos';
        document.getElementById('filtroFechaInicio').value = '{{ now()->subDays(7)->format("Y-m-d") }}';
        document.getElementById('filtroFechaFin').value    = '{{ now()->format("Y-m-d") }}';
        document.getElementById('filtroBusqueda').value    = '';
        fetchFiltrado();
    };

    // ── Event listeners ───────────────────────────────────────────────

    // Selects: disparan inmediatamente
    ['filtroModulo', 'filtroAccion'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => fetchFiltrado());
    });

    // Fechas: disparan al cambiar
    ['filtroFechaInicio', 'filtroFechaFin'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => fetchFiltrado());
    });

    // Búsqueda: debounce 400ms
    document.getElementById('filtroBusqueda').addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchFiltrado(), 400);
    });

    // Limpiar búsqueda
    document.getElementById('clearSearch').addEventListener('click', function () {
        document.getElementById('filtroBusqueda').value = '';
        fetchFiltrado();
        document.getElementById('filtroBusqueda').focus();
    });

    // Botón limpiar todos
    document.getElementById('btnLimpiar').addEventListener('click', limpiarFiltros);

    // Botón actualizar
    document.getElementById('btnActualizar').addEventListener('click', () => fetchFiltrado());
});
</script>
@endpush

@push('styles')
<style>
    .table td { vertical-align: middle; }

    .badge {
        font-size: 0.82em;
        padding: 0.45em 0.7em;
    }

    code {
        font-size: 0.82em;
        background: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
    }

    @media (max-width: 768px) {
        .row.g-3 > .col-md-2,
        .row.g-3 > .col-md-3,
        .row.g-3 > .col-md-1 {
            width: 100%;
        }
    }
</style>
@endpush