@extends('layouts.app')

@section('title', 'Reporte de Ventas')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Ventas</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-shopping-cart me-2"></i>Reporte de Ventas
            </h5>
            <div>
                <button class="btn btn-success" onclick="exportarReporte()" title="Exportar reporte">
                    <i class="fas fa-file-excel me-2"></i> Exportar
                </button>
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

            <!-- Filtros en tiempo real -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-periodo-btn active" data-periodo="hoy">
                            Hoy
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-periodo-btn" data-periodo="semana">
                            Esta semana
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-periodo-btn" data-periodo="mes">
                            Este mes
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-periodo-btn" data-periodo="personalizado">
                            Personalizado
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Buscar por cliente, vendedor...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filtros avanzados -->
            <div class="row mb-3" id="filtrosAvanzados">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header py-2">
                            <h6 class="mb-0">Filtros avanzados</h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control form-control-sm" id="fechaInicio" value="{{ $fechaInicio }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control form-control-sm" id="fechaFin" value="{{ $fechaFin }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Cliente</label>
                                    <select class="form-select form-select-sm" id="clienteFilter">
                                        <option value="">Todos los clientes</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente['id'] }}" {{ request('cliente_id') == $cliente['id'] ? 'selected' : '' }}>
                                                {{ $cliente['nombre'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Vendedor</label>
                                    <select class="form-select form-select-sm" id="vendedorFilter">
                                        <option value="">Todos los vendedores</option>
                                        @foreach($vendedores as $vendedor)
                                            <option value="{{ $vendedor['id'] }}" {{ request('vendedor_id') == $vendedor['id'] ? 'selected' : '' }}>
                                                {{ $vendedor['nombres'] }} {{ $vendedor['apellidos'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Método de Pago</label>
                                    <select class="form-select form-select-sm" id="metodoPagoFilter">
                                        <option value="">Todos</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="transferencia">Transferencia</option>
                                        <option value="credito">Crédito</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select form-select-sm" id="estadoFilter">
                                        <option value="todos">Todos</option>
                                        <option value="completada">Completada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2 d-flex align-items-end">
                                    <button class="btn btn-sm btn-info me-2" id="btnAplicarFiltros">
                                        <i class="fas fa-filter me-1"></i> Aplicar
                                    </button>
                                    <button class="btn btn-sm btn-secondary" id="btnLimpiarFiltros">
                                        <i class="fas fa-undo me-1"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards de resumen -->
            <div class="row mb-4" id="resumenCards">
                <div class="col-md-3 mb-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-1">Total Ventas</h6>
                            <h3 class="mb-0" id="totalVentas">{{ $data['resumen']['total_ventas'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card bg-success text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-1">Monto Total</h6>
                            <h3 class="mb-0" id="montoTotal">Q{{ number_format($data['resumen']['monto_total'] ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card bg-info text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-1">Promedio por Venta</h6>
                            <h3 class="mb-0" id="promedioVenta">Q{{ number_format($data['resumen']['promedio_venta'] ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card bg-warning">
                        <div class="card-body py-3">
                            <h6 class="mb-1">Venta Máxima</h6>
                            <h3 class="mb-0" id="ventaMaxima">Q{{ number_format($data['resumen']['venta_maxima'] ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Por método de pago -->
            <div class="row mb-4" id="metodosPagoContainer">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-2">
                            <h6 class="mb-0">Ventas por Método de Pago</h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="row" id="metodosPagoContent">
                                @if(!empty($data['resumen']['por_metodo_pago']))
                                    @foreach($data['resumen']['por_metodo_pago'] as $metodo => $info)
                                    <div class="col-md-3 mb-2 metodo-pago-item" data-metodo="{{ $metodo }}">
                                        <div class="border rounded p-3">
                                            <h6 class="text-capitalize mb-2">{{ $metodo }}</h6>
                                            <p class="mb-1">Cantidad: <span class="badge bg-info">{{ $info['cantidad'] }}</span></p>
                                            <strong>Q{{ number_format($info['total'], 2) }}</strong>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de ventas -->
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="ventasTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Método Pago</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th style="width: 80px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['ventas'] ?? [] as $venta)
                        @php
                            $searchText = strtolower(
                                ($venta['cliente']['nombre'] ?? '') . ' ' .
                                ($venta['vendedor']['nombres'] ?? '') . ' ' .
                                ($venta['vendedor']['apellidos'] ?? '') . ' ' .
                                ($venta['metodo_pago'] ?? '') . ' ' .
                                ($venta['estado'] ?? '')
                            );
                        @endphp
                        <tr data-id="{{ $venta['id'] }}"
                            data-fecha="{{ $venta['created_at'] }}"
                            data-cliente-id="{{ $venta['cliente']['id'] ?? '' }}"
                            data-cliente-nombre="{{ strtolower($venta['cliente']['nombre'] ?? '') }}"
                            data-vendedor-id="{{ $venta['vendedor']['id'] ?? '' }}"
                            data-vendedor-nombre="{{ strtolower(($venta['vendedor']['nombres'] ?? '') . ' ' . ($venta['vendedor']['apellidos'] ?? '')) }}"
                            data-metodo-pago="{{ $venta['metodo_pago'] ?? '' }}"
                            data-estado="{{ $venta['estado'] ?? '' }}"
                            data-total="{{ $venta['total'] ?? 0 }}"
                            data-search="{{ $searchText }}">
                            <td>#{{ $venta['id'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta['created_at'])->format('d/m/Y H:i') }}</td>
                            <td>{{ $venta['cliente']['nombre'] ?? 'N/A' }}</td>
                            <td>{{ $venta['vendedor']['nombres'] ?? '' }} {{ $venta['vendedor']['apellidos'] ?? '' }}</td>
                            <td>
                                <span class="badge bg-info text-capitalize">{{ $venta['metodo_pago'] ?? 'N/A' }}</span>
                            </td>
                            <td><strong>Q{{ number_format($venta['total'] ?? 0, 2) }}</strong></td>
                            <td>
                                @if(($venta['estado'] ?? '') == 'completada')
                                    <span class="badge bg-success">Completada</span>
                                @else
                                    <span class="badge bg-danger">{{ $venta['estado'] ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ventas.show', $venta['id']) }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-ventas-row">
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5>No hay ventas en el período seleccionado</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPeriodo = 'mes'; // Iniciar en mes para que haya datos visibles
    let fechaInicio = document.getElementById('fechaInicio').value;
    let fechaFin = document.getElementById('fechaFin').value;
    let clienteFilter = '';
    let vendedorFilter = '';
    let metodoPagoFilter = '';
    let estadoFilter = 'todos';
    let currentSearch = '';
    let isLoading = false;

    // Función para obtener fechas según período
    function getFechasPorPeriodo(periodo) {
        const hoy = new Date();
        const pad = d => d.toISOString().split('T')[0];

        switch(periodo) {
            case 'hoy':
                return { inicio: pad(hoy), fin: pad(hoy) };
            case 'semana':
                const inicioSemana = new Date(hoy);
                inicioSemana.setDate(hoy.getDate() - hoy.getDay() + (hoy.getDay() === 0 ? -6 : 1));
                return { inicio: pad(inicioSemana), fin: pad(hoy) };
            case 'mes':
                const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                return { inicio: pad(inicioMes), fin: pad(hoy) };
            default:
                return { 
                    inicio: document.getElementById('fechaInicio').value, 
                    fin: document.getElementById('fechaFin').value 
                };
        }
    }

    // Cargar ventas desde la API
    function cargarVentas() {
        if (isLoading) return;
        isLoading = true;

        const tbody = document.querySelector('#ventasTable tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Cargando ventas...</p>
                </td>
            </tr>
        `;

        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
        });

        if (clienteFilter)     params.append('cliente_id', clienteFilter);
        if (vendedorFilter)    params.append('vendedor_id', vendedorFilter);
        if (metodoPagoFilter)  params.append('metodo_pago', metodoPagoFilter);
        if (estadoFilter && estadoFilter !== 'todos') params.append('estado', estadoFilter);

        fetch(`{{ route('reportes.ventas') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(response => {
            if (response.ventas !== undefined) {
                // Respuesta con ventas directas (array o paginado)
                const ventas = response.ventas?.data ?? response.ventas ?? [];
                renderTabla(ventas);
                actualizarResumen(response.resumen ?? {}, ventas);
            } else {
                renderTabla([]);
                actualizarResumen({}, []);
            }
        })
        .catch(err => {
            console.error('Error cargando ventas:', err);
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Error al cargar datos</td></tr>`;
        })
        .finally(() => { isLoading = false; });
    }

    function renderTabla(ventas) {
        const tbody = document.querySelector('#ventasTable tbody');

        if (!ventas || ventas.length === 0) {
            tbody.innerHTML = `
                <tr id="no-ventas-row">
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3 d-block"></i>
                        <h5>No hay ventas en el período seleccionado</h5>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = ventas.map(venta => {
            const fecha = new Date(venta.created_at).toLocaleString('es-GT', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
            const estadoBadge = venta.estado === 'completada'
                ? '<span class="badge bg-success">Completada</span>'
                : `<span class="badge bg-danger">${venta.estado ?? 'N/A'}</span>`;

            return `
                <tr>
                    <td>#${venta.id}</td>
                    <td>${fecha}</td>
                    <td>${venta.cliente?.nombre ?? 'N/A'}</td>
                    <td>${(venta.vendedor?.nombres ?? '')} ${(venta.vendedor?.apellidos ?? '')}</td>
                    <td><span class="badge bg-info text-capitalize">${venta.metodo_pago ?? 'N/A'}</span></td>
                    <td><strong>Q${parseFloat(venta.total ?? 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</strong></td>
                    <td>${estadoBadge}</td>
                    <td>
                        <a href="/ventas/${venta.id}" class="btn btn-sm btn-info" target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>`;
        }).join('');

        // Aplicar búsqueda local si hay texto
        if (currentSearch) aplicarBusquedaLocal();
    }

    function actualizarResumen(resumen, ventas) {
        // Calcular desde los datos recibidos si no vienen en resumen
        const total = resumen.total_ventas ?? ventas.length;
        const monto = resumen.monto_total ?? ventas.reduce((s, v) => s + parseFloat(v.total ?? 0), 0);
        const promedio = total > 0 ? monto / total : 0;
        const maximo = resumen.venta_maxima ?? Math.max(...ventas.map(v => parseFloat(v.total ?? 0)), 0);

        document.getElementById('totalVentas').textContent = total;
        document.getElementById('montoTotal').textContent = 'Q' + monto.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        document.getElementById('promedioVenta').textContent = 'Q' + promedio.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        document.getElementById('ventaMaxima').textContent = 'Q' + maximo.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

        // Métodos de pago
        const metodos = resumen.por_metodo_pago ?? {};
        actualizarMetodosPago(metodos);
    }

    function actualizarMetodosPago(metodosPago) {
        const container = document.getElementById('metodosPagoContent');
        if (!container) return;

        const entries = typeof metodosPago === 'object' ? Object.entries(metodosPago) : [];

        if (entries.length === 0) {
            container.innerHTML = `<div class="col-12"><p class="text-muted text-center mb-0">No hay ventas en el período</p></div>`;
            return;
        }

        container.innerHTML = entries.map(([metodo, info]) => `
            <div class="col-md-3 mb-2">
                <div class="border rounded p-3">
                    <h6 class="text-capitalize mb-2">${metodo}</h6>
                    <p class="mb-1">Cantidad: <span class="badge bg-info">${info.cantidad ?? 0}</span></p>
                    <strong>Q${parseFloat(info.total ?? 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</strong>
                </div>
            </div>`).join('');
    }

    function aplicarBusquedaLocal() {
        const rows = document.querySelectorAll('#ventasTable tbody tr');
        rows.forEach(row => {
            const texto = row.textContent.toLowerCase();
            row.style.display = (!currentSearch || texto.includes(currentSearch)) ? '' : 'none';
        });
    }

    // ── Eventos ──────────────────────────────────────────

    // Botones de período
    document.querySelectorAll('.filter-periodo-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-periodo-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriodo = this.dataset.periodo;

            if (currentPeriodo !== 'personalizado') {
                const fechas = getFechasPorPeriodo(currentPeriodo);
                fechaInicio = fechas.inicio;
                fechaFin = fechas.fin;
                document.getElementById('fechaInicio').value = fechaInicio;
                document.getElementById('fechaFin').value = fechaFin;
                cargarVentas();
            }
        });
    });

    // Aplicar filtros avanzados
    document.getElementById('btnAplicarFiltros').addEventListener('click', function() {
        fechaInicio = document.getElementById('fechaInicio').value;
        fechaFin    = document.getElementById('fechaFin').value;
        clienteFilter     = document.getElementById('clienteFilter').value;
        vendedorFilter    = document.getElementById('vendedorFilter').value;
        metodoPagoFilter  = document.getElementById('metodoPagoFilter').value;
        estadoFilter      = document.getElementById('estadoFilter').value;
        cargarVentas();
    });

    // Búsqueda local en tiempo real (no requiere fetch)
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = this.value.toLowerCase().trim();
            aplicarBusquedaLocal();
        }, 300);
    });

    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        currentSearch = '';
        aplicarBusquedaLocal();
        this.previousElementSibling.focus();
    });

    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);

    // limpiarFiltros DENTRO del scope
    window.limpiarFiltros = function() {
        document.getElementById('clienteFilter').value     = '';
        document.getElementById('vendedorFilter').value    = '';
        document.getElementById('metodoPagoFilter').value  = '';
        document.getElementById('estadoFilter').value      = 'todos';
        document.getElementById('searchInput').value       = '';
        clienteFilter = vendedorFilter = metodoPagoFilter = currentSearch = '';
        estadoFilter  = 'todos';

        // Activar botón "Mes" y recargar
        document.querySelector('.filter-periodo-btn[data-periodo="mes"]').click();
    };

    // Carga inicial con "Este mes"
    document.querySelector('.filter-periodo-btn[data-periodo="mes"]').click();
});

function exportarReporte() {
    alert('Funcionalidad de exportación próximamente');
}
</script>
@endpush

@push('styles')
<style>
.filter-periodo-btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.card {
    transition: all 0.3s ease;
}

.card-header {
    padding: 0.75rem 1rem;
}

.form-label {
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    padding: 0.25rem 0.75rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    #resumenCards .card-body {
        padding: 0.75rem;
    }
    
    #resumenCards h3 {
        font-size: 1.25rem;
    }
}
</style>
@endpush