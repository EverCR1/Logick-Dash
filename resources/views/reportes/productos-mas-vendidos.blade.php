@extends('layouts.app')

@section('title', 'Productos Más Vendidos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Productos Más Vendidos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-bar me-2"></i>Productos Más Vendidos
            </h5>
            <div>
                <button class="btn btn-success" onclick="exportarReporte()" title="Exportar reporte">
                    <i class="fas fa-file-excel me-2"></i> Exportar
                </button>
            </div>
        </div>
        <div class="card-body">
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
                               placeholder="Buscar producto, SKU, categoría...">
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
                                        <option value="10">10 productos</option>
                                        <option value="20" selected>20 productos</option>
                                        <option value="50">50 productos</option>
                                        <option value="100">100 productos</option>
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
                <table class="table table-hover table-striped" id="productosTable">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>SKU</th>
                            <th>Categoría</th>
                            <th>Veces Vendido</th>
                            <th>Unidades Vendidas</th>
                            <th>Total Vendido</th>
                            <th>Stock Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['productos'] ?? [] as $index => $item)
                        @php
                            $producto = $item['producto'] ?? [];
                            $categoriasTexto = '';
                            if (!empty($producto['categorias'])) {
                                $categoriasTexto = implode(' ', array_column($producto['categorias'], 'nombre'));
                            }
                            $searchText = strtolower(
                                ($producto['nombre'] ?? '') . ' ' .
                                ($producto['sku'] ?? '') . ' ' .
                                $categoriasTexto
                            );
                        @endphp
                        <tr data-index="{{ $index }}"
                            data-producto-id="{{ $producto['id'] ?? '' }}"
                            data-producto-nombre="{{ strtolower($producto['nombre'] ?? '') }}"
                            data-sku="{{ strtolower($producto['sku'] ?? '') }}"
                            data-categoria="{{ strtolower($categoriasTexto) }}"
                            data-veces-vendido="{{ $item['veces_vendido'] ?? 0 }}"
                            data-unidades="{{ $item['total_unidades'] ?? 0 }}"
                            data-total="{{ $item['total_vendido'] ?? 0 }}"
                            data-search="{{ $searchText }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $producto['nombre'] ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">{{ $producto['marca'] ?? '' }}</small>
                            </td>
                            <td>{{ $producto['sku'] ?? 'N/A' }}</td>
                            <td>
                                @if(!empty($producto['categorias']))
                                    @foreach($producto['categorias'] as $cat)
                                        <span class="badge bg-secondary">{{ $cat['nombre'] ?? '' }}</span>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $item['veces_vendido'] ?? 0 }}</span>
                            </td>
                            <td>{{ $item['total_unidades'] ?? 0 }}</td>
                            <td><strong>Q{{ number_format($item['total_vendido'] ?? 0, 2) }}</strong></td>
                            <td>
                                @php
                                    $stock = $producto['stock'] ?? 0;
                                    $stockMinimo = $producto['stock_minimo'] ?? 1;
                                    $stockClass = $stock <= 0 ? 'secondary' : ($stock <= $stockMinimo ? 'danger' : 'success');
                                @endphp
                                <span class="badge bg-{{ $stockClass }}">{{ $stock }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-productos-row">
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <h5>No hay datos de ventas en el período seleccionado</h5>
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
    let currentSearch = '';
    
    // Inicializar
    actualizarFechasPorPeriodo('hoy');
    
    // Eventos para filtros de período
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
        const tbody = document.querySelector('#productosTable tbody');
        let rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows = rows.filter(row => !row.id || row.id !== 'no-productos-row');
        
        let visibleRows = [];
        
        rows.forEach(row => {
            const rowFechaInicio = new Date(fechaInicio);
            const rowFechaFin = new Date(fechaFin);
            const searchData = row.dataset.search || '';
            
            const searchMatch = !currentSearch || searchData.includes(currentSearch);
            
            if (searchMatch) {
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
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
                firstCell.textContent = index + 1;
            }
            tbody.appendChild(row);
        });
        
        hiddenRows.forEach(row => tbody.appendChild(row));
        
        mostrarMensajeNoResultados(visibleRows.length, rows.length);
    }
    
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const tbody = document.querySelector('#productosTable tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron productos</h5>
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
        document.getElementById('limiteFilter').value = '20';
        document.getElementById('searchInput').value = '';
        currentSearch = '';
        aplicarFiltros();
    };

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
</style>
@endpush