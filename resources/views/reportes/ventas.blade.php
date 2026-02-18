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
    let currentPeriodo = 'hoy';
    let fechaInicio = document.getElementById('fechaInicio').value;
    let fechaFin = document.getElementById('fechaFin').value;
    let clienteFilter = document.getElementById('clienteFilter').value;
    let vendedorFilter = document.getElementById('vendedorFilter').value;
    let metodoPagoFilter = document.getElementById('metodoPagoFilter').value;
    let estadoFilter = document.getElementById('estadoFilter').value;
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
    
    // Función para actualizar fechas según período
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
    
    // Botón aplicar filtros
    document.getElementById('btnAplicarFiltros').addEventListener('click', function() {
        actualizarValoresFiltros();
        aplicarFiltros();
    });
    
    // Botón limpiar filtros
    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);
    
    // Búsqueda en tiempo real
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = this.value.toLowerCase().trim();
            aplicarFiltros();
        }, 300);
    });
    
    // Botón limpiar búsqueda
    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        currentSearch = '';
        aplicarFiltros();
        document.getElementById('searchInput').focus();
    });
    
    // Función para actualizar valores de filtros
    function actualizarValoresFiltros() {
        fechaInicio = document.getElementById('fechaInicio').value;
        fechaFin = document.getElementById('fechaFin').value;
        clienteFilter = document.getElementById('clienteFilter').value;
        vendedorFilter = document.getElementById('vendedorFilter').value;
        metodoPagoFilter = document.getElementById('metodoPagoFilter').value;
        estadoFilter = document.getElementById('estadoFilter').value;
    }
    
    // Función para aplicar todos los filtros
    function aplicarFiltros() {
        const tbody = document.querySelector('#ventasTable tbody');
        let rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Excluir fila de no resultados
        rows = rows.filter(row => !row.id || row.id !== 'no-ventas-row');
        
        let totalVentas = 0;
        let montoTotal = 0;
        let ventaMaxima = 0;
        let metodosPago = {};
        
        rows.forEach(row => {
            const fecha = row.dataset.fecha;
            const clienteId = row.dataset.clienteId;
            const vendedorId = row.dataset.vendedorId;
            const metodoPago = row.dataset.metodoPago;
            const estado = row.dataset.estado;
            const total = parseFloat(row.dataset.total) || 0;
            const searchData = row.dataset.search || '';
            
            // Filtrar por fecha
            let fechaMatch = true;
            if (fechaInicio && fechaFin) {
                const rowFecha = new Date(fecha).toISOString().split('T')[0];
                fechaMatch = rowFecha >= fechaInicio && rowFecha <= fechaFin;
            }
            
            // Filtro de cliente
            const clienteMatch = !clienteFilter || clienteId === clienteFilter;
            
            // Filtro de vendedor
            const vendedorMatch = !vendedorFilter || vendedorId === vendedorFilter;
            
            // Filtro de método de pago
            const metodoMatch = !metodoPagoFilter || metodoPago === metodoPagoFilter;
            
            // Filtro de estado
            const estadoMatch = estadoFilter === 'todos' || !estadoFilter || estado === estadoFilter;
            
            // Filtro de búsqueda
            const searchMatch = !currentSearch || searchData.includes(currentSearch);
            
            const matches = fechaMatch && clienteMatch && vendedorMatch && metodoMatch && estadoMatch && searchMatch;
            
            if (matches) {
                row.style.display = '';
                totalVentas++;
                montoTotal += total;
                if (total > ventaMaxima) ventaMaxima = total;
                
                // Contar por método de pago
                if (metodoPago) {
                    if (!metodosPago[metodoPago]) {
                        metodosPago[metodoPago] = { cantidad: 0, total: 0 };
                    }
                    metodosPago[metodoPago].cantidad++;
                    metodosPago[metodoPago].total += total;
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Actualizar resumen
        document.getElementById('totalVentas').textContent = totalVentas;
        document.getElementById('montoTotal').textContent = 'Q' + montoTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        document.getElementById('promedioVenta').textContent = totalVentas > 0 ? 'Q' + (montoTotal / totalVentas).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') : 'Q0.00';
        document.getElementById('ventaMaxima').textContent = 'Q' + ventaMaxima.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        
        // Actualizar métodos de pago
        actualizarMetodosPago(metodosPago);
        
        // Mostrar mensaje si no hay resultados
        mostrarMensajeNoResultados(totalVentas, rows.length);
    }
    
    // Función para actualizar métodos de pago
    function actualizarMetodosPago(metodosPago) {
        const container = document.getElementById('metodosPagoContent');
        if (!container) return;
        
        if (Object.keys(metodosPago).length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <p class="text-muted text-center mb-0">No hay ventas en el período seleccionado</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        for (const [metodo, info] of Object.entries(metodosPago)) {
            html += `
                <div class="col-md-3 mb-2">
                    <div class="border rounded p-3">
                        <h6 class="text-capitalize mb-2">${metodo}</h6>
                        <p class="mb-1">Cantidad: <span class="badge bg-info">${info.cantidad}</span></p>
                        <strong>Q${info.total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</strong>
                    </div>
                </div>
            `;
        }
        container.innerHTML = html;
    }
    
    // Función para mostrar mensaje cuando no hay resultados
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const tbody = document.querySelector('#ventasTable tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron ventas</h5>
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

    // Función para limpiar filtros
    window.limpiarFiltros = function() {
        // Resetear período a hoy
        document.querySelector('.filter-periodo-btn[data-periodo="hoy"]').click();
        
        // Limpiar selects
        document.getElementById('clienteFilter').value = '';
        document.getElementById('vendedorFilter').value = '';
        document.getElementById('metodoPagoFilter').value = '';
        document.getElementById('estadoFilter').value = 'todos';
        
        // Limpiar búsqueda
        document.getElementById('searchInput').value = '';
        currentSearch = '';
        
        actualizarValoresFiltros();
        aplicarFiltros();
    };

// Función para exportar (placeholder)
function exportarReporte() {
    alert('Funcionalidad de exportación próximamente');
    // window.location.href = '#';
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