@extends('layouts.app')

@section('title', 'Rendimiento de Vendedores')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Rendimiento Vendedores</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-user-tie me-2"></i>Rendimiento de Vendedores
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
                               placeholder="Buscar vendedor...">
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
                <table class="table table-hover table-striped" id="vendedoresTable">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th>#</th>
                            <th>Vendedor</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Ventas Realizadas</th>
                            <th>Total Vendido</th>
                            <th>Promedio por Venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['vendedores'] ?? [] as $index => $vendedor)
                        @php
                            $totalVentas = $vendedor['total_ventas'] ?? 0;
                            $ventasCount = $vendedor['ventas_count'] ?? 0;
                            $promedio = $ventasCount > 0 ? $totalVentas / $ventasCount : 0;
                            $nombreCompleto = ($vendedor['nombres'] ?? '') . ' ' . ($vendedor['apellidos'] ?? '');
                            $searchText = strtolower(
                                $nombreCompleto . ' ' .
                                ($vendedor['email'] ?? '') . ' ' .
                                ($vendedor['username'] ?? '')
                            );
                        @endphp
                        <tr data-vendedor-id="{{ $vendedor['id'] ?? '' }}"
                            data-nombre="{{ strtolower($nombreCompleto) }}"
                            data-email="{{ strtolower($vendedor['email'] ?? '') }}"
                            data-username="{{ strtolower($vendedor['username'] ?? '') }}"
                            data-estado="{{ $vendedor['estado'] ?? '' }}"
                            data-ventas="{{ $ventasCount }}"
                            data-total="{{ $totalVentas }}"
                            data-search="{{ $searchText }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $vendedor['nombres'] ?? '' }} {{ $vendedor['apellidos'] ?? '' }}</strong>
                                <br>
                                <small class="text-muted">{{ $vendedor['username'] ?? '' }}</small>
                                @php $rol = $vendedor['rol'] ?? '' @endphp
                                <span class="badge bg-{{ $rol === 'vendedor' ? 'primary' : ($rol === 'administrador' ? 'danger' : 'secondary') }} mt-1">
                                    {{ ucfirst($rol) }}
                                </span>
                            </td>
                            <td>{{ $vendedor['email'] ?? 'N/A' }}</td>
                            <td>{{ $vendedor['telefono'] ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info ventas-badge">{{ $ventasCount }}</span>
                            </td>
                            <td class="total-vendido"><strong>Q{{ number_format($totalVentas, 2) }}</strong></td>
                            <td class="promedio">Q{{ number_format($promedio, 2) }}</td>
                            <td>
                                @if(($vendedor['estado'] ?? '') == 'activo')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('usuarios.show', $vendedor['id']) }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-vendedores-row">
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                                <h5>No hay datos de vendedores en el período seleccionado</h5>
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
    let fechaInicio   = document.getElementById('fechaInicio').value;
    let fechaFin      = document.getElementById('fechaFin').value;
    let estadoFilter  = 'todos';
    let currentSearch = '';
    let isLoading     = false;

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

    function cargarVendedores() {
        if (isLoading) return;
        isLoading = true;

        const tbody = document.querySelector('#vendedoresTable tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center py-4">
                    <div class="spinner-border text-secondary" role="status"></div>
                    <p class="mt-2 text-muted">Cargando vendedores...</p>
                </td>
            </tr>`;

        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin:    fechaFin
        });

        fetch(`{{ route('reportes.rendimiento-vendedores') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(response => {
            renderTabla(response.vendedores ?? []);
        })
        .catch(err => {
            console.error('Error:', err);
            tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger py-4">Error al cargar datos</td></tr>`;
        })
        .finally(() => { isLoading = false; });
    }

    function renderTabla(vendedores) {
        const tbody = document.querySelector('#vendedoresTable tbody');

        if (!vendedores || vendedores.length === 0) {
            tbody.innerHTML = `
                <tr id="no-vendedores-row">
                    <td colspan="9" class="text-center py-4">
                        <i class="fas fa-user-tie fa-3x text-muted mb-3 d-block"></i>
                        <h5>No hay datos de vendedores en el período seleccionado</h5>
                    </td>
                </tr>`;
            return;
        }

        // Filtro de estado local
        let filtrados = vendedores.filter(v => {
            return estadoFilter === 'todos' || (v.estado ?? '') === estadoFilter;
        });

        // Ordenar por total vendido
        filtrados.sort((a, b) => (b.total_ventas ?? 0) - (a.total_ventas ?? 0));

        tbody.innerHTML = filtrados.map((vendedor, index) => {
            const totalVentas = parseFloat(vendedor.total_ventas ?? 0);
            const ventasCount = vendedor.ventas_count ?? 0;
            const promedio    = ventasCount > 0 ? totalVentas / ventasCount : 0;
            const rol         = vendedor.rol ?? '';
            const rolColor    = rol === 'vendedor' ? 'primary' : (rol === 'administrador' ? 'danger' : 'secondary');
            const estadoBadge = (vendedor.estado ?? '') === 'activo'
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';

            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <strong>${vendedor.nombres ?? ''} ${vendedor.apellidos ?? ''}</strong>
                        <br>
                        <small class="text-muted">${vendedor.username ?? ''}</small>
                        <span class="badge bg-${rolColor} mt-1">${rol.charAt(0).toUpperCase() + rol.slice(1)}</span>
                    </td>
                    <td>${vendedor.email ?? 'N/A'}</td>
                    <td>${vendedor.telefono ?? 'N/A'}</td>
                    <td><span class="badge bg-info">${ventasCount}</span></td>
                    <td><strong>Q${totalVentas.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</strong></td>
                    <td>Q${promedio.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
                    <td>${estadoBadge}</td>
                    <td>
                        <a href="/usuarios/${vendedor.id}" class="btn btn-sm btn-info" target="_blank">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>`;
        }).join('');

        if (currentSearch) aplicarBusquedaLocal();
    }

    function aplicarBusquedaLocal() {
        document.querySelectorAll('#vendedoresTable tbody tr').forEach(row => {
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
                cargarVendedores();
            }
        });
    });

    document.getElementById('btnAplicarFiltros').addEventListener('click', function() {
        fechaInicio  = document.getElementById('fechaInicio').value;
        fechaFin     = document.getElementById('fechaFin').value;
        estadoFilter = document.getElementById('estadoFilter').value;
        cargarVendedores();
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
        document.getElementById('estadoFilter').value = 'todos';
        document.getElementById('searchInput').value  = '';
        estadoFilter = 'todos'; currentSearch = '';
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