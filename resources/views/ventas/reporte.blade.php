@extends('layouts.app')

@section('title', 'Reportes de Ventas - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active">Reportes</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-bar me-2"></i> Reportes de Ventas
            </h5>
            <div>
                <button class="btn btn-primary" onclick="imprimirReporte()">
                    <i class="fas fa-print me-2"></i> Imprimir
                </button>
                <button class="btn btn-success ms-2" onclick="exportarExcel()">
                    <i class="fas fa-file-excel me-2"></i> Exportar Excel
                </button>
            </div>
        </div>
        
        <!-- Filtros del Reporte -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('ventas.reporte') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" 
                           class="form-control" value="{{ $fecha_inicio }}">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" 
                           class="form-control" value="{{ $fecha_fin }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i> Generar Reporte
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="button" onclick="aplicarPeriodo('hoy')" class="btn btn-outline-secondary w-100 mb-1">Hoy</button>
                        <button type="button" onclick="aplicarPeriodo('mes')" class="btn btn-outline-secondary w-100">Este Mes</button>
                    </div>
                </div>
            </form>
        </div>
        
        @if(empty($reporte))
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay datos para el reporte</h5>
                <p class="text-muted">Seleccione un período de tiempo</p>
            </div>
        @else
            <!-- Resumen del Reporte -->
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Ventas</h6>
                                        <h2 class="mb-0">{{ $reporte['resumen']['total_ventas'] ?? 0 }}</h2>
                                    </div>
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Total Ingresos</h6>
                                        <h2 class="mb-0">Q{{ number_format($reporte['resumen']['total_ingresos'] ?? 0, 2) }}</h2>
                                    </div>
                                    <i class="fas fa-money-bill-wave fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Descuentos</h6>
                                        <h2 class="mb-0">Q{{ number_format($reporte['resumen']['total_descuentos'] ?? 0, 2) }}</h2>
                                    </div>
                                    <i class="fas fa-tag fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Promedio Venta</h6>
                                        <h2 class="mb-0">Q{{ number_format($reporte['resumen']['promedio_venta'] ?? 0, 2) }}</h2>
                                    </div>
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfico de Ventas por Día -->
                @if(!empty($reporte['por_dia']))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Ventas por Día</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Cantidad de Ventas</th>
                                                <th>Total</th>
                                                <th>Descuentos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reporte['por_dia'] as $fecha => $datos)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                                                <td>{{ $datos['cantidad'] }}</td>
                                                <td>Q{{ number_format($datos['total'], 2) }}</td>
                                                <td>Q{{ number_format($datos['descuentos'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Ventas por Tipo -->
                @if(!empty($reporte['por_tipo']))
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Ventas por Tipo</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Cantidad</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reporte['por_tipo'] as $tipo => $datos)
                                            <tr>
                                                <td>{{ ucfirst($tipo) }}</td>
                                                <td>{{ $datos['cantidad'] }}</td>
                                                <td>Q{{ number_format($datos['total'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ventas por Método de Pago -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Ventas por Método de Pago</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Método</th>
                                                <th>Cantidad</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reporte['por_metodo_pago'] as $metodo => $datos)
                                            <tr>
                                                <td>{{ ucfirst($metodo) }}</td>
                                                <td>{{ $datos['cantidad'] }}</td>
                                                <td>Q{{ number_format($datos['total'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Top Productos -->
                @if(!empty($reporte['top_productos']))
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Top 10 Productos Más Vendidos</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Producto</th>
                                                <th>Cantidad Vendida</th>
                                                <th>Total Ingresos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reporte['top_productos'] as $index => $producto)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $producto['producto'] }}</td>
                                                <td>{{ $producto['cantidad'] }}</td>
                                                <td>Q{{ number_format($producto['total'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Top Servicios -->
                @if(!empty($reporte['top_servicios']))
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Top 10 Servicios Más Vendidos</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Servicio</th>
                                                <th>Cantidad Vendida</th>
                                                <th>Total Ingresos</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reporte['top_servicios'] as $index => $servicio)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $servicio['servicio'] }}</td>
                                                <td>{{ $servicio['cantidad'] }}</td>
                                                <td>Q{{ number_format($servicio['total'], 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Lista Completa de Ventas -->
                @if(!empty($reporte['ventas']))
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Detalle de Ventas</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm" id="tablaDetalleVentas">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Referencia</th>
                                                <th>Cliente</th>
                                                <th>Descripción</th>
                                                <th>Tipo</th>
                                                <th>Cantidad</th>
                                                <th>Total</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($reporte['ventas'] as $venta)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($venta['created_at'])->format('d/m/Y') }}</td>
                                                <td>{{ $venta['referencia'] ?? 'N/A' }}</td>
                                                <td>{{ $venta['cliente']['nombre'] ?? 'Cliente no especificado' }}</td>
                                                <td>{{ $venta['descripcion'] }}</td>
                                                <td>{{ ucfirst($venta['tipo']) }}</td>
                                                <td>{{ $venta['cantidad'] }}</td>
                                                <td>Q{{ number_format($venta['total'], 2) }}</td>
                                                <td>{{ ucfirst($venta['estado']) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.card.bg-primary, .card.bg-success, .card.bg-warning, .card.bg-info {
    border: none;
}

.card.bg-primary .card-title, 
.card.bg-success .card-title, 
.card.bg-warning .card-title, 
.card.bg-info .card-title {
    font-size: 0.9rem;
    opacity: 0.9;
}

.table-sm th, .table-sm td {
    padding: 0.5rem;
}

#tablaDetalleVentas {
    font-size: 0.85rem;
}

@media print {
    .btn, .card-header .d-flex {
        display: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Aplicar período predefinido
function aplicarPeriodo(periodo) {
    const hoy = new Date();
    let inicio, fin;
    
    switch(periodo) {
        case 'hoy':
            inicio = hoy.toISOString().split('T')[0];
            fin = inicio;
            break;
        case 'mes':
            inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().split('T')[0];
            fin = hoy.toISOString().split('T')[0];
            break;
        default:
            return;
    }
    
    document.getElementById('fecha_inicio').value = inicio;
    document.getElementById('fecha_fin').value = fin;
}

// Imprimir reporte
function imprimirReporte() {
    window.print();
}

// Exportar a Excel (simulado)
function exportarExcel() {
    alert('Funcionalidad de exportación a Excel será implementada próximamente.');
    
    // En una implementación real, aquí se haría una llamada AJAX
    // para generar y descargar un archivo Excel
    /*
    fetch('{{ route("ventas.exportar.excel") }}?' + new URLSearchParams({
        fecha_inicio: document.getElementById('fecha_inicio').value,
        fecha_fin: document.getElementById('fecha_fin').value
    }))
    .then(response => response.blob())
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `reporte_ventas_${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    });
    */
}

// Inicializar DataTable si está disponible
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si DataTables está disponible
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#tablaDetalleVentas').DataTable({
            pageLength: 10,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            order: [[0, 'desc']]
        });
    }
});
</script>
@endpush