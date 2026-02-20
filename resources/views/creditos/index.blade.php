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
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-1">Total Créditos</h6>
                            <h4 class="mb-0">{{ $estadisticas['total_creditos'] ?? 0 }}</h4>
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
                            <h4 class="mb-0">{{ $estadisticas['activos'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock fa-2x text-success"></i>
                        </div>
                    </div>
                    <small class="text-muted">
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
                            <h4 class="mb-0">{{ $estadisticas['abonados'] ?? 0 }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                        </div>
                    </div>
                    <small class="text-muted">
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
                            <h4 class="mb-0">Q{{ number_format($estadisticas['total_recuperado'] ?? 0, 2) }}</h4>
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
            <div class="d-flex gap-2">
                <a href="{{ route('creditos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nuevo Crédito
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros y búsqueda en tiempo real -->
            <div class="row mb-4">
                <div class="col-md-7">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">
                            Todos
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm filter-btn" data-filter="activo">
                            Activos
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm filter-btn" data-filter="abonado">
                            Abonados
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm filter-btn" data-filter="pagado">
                            Pagados
                        </button>
                    </div>
                    
                    <div class="btn-group ms-2" role="group">
                        <button type="button" class="btn btn-outline-info btn-sm sort-btn active" data-sort="fecha_desc">
                            <i class="fas fa-sort-amount-down me-1"></i>Más recientes
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
                // Extraer datos de manera segura
                $creditosData = [];
                $creditosLinks = [];
                $creditosMeta = [];
                
                if (isset($creditos['data'])) {
                    $creditosData = $creditos['data'];
                } elseif (isset($creditos) && is_array($creditos)) {
                    $creditosData = $creditos;
                }
                
                if (isset($creditos['links']) && is_array($creditos['links'])) {
                    $creditosLinks = $creditos['links'];
                }
                
                if (isset($creditos['meta']) && is_array($creditos['meta'])) {
                    $creditosMeta = $creditos['meta'];
                }
            @endphp

            @if(empty($creditosData))
                <div class="text-center py-5">
                    <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay créditos registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer crédito</p>
                    <a href="{{ route('creditos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Crédito
                    </a>
                </div>
            @else
                <div class="table-responsive">
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
                                $porcentajePagado = $credito['capital'] > 0 ? 
                                    (($credito['capital'] - $credito['capital_restante']) / $credito['capital']) * 100 : 0;
                                
                                $estadoColors = [
                                    'activo' => 'danger',
                                    'abonado' => 'warning',
                                    'pagado' => 'success'
                                ];
                                
                                $estadoLabels = [
                                    'activo' => 'Activo',
                                    'abonado' => 'Abonado',
                                    'pagado' => 'Pagado'
                                ];

                                // Preparar datos para búsqueda - AHORA EN ATRIBUTOS INDIVIDUALES
                                $clienteNombre = strtolower($credito['nombre_cliente'] ?? '');
                                $producto = strtolower($credito['producto_o_servicio_dado'] ?? '');
                            @endphp
                            <tr data-estado="{{ $credito['estado'] ?? 'activo' }}"
                                data-cliente="{{ $clienteNombre }}"
                                data-producto="{{ $producto }}"
                                data-monto="{{ $credito['capital'] ?? 0 }}"
                                data-fecha="{{ $credito['fecha_credito'] ?? '' }}">
                                <td>
                                    <strong>{{ $credito['nombre_cliente'] ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <small>{{ Str::limit($credito['producto_o_servicio_dado'] ?: 'No especificado', 40) }}</small>
                                </td>
                                <td>
                                    <strong>Q{{ number_format($credito['capital'] ?? 0, 2) }}</strong>
                                </td>
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
                                            <div class="progress-bar bg-{{ $estadoColors[$credito['estado']] ?? 'info' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $porcentajePagado }}%"
                                                 aria-valuenow="{{ $porcentajePagado }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="ms-2">{{ number_format($porcentajePagado, 0) }}%</small>
                                    </div>
                                </td>
                                <td>
                                    <small>{{ isset($credito['fecha_credito']) ? \Carbon\Carbon::parse($credito['fecha_credito'])->format('d/m/Y') : 'N/A' }}</small>
                                </td>
                                <td>
                                    @if($credito['fecha_ultimo_pago'] ?? null)
                                        <small>{{ \Carbon\Carbon::parse($credito['fecha_ultimo_pago'])->format('d/m/Y') }}</small>
                                        <br>
                                        <small class="text-muted">Q{{ number_format($credito['ultima_cantidad_pagada'] ?? 0, 2) }}</small>
                                    @else
                                        <span class="badge bg-light text-dark">Sin pagos</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $estadoColors[$credito['estado']] ?? 'secondary' }} p-2">
                                        <i class="fas fa-{{ $credito['estado'] == 'activo' ? 'clock' : ($credito['estado'] == 'abonado' ? 'money-bill' : 'check-circle') }} me-1"></i>
                                        {{ $estadoLabels[$credito['estado']] ?? $credito['estado'] }}
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
                                        <form action="{{ route('creditos.change-status', $credito['id'] ?? '#') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $estadoColors[$credito['estado']] ?? 'secondary' }}" 
                                                    title="Cambiar estado"
                                                    onclick="return confirm('¿Estás seguro de cambiar el estado de este crédito?')">
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

                <!-- Paginación -->
                @if(!empty($creditosLinks) && count($creditosLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($creditosMeta))
                            Mostrando 
                            {{ $creditosMeta['from'] ?? 1 }} - 
                            {{ $creditosMeta['to'] ?? count($creditosData) }} de 
                            {{ $creditosMeta['total'] ?? count($creditosData) }} créditos
                        @else
                            Mostrando {{ count($creditosData) }} créditos
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($creditosLinks as $link)
                                @if(is_array($link))
                                    <li class="page-item {{ $link['active'] ?? false ? 'active' : '' }} {{ empty($link['url']) ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $link['url'] ?? '#' }}">
                                            {!! $link['label'] ?? '' !!}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </nav>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Modal para registrar pago (dinámico) -->
@include('creditos.partials._modal_registrar_pago_dinamico')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== CONFIGURACIÓN DE FILTROS Y BÚSQUEDA =====
    let currentFilter = 'todos';
    let currentSort = 'fecha_desc';
    let currentSearch = '';
    
    // Función para formatear fecha para comparación
    function parseFecha(fechaStr) {
        if (!fechaStr) return new Date(0);
        return new Date(fechaStr);
    }
    
    // Función para ordenar filas
    function ordenarFilas(rows, sortType) {
        return rows.sort((a, b) => {
            switch(sortType) {
                case 'fecha_desc':
                    const fechaA = parseFecha(a.dataset.fecha);
                    const fechaB = parseFecha(b.dataset.fecha);
                    return fechaB - fechaA;
                    
                case 'monto_desc':
                    const montoA = parseFloat(a.dataset.monto) || 0;
                    const montoB = parseFloat(b.dataset.monto) || 0;
                    return montoB - montoA;
                    
                default:
                    return 0;
            }
        });
    }
    
    // Función para aplicar todos los filtros
    function aplicarFiltros() {
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
        currentSearch = searchText;
        
        const tbody = document.querySelector('#creditosTable tbody');
        if (!tbody) return;
        
        let rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Excluir fila de no resultados si existe
        rows = rows.filter(row => row.id !== 'no-results-row');
        
        let visibleRows = [];
        
        rows.forEach(row => {
            // Obtener datos de los atributos data-*
            const estado = row.dataset.estado;
            const cliente = row.dataset.cliente || '';
            const producto = row.dataset.producto || '';
            
            // Filtro de estado
            const estadoMatch = currentFilter === 'todos' || estado === currentFilter;
            
            // Filtro de búsqueda - buscar en cliente y producto
            let searchMatch = true;
            if (searchText !== '') {
                searchMatch = cliente.includes(searchText) || producto.includes(searchText);
            }
            
            // Guardar estado de visibilidad
            if (estadoMatch && searchMatch) {
                row.style.display = '';
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });
        
        // Aplicar ordenamiento a las filas visibles
        if (currentSort !== 'ninguno') {
            visibleRows = ordenarFilas(visibleRows, currentSort);
        }
        
        // Reconstruir el tbody con las filas ordenadas
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        
        // Agregar filas visibles ordenadas
        visibleRows.forEach(row => tbody.appendChild(row));
        
        // Agregar filas ocultas al final
        rows.forEach(row => {
            if (row.style.display === 'none') {
                tbody.appendChild(row);
            }
        });
        
        // Mostrar mensaje si no hay resultados
        mostrarMensajeNoResultados(visibleRows.length, rows.length);
    }
    
    // Función para mostrar mensaje cuando no hay resultados
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const table = document.getElementById('creditosTable');
        const tbody = table.querySelector('tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="9" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron créditos</h5>
                        <p class="text-muted mb-3">Intenta con otros términos de búsqueda o filtros</p>
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
    
    // Función para limpiar filtros
    window.limpiarFiltros = function() {
        // Resetear filtros
        currentFilter = 'todos';
        currentSort = 'fecha_desc';
        
        // Actualizar botones de estado
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Actualizar botones de ordenamiento
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.sort === 'fecha_desc') {
                btn.classList.add('active');
            }
        });
        
        // Limpiar búsqueda
        document.getElementById('searchInput').value = '';
        
        // Aplicar filtros
        aplicarFiltros();
    };
    
    // Event listeners para filtros
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            aplicarFiltros();
        });
    });
    
    // Event listeners para ordenamiento
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentSort = this.dataset.sort;
            aplicarFiltros();
        });
    });
    
    // Búsqueda en tiempo real
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(aplicarFiltros, 300);
    });
    
    // Botón limpiar búsqueda
    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        aplicarFiltros();
        document.getElementById('searchInput').focus();
    });
    
    // Botón limpiar todos los filtros (opcional - puedes agregar un botón en la UI)
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', limpiarFiltros);
    }
    
    // ===== CONFIGURACIÓN DEL MODAL DE PAGO =====
    // Event listeners para botones de registrar pago
    document.querySelectorAll('.btn-registrar-pago').forEach(btn => {
        btn.addEventListener('click', function() {
            const creditoId = this.dataset.creditoId;
            const cliente = this.dataset.cliente;
            const capitalRestante = parseFloat(this.dataset.capitalRestante);
            
            // Llamar a la función global para abrir el modal
            if (window.abrirModalPago) {
                window.abrirModalPago(creditoId, cliente, capitalRestante);
            }
        });
    });
    
    // Inicializar botones activos
    const filterBtn = document.querySelector('.filter-btn[data-filter="todos"]');
    if (filterBtn) filterBtn.classList.add('active');
    
    const sortBtn = document.querySelector('.sort-btn[data-sort="fecha_desc"]');
    if (sortBtn) sortBtn.classList.add('active');
    
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
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