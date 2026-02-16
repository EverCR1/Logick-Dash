@extends('layouts.app')

@section('title', 'Detalle de Venta - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active">Detalle de Venta #{{ $venta['id'] }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-invoice me-2"></i> Detalle de Venta #{{ $venta['id'] }}
                </h5>
                <div>
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                    @if($venta['estado'] === 'pendiente' || $venta['estado'] === 'completada')
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> Imprimir
                    </button>
                    @endif
                    @if($venta['estado'] === 'pendiente' || $venta['estado'] === 'completada')
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmarCancelacion({{ $venta['id'] }})">
                        <i class="fas fa-ban me-2"></i> Cancelar Venta
                    </button>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Estado de la venta -->
            <div class="row mb-4">
                <div class="col-12">
                    @php
                        $estadoClass = [
                            'completada' => 'success',
                            'pendiente' => 'warning',
                            'cancelada' => 'danger',
                            'anulada' => 'secondary'
                        ][$venta['estado']] ?? 'secondary';
                        
                        $estadoTexto = [
                            'completada' => 'Completada',
                            'pendiente' => 'Pendiente',
                            'cancelada' => 'Cancelada',
                            'anulada' => 'Anulada'
                        ][$venta['estado']] ?? $venta['estado'];
                        
                        // Determinar qué campo de fecha usar
                        $fechaVenta = $venta['fecha'] ?? $venta['fecha_venta'] ?? $venta['created_at'] ?? $venta['fecha_creacion'] ?? null;
                    @endphp
                    <div class="alert alert-{{ $estadoClass }} mb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Estado:</strong> 
                                <span class="badge bg-{{ $estadoClass }} bg-opacity-50 text-dark">
                                    {{ $estadoTexto }}
                                </span>
                            </div>
                            <div>
                                <strong>Fecha:</strong> 
                                @if($fechaVenta)
                                    {{ \Carbon\Carbon::parse($fechaVenta)->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">No disponible</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del cliente y venta -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user me-2"></i> Información del Cliente</h6>
                        </div>
                        <div class="card-body">
                            @if($venta['cliente'])
                                <p class="mb-1"><strong>Nombre:</strong> {{ $venta['cliente']['nombre'] }}</p>
                                @if(!empty($venta['cliente']['nit']))
                                    <p class="mb-1"><strong>NIT:</strong> {{ $venta['cliente']['nit'] }}</p>
                                @endif
                                @if(!empty($venta['cliente']['telefono']))
                                    <p class="mb-1"><strong>Teléfono:</strong> {{ $venta['cliente']['telefono'] }}</p>
                                @endif
                                @if(!empty($venta['cliente']['email']))
                                    <p class="mb-1"><strong>Email:</strong> {{ $venta['cliente']['email'] }}</p>
                                @endif
                            @else
                                <p class="text-muted mb-0">Cliente ocasional / No registrado</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i> Información de Pago</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-1">
                                <strong>Método de pago:</strong> 
                                <span class="badge bg-info">
                                    {{ [
                                        'efectivo' => 'Efectivo',
                                        'tarjeta' => 'Tarjeta',
                                        'transferencia' => 'Transferencia',
                                        'mixto' => 'Mixto'
                                    ][$venta['metodo_pago']] ?? $venta['metodo_pago'] }}
                                </span>
                            </p>
                            @if($venta['es_credito'] ?? false)
                                <p class="mb-1">
                                    <strong>Tipo:</strong> 
                                    <span class="badge bg-warning">Crédito</span>
                                </p>
                                @if(isset($venta['fecha_vencimiento']))
                                    <p class="mb-1">
                                        <strong>Fecha vencimiento:</strong> 
                                        {{ \Carbon\Carbon::parse($venta['fecha_vencimiento'])->format('d/m/Y') }}
                                    </p>
                                @endif
                                @if(isset($venta['saldo_pendiente']) && $venta['saldo_pendiente'] > 0)
                                    <p class="mb-1">
                                        <strong>Saldo pendiente:</strong> 
                                        <span class="text-danger">Q{{ number_format($venta['saldo_pendiente'], 2) }}</span>
                                    </p>
                                @endif
                            @else
                                <p class="mb-1">
                                    <strong>Tipo:</strong> 
                                    <span class="badge bg-success">Contado</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items de la venta -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-boxes me-2"></i> Items de la Venta</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Tipo</th>
                                            <th>Descripción</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Precio Unit.</th>
                                            <th class="text-end">Descuento</th>
                                            <th class="text-end">Subtotal</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($venta['items'] ?? [] as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @php
                                                    $tipoClass = [
                                                        'producto' => 'primary',
                                                        'servicio' => 'info',
                                                        'otro' => 'secondary'
                                                    ][$item['tipo']] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $tipoClass }}">
                                                    {{ ucfirst($item['tipo']) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $item['descripcion'] }}
                                                @if(!empty($item['referencia']))
                                                    <br><small class="text-muted">Ref: {{ $item['referencia'] }}</small>
                                                @endif
                                                @if(!empty($item['producto_id']))
                                                    <br><small class="text-muted">ID Producto: {{ $item['producto_id'] }}</small>
                                                @endif
                                                @if(!empty($item['servicio_id']))
                                                    <br><small class="text-muted">ID Servicio: {{ $item['servicio_id'] }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $item['cantidad'] }}</td>
                                            <td class="text-end">Q{{ number_format($item['precio_unitario'], 2) }}</td>
                                            <td class="text-end {{ $item['descuento'] > 0 ? 'text-danger' : '' }}">
                                                Q{{ number_format($item['descuento'] ?? 0, 2) }}
                                            </td>
                                            <td class="text-end">
                                                Q{{ number_format($item['cantidad'] * $item['precio_unitario'], 2) }}
                                            </td>
                                            <td class="text-end">
                                                <strong>Q{{ number_format($item['total'] ?? ($item['cantidad'] * $item['precio_unitario'] - ($item['descuento'] ?? 0)), 2) }}</strong>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                                <p class="text-muted">No hay items en esta venta</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-secondary">
                                            <td colspan="5" class="text-end"><strong>Totales:</strong></td>
                                            <td class="text-end">
                                                <strong>Q{{ number_format($venta['total_descuento'] ?? 0, 2) }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <strong>Q{{ number_format($venta['total_subtotal'] ?? 0, 2) }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <strong class="text-primary">Q{{ number_format($venta['total'] ?? 0, 2) }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Observaciones y totales adicionales -->
            <div class="row">
                <div class="col-md-8">
                    @if(!empty($venta['observaciones']))
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-comment me-2"></i> Observaciones</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $venta['observaciones'] }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end">Q{{ number_format($venta['total_subtotal'] ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Descuento total:</td>
                                    <td class="text-end text-danger">- Q{{ number_format($venta['total_descuento'] ?? 0, 2) }}</td>
                                </tr>
                                @if(($venta['impuesto'] ?? 0) > 0)
                                <tr>
                                    <td>Impuesto ({{ $venta['impuesto_porcentaje'] ?? 12 }}%):</td>
                                    <td class="text-end">Q{{ number_format($venta['impuesto'] ?? 0, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td><strong>TOTAL:</strong></td>
                                    <td class="text-end"><strong class="text-primary fs-5">Q{{ number_format($venta['total'] ?? 0, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($venta['estado'] === 'pendiente' || $venta['estado'] === 'completada')
        <div class="card-footer">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Volver
                </a>
                <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                    <i class="fas fa-print me-2"></i> Imprimir
                </button>
                @if($venta['estado'] !== 'cancelada' && $venta['estado'] !== 'anulada')
                <button type="button" class="btn btn-danger" onclick="confirmarCancelacion({{ $venta['id'] }})">
                    <i class="fas fa-ban me-2"></i> Cancelar Venta
                </button>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de confirmación para cancelar venta -->
<div class="modal fade" id="modalCancelar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cancelar Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea cancelar esta venta?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta acción no se puede deshacer y revertirá el stock de los productos.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, mantener</button>
                <form id="formCancelar" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn btn-danger">Sí, cancelar venta</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarCancelacion(id) {
    const form = document.getElementById('formCancelar');
    form.action = `{{ url('ventas') }}/${id}/cancelar`;
    new bootstrap.Modal(document.getElementById('modalCancelar')).show();
}

// Corregir función number_exists (si no existe)
function number_format(number, decimals) {
    return new Intl.NumberFormat('es-GT', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn, .card-header, .card-footer, .breadcrumb, footer, nav {
        display: none !important;
    }
    .card {
        border: none !important;
    }
    .card-body {
        padding: 0 !important;
    }
    .table {
        border: 1px solid #000 !important;
    }
}
</style>
@endpush