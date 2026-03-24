@extends('layouts.app')

@section('title', 'Ventas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Ventas</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Estadísticas --}}
    @if(!empty($estadisticas))
    <div class="row mb-4">
        @php
            $stats = [
                ['label' => 'Ventas Hoy',    'icon' => 'shopping-cart',  'color' => 'primary', 'total' => $estadisticas['totales']['hoy']['total']    ?? 0, 'count' => $estadisticas['totales']['hoy']['ventas']    ?? 0, 'suffix' => 'ventas'],
                ['label' => 'Esta Semana',   'icon' => 'calendar-week',  'color' => 'success', 'total' => $estadisticas['totales']['semana']['total']  ?? 0, 'count' => $estadisticas['totales']['semana']['ventas']  ?? 0, 'suffix' => 'ventas'],
                ['label' => 'Este Mes',      'icon' => 'calendar-alt',   'color' => 'warning', 'total' => $estadisticas['totales']['mes']['total']     ?? 0, 'count' => $estadisticas['totales']['mes']['ventas']     ?? 0, 'suffix' => 'ventas'],
                ['label' => 'Pendientes',    'icon' => 'clock',          'color' => 'info',    'total' => null,                                                'count' => $estadisticas['por_tipo']['pendiente']['cantidad'] ?? 0, 'suffix' => 'pendientes'],
            ];
        @endphp
        @foreach($stats as $stat)
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-{{ $stat['color'] }} bg-opacity-10 rounded-circle p-3 me-3 flex-shrink-0">
                        <i class="fas fa-{{ $stat['icon'] }} text-{{ $stat['color'] }} fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">{{ $stat['label'] }}</small>
                        @if($stat['total'] !== null)
                            <h5 class="mb-0 fw-bold">Q {{ number_format($stat['total'], 2) }}</h5>
                            <small class="text-muted">{{ $stat['count'] }} {{ $stat['suffix'] }}</small>
                        @else
                            <h5 class="mb-0 fw-bold">{{ $stat['count'] }}</h5>
                            <small class="text-muted">{{ $stat['suffix'] }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-shopping-cart me-2"></i>Gestión de Ventas
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('ventas.reporte') }}" class="btn btn-success">
                    <i class="fas fa-chart-bar me-2"></i> Reportes
                </a>
                <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nueva Venta
                </a>
            </div>
        </div>

        <div class="card-body">

            {{-- Fila 1: Estado y método de pago --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap gap-2 align-items-center">

                        {{-- Estado --}}
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">Todos</button>
                            <button class="btn btn-outline-success  btn-sm filter-btn" data-filter="completada">Completadas</button>
                            <button class="btn btn-outline-warning  btn-sm filter-btn" data-filter="pendiente">Pendientes</button>
                            <button class="btn btn-outline-danger   btn-sm filter-btn" data-filter="cancelada">Canceladas</button>
                        </div>

                        {{-- Método de pago --}}
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary btn-sm filter-pago-btn active" data-pago="todos">Todo pago</button>
                            <button class="btn btn-outline-success  btn-sm filter-pago-btn" data-pago="efectivo">Efectivo</button>
                            <button class="btn btn-outline-info     btn-sm filter-pago-btn" data-pago="tarjeta">Tarjeta</button>
                            <button class="btn btn-outline-primary  btn-sm filter-pago-btn" data-pago="transferencia">Transferencia</button>
                            <button class="btn btn-outline-warning  btn-sm filter-pago-btn" data-pago="mixto">Mixto</button>
                        </div>

                        {{-- Ordenamiento --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="sortDropdownBtn" data-bs-toggle="dropdown">
                                <i class="fas fa-sort me-1"></i><span id="sortLabel">Más recientes</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item sort-option active" href="#" data-sort="fecha_desc">Más recientes</a></li>
                                <li><a class="dropdown-item sort-option"        href="#" data-sort="fecha_asc">Más antiguos</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="total_desc">Mayor monto</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="total_asc">Menor monto</a></li>
                            </ul>
                        </div>

                        {{-- Rango de fechas --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="fechaDropdownBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <i class="fas fa-calendar me-1"></i> Fechas
                            </button>
                            <div class="dropdown-menu p-3" style="min-width:280px;">
                                <div class="mb-2">
                                    <label class="form-label form-label-sm">Desde</label>
                                    <input type="date" class="form-control form-control-sm" id="fechaDesde">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label form-label-sm">Hasta</label>
                                    <input type="date" class="form-control form-control-sm" id="fechaHasta">
                                </div>
                                <button class="btn btn-sm btn-primary w-100" id="btnAplicarFechas">
                                    <i class="fas fa-filter me-1"></i>Aplicar rango
                                </button>
                            </div>
                        </div>

                        {{-- Rango de montos --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="montoDropdownBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <i class="fas fa-dollar-sign me-1"></i> Monto
                            </button>
                            <div class="dropdown-menu p-3" style="min-width:260px;">
                                <div class="mb-2">
                                    <label class="form-label form-label-sm">Monto mínimo</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" class="form-control" id="montoMin" min="0" step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label form-label-sm">Monto máximo</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" class="form-control" id="montoMax" min="0" step="0.01" placeholder="9999.99">
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary w-100" id="btnAplicarMonto">
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
                               placeholder="Buscar por N° venta, cliente, NIT, descripción...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Datos iniciales PHP --}}
            @php
                $ventasData  = $ventas['data']  ?? [];
                $ventasLinks = $ventas['links'] ?? [];
                $ventasMeta  = [
                    'total' => $ventas['total'] ?? 0,
                    'from'  => $ventas['from']  ?? 0,
                    'to'    => $ventas['to']    ?? 0,
                ];
            @endphp

            {{-- Tabla siempre en el DOM --}}
            <div id="tabla-container">

                <div id="empty-state" class="text-center py-5"
                     style="{{ empty($ventasData) ? '' : 'display:none;' }}">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay ventas registradas</h5>
                    <p class="text-muted">Comienza registrando tu primera venta</p>
                    <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primera Venta
                    </a>
                </div>

                <div class="table-responsive" id="table-wrapper"
                     style="{{ empty($ventasData) ? 'display:none;' : '' }}">
                    <table class="table table-hover table-striped" id="ventasTable">
                        <thead class="table-head-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>N° Venta</th>
                                <th>Cliente</th>
                                <th>Items</th>
                                <th>Método</th>
                                <th>Subtotal</th>
                                <th>Descuento</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th style="width:100px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventasData as $venta)
                            @php
                                $createdAt  = \Carbon\Carbon::parse($venta['created_at'] ?? now())->timezone('America/Guatemala');
                                $estado     = $venta['estado']      ?? 'pendiente';
                                $metodo     = $venta['metodo_pago'] ?? 'efectivo';
                                $numItems   = count($venta['detalles'] ?? []);
                                $estadoColor = ['completada' => 'success', 'cancelada' => 'danger', 'pendiente' => 'warning'][$estado] ?? 'secondary';
                                $metodoColor = ['efectivo' => 'success', 'tarjeta' => 'info', 'transferencia' => 'primary', 'mixto' => 'secondary'][$metodo] ?? 'warning';
                                $metodoIcon  = ['efectivo' => 'money-bill', 'tarjeta' => 'credit-card', 'transferencia' => 'exchange-alt', 'mixto' => 'coins'][$metodo] ?? 'money-bill';
                            @endphp
                            <tr>
                                <td>
                                    <small class="fw-semibold d-block">{{ $createdAt->format('d/m/Y') }}</small>
                                    <small class="text-muted">{{ $createdAt->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $venta['numero_venta'] ?? 'SIN-NUM' }}</strong>
                                    <small class="text-muted d-block">ID: {{ $venta['id'] }}</small>
                                </td>
                                <td>
                                    {{ $venta['cliente']['nombre'] ?? 'Sin cliente' }}
                                    @if(!empty($venta['cliente']['nit']))
                                        <small class="text-muted d-block">NIT: {{ $venta['cliente']['nit'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $numItems }} {{ $numItems === 1 ? 'item' : 'items' }}
                                    </span>
                                    @if($numItems > 0)
                                        @php
                                            $popHtml = '<ul class="list-unstyled mb-0">';
                                            foreach (array_slice($venta['detalles'] ?? [], 0, 3) as $d) {
                                                $popHtml .= '<li><small>• ' . ($d['cantidad'] ?? 1) . 'x ' . Str::limit($d['descripcion'] ?? '', 25) . '</small></li>';
                                            }
                                            if ($numItems > 3) {
                                                $popHtml .= '<li><small class="text-muted">... y ' . ($numItems - 3) . ' más</small></li>';
                                            }
                                            $popHtml .= '</ul>';
                                        @endphp
                                        <button type="button" class="btn btn-sm btn-link p-0 ms-1"
                                                data-bs-toggle="popover"
                                                data-bs-html="true"
                                                data-bs-trigger="hover"
                                                title="Items"
                                                data-bs-content="{{ $popHtml }}">
                                            <i class="fas fa-info-circle text-muted"></i>
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $metodoColor }}">
                                        <i class="fas fa-{{ $metodoIcon }} me-1"></i>{{ ucfirst($metodo) }}
                                    </span>
                                </td>
                                <td>Q{{ number_format($venta['subtotal'] ?? 0, 2) }}</td>
                                <td>
                                    @if(($venta['descuento_total'] ?? 0) > 0)
                                        <span class="text-danger">
                                            <i class="fas fa-tag me-1"></i>Q{{ number_format($venta['descuento_total'], 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><strong class="text-primary">Q{{ number_format($venta['total'] ?? 0, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $estadoColor }}">
                                        <i class="fas fa-{{ $estado === 'completada' ? 'check-circle' : ($estado === 'pendiente' ? 'clock' : 'times-circle') }} me-1"></i>
                                        {{ ucfirst($estado) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('ventas.show', $venta['id']) }}"
                                           class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($estado !== 'cancelada')
                                            <form action="{{ route('ventas.cancelar', $venta['id']) }}" method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Cancelar esta venta?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancelar">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
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
                     style="{{ empty($ventasData) ? 'display:none;' : '' }}">
                    <div class="text-muted" id="contador-wrap">
                        @if(($ventasMeta['total'] ?? 0) > 0)
                            Mostrando {{ $ventasMeta['from'] }} - {{ $ventasMeta['to'] }} de
                            <strong>{{ $ventasMeta['total'] }}</strong> ventas
                        @else
                            Mostrando {{ count($ventasData) }} ventas
                        @endif
                    </div>
                    <nav>
                        <div id="paginacion-wrap">
                            <ul class="pagination mb-0">
                                @foreach($ventasLinks as $link)
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

    // Estado actual de todos los filtros activos
    let estado      = 'todos';
    let metodoPago  = 'todos';
    let sort        = 'fecha_desc';
    let search      = '';
    let fechaDesde  = '';
    let fechaHasta  = '';
    let montoMin    = '';
    let montoMax    = '';
    let searchTimeout = null;

    // Referencias al DOM reutilizadas en múltiples funciones
    const getTabla       = () => document.querySelector('#ventasTable tbody');
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

    // Muestra el estado vacío con mensaje y botón de limpiar
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

        const params = new URLSearchParams({
            estado, metodo_pago: metodoPago, sort, search, page,
            fecha_inicio: fechaDesde, fecha_fin: fechaHasta,
            monto_min: montoMin, monto_max: montoMax
        });

        fetch(`{{ route('ventas.filter') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) renderTabla(data.ventas);
            else mostrarError('Error al filtrar ventas');
        })
        .catch(() => mostrarError('Error de conexión'));
    }

    // Formatea una fecha ISO con timezone de Guatemala
    function formatearFecha(isoStr) {
        if (!isoStr) return { fecha: 'N/A', hora: '' };

        const fecha = new Date(isoStr);
        const opts  = { timeZone: 'America/Guatemala' };

        const dia  = fecha.toLocaleString('es-GT', { ...opts, day:   '2-digit' });
        const mes  = fecha.toLocaleString('es-GT', { ...opts, month: '2-digit' });
        const anio = fecha.toLocaleString('es-GT', { ...opts, year:  'numeric' });
        const hora = fecha.toLocaleString('es-GT', { ...opts, hour: '2-digit', minute: '2-digit', hour12: false });

        return { fecha: `${dia}/${mes}/${anio}`, hora };
    }

    function hora12(hora24) {
        if (!hora24) return '';
        const [h, m] = hora24.split(':');
        const hNum   = parseInt(h);
        const ampm   = hNum >= 12 ? 'PM' : 'AM';
        const h12    = hNum % 12 || 12;
        return `${h12}:${m} ${ampm}`;
    }

    // Transforma los datos paginados de la API en filas HTML e inyecta en la tabla
    function renderTabla(paginado) {
        const registros = paginado.data  ?? [];
        const links     = paginado.links ?? [];
        const total     = paginado.total ?? 0;
        const from      = paginado.from  ?? 0;
        const to        = paginado.to    ?? 0;

        if (registros.length === 0) {
            mostrarEmptyState('No se encontraron ventas');
            actualizarContador(0, 0, 0);
            actualizarPaginacion([]);
            return;
        }

        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;

        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const fmt  = val => parseFloat(val ?? 0).toLocaleString('es-GT', { minimumFractionDigits: 2 });

        const estadoColor  = { completada: 'success', cancelada: 'danger',  pendiente: 'warning' };
        const estadoIcon   = { completada: 'check-circle', cancelada: 'times-circle', pendiente: 'clock' };
        const metodoColor  = { efectivo: 'success', tarjeta: 'info', transferencia: 'primary', mixto: 'secondary' };
        const metodoIcon   = { efectivo: 'money-bill', tarjeta: 'credit-card', transferencia: 'exchange-alt', mixto: 'coins' };

        tbody.innerHTML = '';

        if (registros.length > 0) {
    console.log('Primera venta keys:', Object.keys(registros[0]));
    console.log('Detalles:', registros[0].detalles);
    console.log('Detalles raw:', JSON.stringify(registros[0].detalles));
}

        registros.forEach(v => {
            const { fecha, hora } = formatearFecha(v.created_at);
            const est    = v.estado      ?? 'pendiente';
            const met    = v.metodo_pago ?? 'efectivo';
            const items  = v.detalles    ?? [];
            const nItems = items.length;
            const showUrl   = `/ventas/${v.id}`;
            const cancelUrl = `/ventas/${v.id}/cancelar`;

            // Construye el popover de items
            let popoverContent = '<ul class=\'list-unstyled mb-0\'>';
            items.slice(0, 3).forEach(d => {
                const desc = (d.descripcion ?? '').substring(0, 25);
                popoverContent += `<li><small>• ${d.cantidad}x ${desc}</small></li>`;
            });
            if (nItems > 3) popoverContent += `<li><small class='text-muted'>... y ${nItems - 3} más</small></li>`;
            popoverContent += '</ul>';

            const cancelBtn = est !== 'cancelada'
                ? `<form action="${cancelUrl}" method="POST" class="d-inline"
                         onsubmit="return confirm('¿Cancelar esta venta?')">
                       <input type="hidden" name="_token" value="${csrf}">
                       <button type="submit" class="btn btn-sm btn-danger" title="Cancelar">
                           <i class="fas fa-ban"></i>
                       </button>
                   </form>`
                : '';

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td>
                        <small class="fw-semibold d-block">${fecha}</small>
                        <small class="text-muted">${hora12(hora)}</small>
                    </td>
                    <td>
                        <strong>${v.numero_venta ?? 'SIN-NUM'}</strong>
                        <small class="text-muted d-block">ID: ${v.id}</small>
                    </td>
                    <td>
                        ${v.cliente?.nombre ?? 'Sin cliente'}
                        ${v.cliente?.nit ? `<small class="text-muted d-block">NIT: ${v.cliente.nit}</small>` : ''}
                    </td>
                    <td>
                        <span class="badge bg-info">${nItems} ${nItems === 1 ? 'item' : 'items'}</span>
                        ${nItems > 0 ? `<button type="button" class="btn btn-sm btn-link p-0 ms-1"
                            data-bs-toggle="popover" data-bs-html="true" data-bs-trigger="hover"
                            title="Items" data-bs-content="${popoverContent.replace(/"/g, '&quot;')}">
                            <i class="fas fa-info-circle text-muted"></i>
                        </button>` : ''}
                    </td>
                    <td>
                        <span class="badge bg-${metodoColor[met] ?? 'secondary'}">
                            <i class="fas fa-${metodoIcon[met] ?? 'money-bill'} me-1"></i>${met.charAt(0).toUpperCase() + met.slice(1)}
                        </span>
                    </td>
                    <td>Q${fmt(v.subtotal)}</td>
                    <td>
                        ${parseFloat(v.descuento_total ?? 0) > 0
                            ? `<span class="text-danger"><i class="fas fa-tag me-1"></i>Q${fmt(v.descuento_total)}</span>`
                            : '<span class="text-muted">—</span>'}
                    </td>
                    <td><strong class="text-primary">Q${fmt(v.total)}</strong></td>
                    <td>
                        <span class="badge bg-${estadoColor[est] ?? 'secondary'}">
                            <i class="fas fa-${estadoIcon[est] ?? 'circle'} me-1"></i>${est.charAt(0).toUpperCase() + est.slice(1)}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="${showUrl}" class="btn btn-sm btn-info" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            ${cancelBtn}
                        </div>
                    </td>
                </tr>`);
        });

        // Reinicia los popovers de Bootstrap en las nuevas filas
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
            bootstrap.Popover.getOrCreateInstance(el);
        });

        actualizarContador(from, to, total);
        actualizarPaginacion(links);
    }

    // Actualiza el texto del contador de resultados
    function actualizarContador(from, to, total) {
        if (!contadorWrap) return;
        contadorWrap.innerHTML = total > 0
            ? `Mostrando ${from} - ${to} de <strong>${total}</strong> ventas encontradas`
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
            <tr><td colspan="10" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="text-muted mt-2 mb-0">Buscando ventas...</p>
            </td></tr>`;
    }

    function mostrarError(msg) {
        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;
        tbody.innerHTML = `
            <tr><td colspan="10" class="text-center py-4 text-danger">
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
        estado = 'todos'; metodoPago = 'todos'; sort = 'fecha_desc';
        search = ''; fechaDesde = ''; fechaHasta = ''; montoMin = ''; montoMax = '';

        document.getElementById('searchInput').value = '';
        document.getElementById('fechaDesde').value  = '';
        document.getElementById('fechaHasta').value  = '';
        document.getElementById('montoMin').value    = '';
        document.getElementById('montoMax').value    = '';

        document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === 'todos'));
        document.querySelectorAll('.filter-pago-btn').forEach(b => b.classList.toggle('active', b.dataset.pago === 'todos'));
        document.querySelectorAll('.sort-option').forEach(b => b.classList.toggle('active', b.dataset.sort === 'fecha_desc'));
        document.getElementById('sortLabel').textContent = 'Más recientes';

        window.location.href = "{{ route('ventas.index') }}";
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

    document.querySelectorAll('.filter-pago-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-pago-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            metodoPago = this.dataset.pago;
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

    document.getElementById('btnAplicarFechas').addEventListener('click', function () {
        fechaDesde = document.getElementById('fechaDesde').value;
        fechaHasta = document.getElementById('fechaHasta').value;
        fetchFiltrado();
        bootstrap.Dropdown.getOrCreateInstance(document.getElementById('fechaDropdownBtn')).hide();
    });

    document.getElementById('btnAplicarMonto').addEventListener('click', function () {
        montoMin = document.getElementById('montoMin').value;
        montoMax = document.getElementById('montoMax').value;
        fetchFiltrado();
        bootstrap.Dropdown.getOrCreateInstance(document.getElementById('montoDropdownBtn')).hide();
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

    // Inicializa los popovers del render inicial de PHP
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
        bootstrap.Popover.getOrCreateInstance(el);
    });
});
</script>
@endpush

@push('styles')
<style>
.table-hover tbody tr:hover { background: #f0fdf4; }
 
/* Estado */
.filter-btn.active                            { background: #22c55e; color: white; border-color: #22c55e; }
.filter-btn[data-filter="completada"].active  { background: #22c55e; border-color: #22c55e; }
.filter-btn[data-filter="pendiente"].active   { background: #f59e0b; border-color: #f59e0b; color: white; }
.filter-btn[data-filter="cancelada"].active   { background: #ef4444; border-color: #ef4444; }
 
/* Pago */
.filter-pago-btn.active                               { background: #0284c7; color: white; border-color: #0284c7; }
.filter-pago-btn[data-pago="efectivo"].active         { background: #22c55e; border-color: #22c55e; color: white; }
.filter-pago-btn[data-pago="tarjeta"].active          { background: #3b82f6; border-color: #3b82f6; color: white; }
.filter-pago-btn[data-pago="transferencia"].active    { background: #64748b; border-color: #64748b; color: white; }
.filter-pago-btn[data-pago="mixto"].active            { background: #f59e0b; border-color: #f59e0b; color: white; }
 
/* Stats cards en ventas */
.col-md-3 .card.border-0:hover { transform: none; }
 
/* Popovers */
.popover { max-width: 280px; font-size: 0.83rem; }
.popover li { padding: 2px 0; border-bottom: 1px solid #f1f5f9; }
.popover li:last-child { border-bottom: none; }
 
.dropdown-menu { max-height: 320px; overflow-y: auto; }
 
@media (max-width: 768px) {
    .d-flex.flex-wrap { flex-direction: column; gap: 0.5rem; }
    .dropdown         { width: 100%; }
    .dropdown .btn    { width: 100%; text-align: left; }
}
</style>
@endpush
 