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
    let currentPeriodo = 'hoy';
    let fechaInicio = document.getElementById('fechaInicio').value;
    let fechaFin = document.getElementById('fechaFin').value;
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
        const tbody = document.querySelector('#vendedoresTable tbody');
        let rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows = rows.filter(row => !row.id || row.id !== 'no-vendedores-row');
        
        let visibleRows = [];
        
        rows.forEach(row => {
            const estado = row.dataset.estado;
            const searchData = row.dataset.search || '';
            
            // Filtro de estado
            const estadoMatch = estadoFilter === 'todos' || !estadoFilter || estado === estadoFilter;
            
            // Filtro de búsqueda
            const searchMatch = !currentSearch || searchData.includes(currentSearch);
            
            if (estadoMatch && searchMatch) {
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });
        
        // Ordenar por total vendido (mayor a menor)
        visibleRows.sort((a, b) => {
            const totalA = parseFloat(a.dataset.total) || 0;
            const totalB = parseFloat(b.dataset.total) || 0;
            return totalB - totalA;
        });
        
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
                firstCell.textContent = index + 1;
            }
            tbody.appendChild(row);
        });
        
        hiddenRows.forEach(row => tbody.appendChild(row));
        
        mostrarMensajeNoResultados(visibleRows.length, rows.length);
    }
    
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const tbody = document.querySelector('#vendedoresTable tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron vendedores</h5>
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

// Hacer la función global
window.limpiarFiltros = function() {
    document.querySelector('.filter-periodo-btn[data-periodo="hoy"]').click();
    document.getElementById('estadoFilter').value = 'todos';
    document.getElementById('searchInput').value = '';
    
    // Actualizar variables globales
    estadoFilter = 'todos';
    currentSearch = '';
    
    // Llamar a aplicarFiltros (necesitamos acceder a la función)
    // Como está dentro del DOMContentLoaded, necesitamos disparar un evento o recargar
    document.getElementById('btnAplicarFiltros').click();
};

function exportarReporte() {
    alert('Funcionalidad de exportación próximamente');
}
</script>
@endpush