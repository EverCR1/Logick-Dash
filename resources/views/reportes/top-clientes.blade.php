@extends('layouts.app')

@section('title', 'Top Clientes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Top Clientes</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-trophy me-2"></i>Top Clientes
            </h5>
            <div>
                <button class="btn btn-success" onclick="exportarReporte()" title="Exportar reporte">
                    <i class="fas fa-file-excel me-2"></i> Exportar
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
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
                               placeholder="Buscar cliente, NIT...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filtros avanzados -->
            <div class="row mb-3">
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
                                    <label class="form-label">Límite</label>
                                    <select class="form-select form-select-sm" id="limiteFilter">
                                        <option value="5">5 clientes</option>
                                        <option value="10" selected>10 clientes</option>
                                        <option value="20">20 clientes</option>
                                        <option value="50">50 clientes</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Tipo</label>
                                    <select class="form-select form-select-sm" id="tipoFilter">
                                        <option value="">Todos</option>
                                        <option value="natural">Natural</option>
                                        <option value="juridico">Jurídico</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select form-select-sm" id="estadoFilter">
                                        <option value="todos">Todos</option>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
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

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="clientesTable">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>NIT</th>
                            <th>Tipo</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Compras Realizadas</th>
                            <th>Total Comprado</th>
                            <th>Promedio por Compra</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['clientes'] ?? [] as $index => $cliente)
                        @php
                            $totalComprado = $cliente['total_comprado'] ?? 0;
                            $comprasCount = $cliente['ventas_count'] ?? 0;
                            $promedio = $comprasCount > 0 ? $totalComprado / $comprasCount : 0;
                            $searchText = strtolower(
                                ($cliente['nombre'] ?? '') . ' ' .
                                ($cliente['nit'] ?? '') . ' ' .
                                ($cliente['email'] ?? '') . ' ' .
                                ($cliente['telefono'] ?? '')
                            );
                        @endphp
                        <tr data-cliente-id="{{ $cliente['id'] ?? '' }}"
                            data-nombre="{{ strtolower($cliente['nombre'] ?? '') }}"
                            data-nit="{{ strtolower($cliente['nit'] ?? '') }}"
                            data-tipo="{{ $cliente['tipo'] ?? '' }}"
                            data-estado="{{ $cliente['estado'] ?? '' }}"
                            data-compras="{{ $comprasCount }}"
                            data-total="{{ $totalComprado }}"
                            data-search="{{ $searchText }}">
                            <td>
                                @if($index < 3)
                                    <i class="fas fa-crown text-warning"></i>
                                @endif
                                {{ $index + 1 }}
                            </td>
                            <td>
                                <strong>{{ $cliente['nombre'] ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ $cliente['nit'] ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ ($cliente['tipo'] ?? '') == 'natural' ? 'primary' : 'secondary' }}">
                                    {{ $cliente['tipo'] ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $cliente['telefono'] ?? 'N/A' }}</td>
                            <td>{{ $cliente['email'] ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info compras-badge">{{ $comprasCount }}</span>
                            </td>
                            <td class="total-comprado"><strong>Q{{ number_format($totalComprado, 2) }}</strong></td>
                            <td class="promedio">Q{{ number_format($promedio, 2) }}</td>
                            <td>
                                <a href="{{ route('clientes.show', $cliente['id']) }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-clientes-row">
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>No hay datos de clientes en el período seleccionado</h5>
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
    let currentPeriodo = 'mes';
    let fechaInicio    = document.getElementById('fechaInicio').value;
    let fechaFin       = document.getElementById('fechaFin').value;
    let limiteFilter   = '10';
    let tipoFilter     = '';
    let estadoFilter   = 'todos';
    let currentSearch  = '';
    let isLoading      = false;

    function getFechasPorPeriodo(periodo) {
        const hoy = new Date();
        const pad = d => d.toISOString().split('T')[0];
        switch(periodo) {
            case 'hoy':
                return { inicio: pad(hoy), fin: pad(hoy) };
            case 'semana':
                const s = new Date(hoy);
                s.setDate(hoy.getDate() - hoy.getDay() + (hoy.getDay() === 0 ? -6 : 1));
                return { inicio: pad(s), fin: pad(hoy) };
            case 'mes':
                return { inicio: pad(new Date(hoy.getFullYear(), hoy.getMonth(), 1)), fin: pad(hoy) };
            default:
                return {
                    inicio: document.getElementById('fechaInicio').value,
                    fin:    document.getElementById('fechaFin').value
                };
        }
    }

    function cargarClientes() {
        if (isLoading) return;
        isLoading = true;

        const tbody = document.querySelector('#clientesTable tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-4">
                    <div class="spinner-border text-info" role="status"></div>
                    <p class="mt-2 text-muted">Cargando clientes...</p>
                </td>
            </tr>`;

        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin:    fechaFin,
            limite:       limiteFilter
        });

        fetch(`{{ route('reportes.top-clientes') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(response => {
            renderTabla(response.clientes ?? []);
        })
        .catch(err => {
            console.error('Error:', err);
            tbody.innerHTML = `<tr><td colspan="10" class="text-center text-danger py-4">Error al cargar datos</td></tr>`;
        })
        .finally(() => { isLoading = false; });
    }

    function renderTabla(clientes) {
        const tbody = document.querySelector('#clientesTable tbody');

        if (!clientes || clientes.length === 0) {
            tbody.innerHTML = `
                <tr id="no-clientes-row">
                    <td colspan="10" class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                        <h5>No hay datos de clientes en el período seleccionado</h5>
                    </td>
                </tr>`;
            return;
        }

        // Aplicar filtros locales antes de renderizar
        let filtrados = clientes.filter(c => {
            const tipoMatch  = !tipoFilter  || (c.tipo ?? '') === tipoFilter;
            const estadoMatch = estadoFilter === 'todos' || (c.estado ?? '') === estadoFilter;
            return tipoMatch && estadoMatch && (c.ventas_count ?? 0) > 0;
        });

        // Ordenar por total comprado
        filtrados.sort((a, b) => (b.total_comprado ?? 0) - (a.total_comprado ?? 0));

        // Aplicar límite
        filtrados = filtrados.slice(0, parseInt(limiteFilter));

        tbody.innerHTML = filtrados.map((cliente, index) => {
            const totalComprado = parseFloat(cliente.total_comprado ?? 0);
            const comprasCount  = cliente.ventas_count ?? 0;
            const promedio      = comprasCount > 0 ? totalComprado / comprasCount : 0;
            const corona        = index < 3 ? '<i class="fas fa-crown text-warning"></i> ' : '';
            const tipoBadge     = `<span class="badge bg-${(cliente.tipo ?? '') === 'natural' ? 'primary' : 'secondary'}">${cliente.tipo ?? 'N/A'}</span>`;

            return `
                <tr>
                    <td>${corona}${index + 1}</td>
                    <td><strong>${cliente.nombre ?? 'N/A'}</strong></td>
                    <td>${cliente.nit ?? 'N/A'}</td>
                    <td>${tipoBadge}</td>
                    <td>${cliente.telefono ?? 'N/A'}</td>
                    <td>${cliente.email ?? 'N/A'}</td>
                    <td><span class="badge bg-info">${comprasCount}</span></td>
                    <td><strong>Q${totalComprado.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</strong></td>
                    <td>Q${promedio.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                    <td>
                        <a href="/clientes/${cliente.id}" class="btn btn-sm btn-info" target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>`;
        }).join('');

        if (currentSearch) aplicarBusquedaLocal();
    }

    function aplicarBusquedaLocal() {
        document.querySelectorAll('#clientesTable tbody tr').forEach(row => {
            const texto = row.textContent.toLowerCase();
            row.style.display = (!currentSearch || texto.includes(currentSearch)) ? '' : 'none';
        });
    }

    // ── Eventos ──────────────────────────────────────────

    document.querySelectorAll('.filter-periodo-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-periodo-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriodo = this.dataset.periodo;

            if (currentPeriodo !== 'personalizado') {
                const fechas = getFechasPorPeriodo(currentPeriodo);
                fechaInicio = fechas.inicio;
                fechaFin    = fechas.fin;
                document.getElementById('fechaInicio').value = fechaInicio;
                document.getElementById('fechaFin').value    = fechaFin;
                cargarClientes();
            }
        });
    });

    document.getElementById('btnAplicarFiltros').addEventListener('click', function() {
        fechaInicio  = document.getElementById('fechaInicio').value;
        fechaFin     = document.getElementById('fechaFin').value;
        limiteFilter = document.getElementById('limiteFilter').value;
        tipoFilter   = document.getElementById('tipoFilter').value;
        estadoFilter = document.getElementById('estadoFilter').value;
        cargarClientes();
    });

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
    });

    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);

    window.limpiarFiltros = function() {
        document.getElementById('limiteFilter').value = '10';
        document.getElementById('tipoFilter').value   = '';
        document.getElementById('estadoFilter').value = 'todos';
        document.getElementById('searchInput').value  = '';
        limiteFilter = '10'; tipoFilter = ''; estadoFilter = 'todos'; currentSearch = '';
        document.querySelector('.filter-periodo-btn[data-periodo="mes"]').click();
    };

    // Carga inicial
    document.querySelector('.filter-periodo-btn[data-periodo="mes"]').click();
});

function exportarReporte() {
    alert('Funcionalidad de exportación próximamente');
}
</script>
@endpush