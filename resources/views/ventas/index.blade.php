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
            <h5 class="card-title mb-0">Gestión de Ventas</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('ventas.buscar') }}" class="btn btn-info">
                    <i class="fas fa-search me-2"></i> Búsqueda Avanzada
                </a>
                <a href="{{ route('ventas.reporte') }}" class="btn btn-success">
                    <i class="fas fa-chart-bar me-2"></i> Reportes
                </a>
                <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nueva Venta
                </a>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('ventas.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Buscar por número, referencia o cliente..."
                           value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-control">
                        <option value="todos" {{ $estado == 'todos' ? 'selected' : '' }}>Todos los estados</option>
                        <option value="completada" {{ $estado == 'completada' ? 'selected' : '' }}>Completadas</option>
                        <option value="pendiente" {{ $estado == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                        <option value="cancelada" {{ $estado == 'cancelada' ? 'selected' : '' }}>Canceladas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tipo" class="form-control">
                        <option value="todos" {{ $tipo == 'todos' ? 'selected' : '' }}>Todos los tipos</option>
                        <option value="producto" {{ $tipo == 'producto' ? 'selected' : '' }}>Productos</option>
                        <option value="servicio" {{ $tipo == 'servicio' ? 'selected' : '' }}>Servicios</option>
                        <option value="otro" {{ $tipo == 'otro' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i> Filtrar
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo me-2"></i> Limpiar
                    </a>
                </div>
            </form>
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
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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
                    <table class="table table-hover">
                        <thead>
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
                                $createdAt = \Carbon\Carbon::parse($venta['created_at'] ?? now());
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
                            @endphp
                            <tr>
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
                                    {{ $venta['cliente']['nombre'] ?? 'Cliente no especificado' }}
                                    @if(!empty($venta['cliente']['nit']))
                                        <br>
                                        <small class="text-muted">NIT: {{ $venta['cliente']['nit'] }}</small>
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
                                        {{ ucfirst($venta['metodo_pago'] ?? 'efectivo') }}
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
                                        {{ ucfirst($venta['estado'] ?? 'pendiente') }}
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
}
</style>
@endpush

@push('scripts')
<script>
// Inicializar popovers de Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
});
</script>
@endpush