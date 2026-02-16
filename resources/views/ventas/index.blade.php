@extends('layouts.app')

@section('title', 'Ventas - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Ventas</li>
@endsection

@section('content')
<div class="container-fluid">
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
        
        <!-- Estadísticas -->
        @if(!empty($estadisticas))
        <div class="card-body border-bottom bg-light">
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle p-3 me-3">
                            <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                        <div>
                            <small class="text-muted">Ventas Hoy</small>
                            <h4 class="mb-0">Q {{ number_format($estadisticas['totales']['hoy']['total'] ?? 0, 2) }}</h4>
                            <small>{{ $estadisticas['totales']['hoy']['ventas'] ?? 0 }} ventas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success rounded-circle p-3 me-3">
                            <i class="fas fa-calendar-week text-white"></i>
                        </div>
                        <div>
                            <small class="text-muted">Esta Semana</small>
                            <h4 class="mb-0">Q {{ number_format($estadisticas['totales']['semana']['total'] ?? 0, 2) }}</h4>
                            <small>{{ $estadisticas['totales']['semana']['ventas'] ?? 0 }} ventas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning rounded-circle p-3 me-3">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <div>
                            <small class="text-muted">Este Mes</small>
                            <h4 class="mb-0">Q {{ number_format($estadisticas['totales']['mes']['total'] ?? 0, 2) }}</h4>
                            <small>{{ $estadisticas['totales']['mes']['ventas'] ?? 0 }} ventas</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info rounded-circle p-3 me-3">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                        <div>
                            <small class="text-muted">Pendientes</small>
                            <h4 class="mb-0">{{ $estadisticas['por_tipo']['pendiente']['cantidad'] ?? 0 }}</h4>
                            <small>Ventas pendientes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

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

            <!-- Filtros y búsqueda en tiempo real -->
            <div class="row mb-4">
                <div class="col-md-7">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">
                            Todos
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm filter-btn" data-filter="completada">
                            Completadas
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm filter-btn" data-filter="pendiente">
                            Pendientes
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm filter-btn" data-filter="cancelada">
                            Canceladas
                        </button>
                    </div>
                    
                    <div class="btn-group ms-2" role="group">
                        <button type="button" class="btn btn-outline-info btn-sm filter-pago-btn" data-pago="todos">
                            Todos
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm filter-pago-btn" data-pago="efectivo">
                            Efectivo
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm filter-pago-btn" data-pago="tarjeta">
                            Tarjeta
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-pago-btn" data-pago="transferencia">
                            Transferencia
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm filter-pago-btn" data-pago="mixto">
                            Mixto
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Buscar por número, referencia, cliente, NIT...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filtros adicionales -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-calendar me-1"></i> Rango de fechas
                            </button>
                            <div class="dropdown-menu p-3" style="min-width: 300px;">
                                <div class="mb-3">
                                    <label class="form-label">Fecha desde</label>
                                    <input type="date" class="form-control form-control-sm" id="fechaDesde">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fecha hasta</label>
                                    <input type="date" class="form-control form-control-sm" id="fechaHasta">
                                </div>
                                <button class="btn btn-sm btn-primary w-100" id="aplicarRangoFechas">Aplicar</button>
                            </div>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-dollar-sign me-1"></i> Rango de montos
                            </button>
                            <div class="dropdown-menu p-3" style="min-width: 280px;">
                                <div class="mb-3">
                                    <label class="form-label">Monto mínimo</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" class="form-control" id="montoMin" min="0" step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Monto máximo</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" class="form-control" id="montoMax" min="0" step="0.01" placeholder="9999.99">
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary w-100" id="aplicarRangoMontos">Aplicar</button>
                            </div>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-sort me-1"></i> Ordenar por
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item sort-option" href="#" data-sort="fecha_desc">Más recientes</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="fecha_asc">Más antiguos</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="total_desc">Mayor monto</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="total_asc">Menor monto</a></li>
                            </ul>
                        </div>
                        
                        <button class="btn btn-sm btn-info" id="btnLimpiarFiltros" title="Limpiar todos los filtros">
                            <i class="fas fa-undo me-1"></i> Limpiar filtros
                        </button>
                    </div>
                </div>
            </div>

            @php
                $ventasData = $ventas['data'] ?? [];
                $ventasLinks = $ventas['links'] ?? [];
                $ventasMeta = $ventas['meta'] ?? [];
            @endphp

            @if(empty($ventasData))
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay ventas registradas</h5>
                    <p class="text-muted">Comienza registrando tu primera venta</p>
                    <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primera Venta
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="ventasTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 100px;">Fecha</th>
                                <th>N° Venta</th>
                                <th>Cliente</th>
                                <th>Items</th>
                                <th>Método Pago</th>
                                <th>Subtotal</th>
                                <th>Descuento</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventasData as $venta)
                            @php
                                $createdAt = \Carbon\Carbon::parse($venta['created_at'] ?? now())->timezone('America/Guatemala');
                                $numItems = count($venta['detalles'] ?? []);
                                
                                // Determinar color de estado
                                $estado = $venta['estado'] ?? 'pendiente';
                                $estadoColor = 'warning';
                                if ($estado == 'completada') {
                                    $estadoColor = 'success';
                                } elseif ($estado == 'cancelada') {
                                    $estadoColor = 'danger';
                                }
                                
                                // Determinar color de método de pago
                                $metodo = $venta['metodo_pago'] ?? 'efectivo';
                                $metodoColor = 'warning';
                                if ($metodo == 'efectivo') {
                                    $metodoColor = 'success';
                                } elseif ($metodo == 'tarjeta') {
                                    $metodoColor = 'info';
                                } elseif ($metodo == 'transferencia') {
                                    $metodoColor = 'primary';
                                } elseif ($metodo == 'mixto') {
                                    $metodoColor = 'secondary';
                                }
                                
                                // Preparar datos para búsqueda
                                $clienteNombre = $venta['cliente']['nombre'] ?? '';
                                $clienteNit = $venta['cliente']['nit'] ?? '';
                                $itemsTexto = '';
                                if (!empty($venta['detalles'])) {
                                    $itemsTexto = implode(' ', array_column($venta['detalles'], 'descripcion'));
                                }
                                $searchText = strtolower(
                                    ($venta['numero_venta'] ?? '') . ' ' . 
                                    $clienteNombre . ' ' . 
                                    $clienteNit . ' ' . 
                                    $itemsTexto . ' ' . 
                                    ($venta['id'] ?? '')
                                );
                            @endphp
                            <tr data-estado="{{ $estado }}"
                                data-metodo-pago="{{ $metodo }}"
                                data-fecha="{{ $venta['created_at'] ?? '' }}"
                                data-total="{{ $venta['total'] ?? 0 }}"
                                data-cliente="{{ strtolower($clienteNombre) }}"
                                data-search="{{ $searchText }}">
                                <td>
                                    <small class="d-block">{{ $createdAt->format('d/m/Y') }}</small>
                                    <small class="text-muted">{{ $createdAt->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $venta['numero_venta'] ?? 'SIN-NUM' }}</strong>
                                    <br>
                                    <small class="text-muted">ID: {{ $venta['id'] }}</small>
                                </td>
                                <td>
                                    {{ $clienteNombre ?: 'Cliente no especificado' }}
                                    @if(!empty($clienteNit))
                                        <br>
                                        <small class="text-muted">NIT: {{ $clienteNit }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $numItems }} {{ $numItems == 1 ? 'item' : 'items' }}
                                    </span>
                                    @if($numItems > 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-link p-0 ms-1" 
                                                data-bs-toggle="popover" 
                                                data-bs-html="true"
                                                data-bs-trigger="hover"
                                                title="Items de la venta"
                                                data-bs-content="
                                                    <ul class='list-unstyled mb-0'>
                                                        @foreach(array_slice($venta['detalles'] ?? [], 0, 3) as $detalle)
                                                            <li><small>• {{ $detalle['cantidad'] }}x {{ Str::limit($detalle['descripcion'], 25) }}</small></li>
                                                        @endforeach
                                                        @if($numItems > 3)
                                                            <li><small class='text-muted'>... y {{ $numItems - 3 }} más</small></li>
                                                        @endif
                                                    </ul>
                                                ">
                                            <i class="fas fa-info-circle text-muted"></i>
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $metodoColor }}">
                                        <i class="fas fa-{{ $metodo == 'efectivo' ? 'money-bill' : ($metodo == 'tarjeta' ? 'credit-card' : ($metodo == 'transferencia' ? 'exchange-alt' : 'coins')) }} me-1"></i>
                                        {{ ucfirst($metodo) }}
                                    </span>
                                </td>
                                <td>
                                    Q{{ number_format($venta['subtotal'] ?? 0, 2) }}
                                </td>
                                <td>
                                    @if(!empty($venta['descuento_total']) && $venta['descuento_total'] > 0)
                                        <span class="text-danger">
                                            <i class="fas fa-tag me-1"></i>
                                            Q{{ number_format($venta['descuento_total'], 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-primary">
                                        Q{{ number_format($venta['total'] ?? 0, 2) }}
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $estadoColor }}">
                                        <i class="fas fa-{{ $estado == 'completada' ? 'check-circle' : ($estado == 'pendiente' ? 'clock' : 'times-circle') }} me-1"></i>
                                        {{ ucfirst($estado) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('ventas.show', $venta['id'] ?? '#') }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(($venta['estado'] ?? 'completada') !== 'cancelada')
                                            <form action="{{ route('ventas.cancelar', $venta['id'] ?? '#') }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('¿Estás seguro de cancelar esta venta?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Cancelar venta">
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

                <!-- Paginación -->
                @if(!empty($ventasLinks) && count($ventasLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($ventasMeta))
                            Mostrando 
                            {{ $ventasMeta['from'] ?? 1 }} - 
                            {{ $ventasMeta['to'] ?? count($ventasData) }} de 
                            {{ $ventasMeta['total'] ?? count($ventasData) }} ventas
                        @else
                            Mostrando {{ count($ventasData) }} ventas
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($ventasLinks as $link)
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentEstadoFilter = 'todos';
    let currentMetodoPagoFilter = 'todos';
    let currentSort = 'fecha_desc';
    let currentSearch = '';
    let fechaDesde = null;
    let fechaHasta = null;
    let montoMin = null;
    let montoMax = null;
    
    // Función para parsear fecha
    function parseFecha(fechaStr) {
        if (!fechaStr) return null;
        return new Date(fechaStr);
    }
    
    // Función para aplicar todos los filtros
    function aplicarFiltros() {
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
        currentSearch = searchText;
        
        const tbody = document.querySelector('#ventasTable tbody');
        let rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Excluir fila de no resultados si existe
        rows = rows.filter(row => row.id !== 'no-results-row');
        
        let visibleRows = [];
        
        rows.forEach(row => {
            const estado = row.dataset.estado;
            const metodoPago = row.dataset.metodoPago;
            const fecha = parseFecha(row.dataset.fecha);
            const total = parseFloat(row.dataset.total) || 0;
            const searchData = row.dataset.search || '';
            
            // Filtro de estado
            const estadoMatch = currentEstadoFilter === 'todos' || estado === currentEstadoFilter;
            
            // Filtro de método de pago
            const metodoMatch = currentMetodoPagoFilter === 'todos' || metodoPago === currentMetodoPagoFilter;
            
            // Filtro de rango de fechas
            let fechaMatch = true;
            if (fechaDesde && fecha) {
                fechaMatch = fecha >= fechaDesde;
            }
            if (fechaMatch && fechaHasta && fecha) {
                const fechaHastaFin = new Date(fechaHasta);
                fechaHastaFin.setHours(23, 59, 59, 999);
                fechaMatch = fecha <= fechaHastaFin;
            }
            
            // Filtro de rango de montos
            let montoMatch = true;
            if (montoMin !== null) {
                montoMatch = total >= montoMin;
            }
            if (montoMatch && montoMax !== null) {
                montoMatch = total <= montoMax;
            }
            
            // Filtro de búsqueda
            const searchMatch = searchText === '' || searchData.includes(searchText);
            
            // Mostrar u ocultar fila
            if (estadoMatch && metodoMatch && fechaMatch && montoMatch && searchMatch) {
                row.style.display = '';
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });
        
        // Aplicar ordenamiento
        visibleRows = ordenarFilas(visibleRows, currentSort);
        
        // Reordenar la tabla
        const allRows = rows.filter(row => visibleRows.includes(row));
        const hiddenRows = rows.filter(row => !visibleRows.includes(row));
        
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        
        visibleRows.forEach(row => tbody.appendChild(row));
        hiddenRows.forEach(row => tbody.appendChild(row));
        
        // Mostrar mensaje si no hay resultados
        mostrarMensajeNoResultados(visibleRows.length, rows.length);
    }
    
    // Función para ordenar filas
    function ordenarFilas(rows, sortType) {
        return rows.sort((a, b) => {
            switch(sortType) {
                case 'fecha_desc':
                    const fechaA = parseFecha(a.dataset.fecha) || new Date(0);
                    const fechaB = parseFecha(b.dataset.fecha) || new Date(0);
                    return fechaB - fechaA;
                    
                case 'fecha_asc':
                    const fechaC = parseFecha(a.dataset.fecha) || new Date(0);
                    const fechaD = parseFecha(b.dataset.fecha) || new Date(0);
                    return fechaC - fechaD;
                    
                case 'total_desc':
                    return (parseFloat(b.dataset.total) || 0) - (parseFloat(a.dataset.total) || 0);
                    
                case 'total_asc':
                    return (parseFloat(a.dataset.total) || 0) - (parseFloat(b.dataset.total) || 0);
                    
                default:
                    return 0;
            }
        });
    }
    
    // Función para mostrar mensaje cuando no hay resultados
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const table = document.getElementById('ventasTable');
        const tbody = table.querySelector('tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="10" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron ventas</h5>
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
        currentEstadoFilter = 'todos';
        currentMetodoPagoFilter = 'todos';
        currentSort = 'fecha_desc';
        fechaDesde = null;
        fechaHasta = null;
        montoMin = null;
        montoMax = null;
        
        // Actualizar botones de estado
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Actualizar botones de método de pago
        document.querySelectorAll('.filter-pago-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.pago === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Limpiar inputs
        document.getElementById('fechaDesde').value = '';
        document.getElementById('fechaHasta').value = '';
        document.getElementById('montoMin').value = '';
        document.getElementById('montoMax').value = '';
        document.getElementById('searchInput').value = '';
        
        aplicarFiltros();
    };
    
    // Eventos para filtros de estado
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentEstadoFilter = this.dataset.filter;
            aplicarFiltros();
        });
    });
    
    // Eventos para filtros de método de pago
    document.querySelectorAll('.filter-pago-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-pago-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentMetodoPagoFilter = this.dataset.pago;
            aplicarFiltros();
        });
    });
    
    // Evento para aplicar rango de fechas
    document.getElementById('aplicarRangoFechas').addEventListener('click', function() {
        fechaDesde = document.getElementById('fechaDesde').value ? new Date(document.getElementById('fechaDesde').value) : null;
        fechaHasta = document.getElementById('fechaHasta').value ? new Date(document.getElementById('fechaHasta').value) : null;
        aplicarFiltros();
    });
    
    // Evento para aplicar rango de montos
    document.getElementById('aplicarRangoMontos').addEventListener('click', function() {
        montoMin = document.getElementById('montoMin').value ? parseFloat(document.getElementById('montoMin').value) : null;
        montoMax = document.getElementById('montoMax').value ? parseFloat(document.getElementById('montoMax').value) : null;
        aplicarFiltros();
    });
    
    // Eventos para ordenamiento
    document.querySelectorAll('.sort-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
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
    
    // Botón limpiar todos los filtros
    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);
    
    // Inicializar botones activos
    document.querySelector('.filter-btn[data-filter="todos"]').classList.add('active');
    document.querySelector('.filter-pago-btn[data-pago="todos"]').classList.add('active');
    
    // Inicializar popovers de Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
    
    // Agregar tooltips a los botones
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

@push('styles')
<style>
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.badge {
    font-size: 0.85em;
    padding: 0.4em 0.8em;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.bg-primary.rounded-circle,
.bg-success.rounded-circle,
.bg-warning.rounded-circle,
.bg-info.rounded-circle {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Estilos para botones de filtro activos */
.filter-btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.filter-btn[data-filter="completada"].active {
    background-color: #198754;
    border-color: #198754;
}

.filter-btn[data-filter="pendiente"].active {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.filter-btn[data-filter="cancelada"].active {
    background-color: #dc3545;
    border-color: #dc3545;
}

.filter-pago-btn.active {
    background-color: #0dcaf0;
    color: #000;
    border-color: #0dcaf0;
}

.filter-pago-btn[data-pago="efectivo"].active {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

.filter-pago-btn[data-pago="tarjeta"].active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.filter-pago-btn[data-pago="transferencia"].active {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.filter-pago-btn[data-pago="mixto"].active {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

/* Dropdowns de filtros */
.dropdown-menu {
    max-height: 300px;
    overflow-y: auto;
}

/* Tooltip personalizado */
.popover {
    max-width: 300px;
}

.popover ul {
    margin-bottom: 0;
    padding-left: 0;
}

.popover li {
    padding: 2px 0;
    border-bottom: 1px solid #f0f0f0;
}

.popover li:last-child {
    border-bottom: none;
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
    
    .card-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }
    
    .card-header .d-flex {
        width: 100%;
        justify-content: space-between;
    }
    
    .badge {
        font-size: 0.75em;
        padding: 0.3em 0.5em;
    }
    
    .d-flex.flex-wrap {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .dropdown {
        width: 100%;
    }
    
    .dropdown .btn {
        width: 100%;
        text-align: left;
    }
}
</style>
@endpush