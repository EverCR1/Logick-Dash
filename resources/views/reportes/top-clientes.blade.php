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
    let currentPeriodo = 'hoy';
    let fechaInicio = document.getElementById('fechaInicio').value;
    let fechaFin = document.getElementById('fechaFin').value;
    let limiteFilter = document.getElementById('limiteFilter').value;
    let tipoFilter = document.getElementById('tipoFilter').value;
    let estadoFilter = document.getElementById('estadoFilter').value;
    let currentSearch = '';
    
    actualizarFechasPorPeriodo('hoy');
    
    document.querySelectorAll('.filter-periodo-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-periodo-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriodo = this.dataset.periodo;
            
            if (currentPeriodo !== 'personalizado') {
                actualizarFechasPorPeriodo(currentPeriodo);
                aplicarFiltros();
            }
        });
    });
    
    function actualizarFechasPorPeriodo(periodo) {
        const hoy = new Date();
        let fechaInicioInput = document.getElementById('fechaInicio');
        let fechaFinInput = document.getElementById('fechaFin');
        
        switch(periodo) {
            case 'hoy':
                fechaInicioInput.value = hoy.toISOString().split('T')[0];
                fechaFinInput.value = hoy.toISOString().split('T')[0];
                break;
            case 'semana':
                const inicioSemana = new Date(hoy);
                inicioSemana.setDate(hoy.getDate() - hoy.getDay() + (hoy.getDay() === 0 ? -6 : 1));
                fechaInicioInput.value = inicioSemana.toISOString().split('T')[0];
                fechaFinInput.value = hoy.toISOString().split('T')[0];
                break;
            case 'mes':
                const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
                fechaInicioInput.value = inicioMes.toISOString().split('T')[0];
                fechaFinInput.value = hoy.toISOString().split('T')[0];
                break;
        }
        
        fechaInicio = fechaInicioInput.value;
        fechaFin = fechaFinInput.value;
    }
    
    document.getElementById('btnAplicarFiltros').addEventListener('click', function() {
        fechaInicio = document.getElementById('fechaInicio').value;
        fechaFin = document.getElementById('fechaFin').value;
        limiteFilter = document.getElementById('limiteFilter').value;
        tipoFilter = document.getElementById('tipoFilter').value;
        estadoFilter = document.getElementById('estadoFilter').value;
        aplicarFiltros();
    });
    
    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);
    
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = this.value.toLowerCase().trim();
            aplicarFiltros();
        }, 300);
    });
    
    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        currentSearch = '';
        aplicarFiltros();
        document.getElementById('searchInput').focus();
    });
    
    function aplicarFiltros() {
        const tbody = document.querySelector('#clientesTable tbody');
        let rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows = rows.filter(row => !row.id || row.id !== 'no-clientes-row');
        
        let visibleRows = [];
        
        rows.forEach(row => {
            const tipo = row.dataset.tipo;
            const estado = row.dataset.estado;
            const compras = parseInt(row.dataset.compras) || 0;
            const total = parseFloat(row.dataset.total) || 0;
            const searchData = row.dataset.search || '';
            
            // Filtro de tipo
            const tipoMatch = !tipoFilter || tipo === tipoFilter;
            
            // Filtro de estado
            const estadoMatch = estadoFilter === 'todos' || !estadoFilter || estado === estadoFilter;
            
            // Filtro de compras (solo mostrar clientes con compras)
            const comprasMatch = compras > 0;
            
            // Filtro de búsqueda
            const searchMatch = !currentSearch || searchData.includes(currentSearch);
            
            if (tipoMatch && estadoMatch && comprasMatch && searchMatch) {
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });
        
        // Ordenar por total comprado (mayor a menor)
        visibleRows.sort((a, b) => {
            const totalA = parseFloat(a.dataset.total) || 0;
            const totalB = parseFloat(b.dataset.total) || 0;
            return totalB - totalA;
        });
        
        // Aplicar límite
        if (limiteFilter > 0) {
            visibleRows = visibleRows.slice(0, parseInt(limiteFilter));
        }
        
        // Reordenar tabla
        const allRows = rows.filter(row => visibleRows.includes(row));
        const hiddenRows = rows.filter(row => !visibleRows.includes(row));
        
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        
        visibleRows.forEach((row, index) => {
            row.style.display = '';
            const firstCell = row.querySelector('td:first-child');
            if (firstCell) {
                if (index < 3) {
                    firstCell.innerHTML = `<i class="fas fa-crown text-warning"></i> ${index + 1}`;
                } else {
                    firstCell.textContent = index + 1;
                }
            }
            tbody.appendChild(row);
        });
        
        hiddenRows.forEach(row => tbody.appendChild(row));
        
        mostrarMensajeNoResultados(visibleRows.length, rows.length);
    }
    
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const tbody = document.querySelector('#clientesTable tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="10" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron clientes</h5>
                        <p class="text-muted mb-3">Intenta con otros filtros</p>
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
    
    
});

window.limpiarFiltros = function() {
        document.querySelector('.filter-periodo-btn[data-periodo="hoy"]').click();
        document.getElementById('limiteFilter').value = '10';
        document.getElementById('tipoFilter').value = '';
        document.getElementById('estadoFilter').value = 'todos';
        document.getElementById('searchInput').value = '';
        currentSearch = '';
        
        tipoFilter = '';
        estadoFilter = 'todos';
        
        aplicarFiltros();
    };

function exportarReporte() {
    alert('Funcionalidad de exportación próximamente');
}
</script>
@endpush