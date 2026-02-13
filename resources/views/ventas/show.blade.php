@extends('layouts.app')

@section('title', 'Detalle de Venta - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active">Detalle Venta</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(!$venta)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i> Venta no encontrada
        </div>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Volver a Ventas
        </a>
    @else
        <div class="row">
            <!-- Información Principal -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-receipt me-2"></i> Detalle de Venta #{{ $venta['referencia'] ?? 'N/A' }}
                        </h5>
                        <div>
                            @if($venta['estado'] !== 'cancelada')
                                <form action="{{ route('ventas.cancelar', $venta['id']) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de cancelar esta venta?')">
                                        <i class="fas fa-ban me-1"></i> Cancelar Venta
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('ventas.index') }}" class="btn btn-secondary btn-sm ms-2">
                                <i class="fas fa-arrow-left me-1"></i> Volver
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 40%;">Referencia:</th>
                                        <td><strong>{{ $venta['referencia'] ?? 'N/A' }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha:</th>
                                        <td>{{ \Carbon\Carbon::parse($venta['created_at'])->format('d/m/Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Estado:</th>
                                        <td>
                                            @php
                                                $estadoColor = 'warning';
                                                if ($venta['estado'] == 'completada') $estadoColor = 'success';
                                                if ($venta['estado'] == 'cancelada') $estadoColor = 'danger';
                                            @endphp
                                            <span class="badge bg-{{ $estadoColor }}">
                                                {{ ucfirst($venta['estado']) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tipo:</th>
                                        <td>
                                            @php
                                                $tipoColor = 'secondary';
                                                if ($venta['tipo'] == 'producto') $tipoColor = 'primary';
                                                if ($venta['tipo'] == 'servicio') $tipoColor = 'info';
                                            @endphp
                                            <span class="badge bg-{{ $tipoColor }}">
                                                {{ ucfirst($venta['tipo']) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 40%;">Cliente:</th>
                                        <td>{{ $venta['cliente']['nombre'] ?? 'Cliente no especificado' }}</td>
                                    </tr>
                                    @if(!empty($venta['cliente']['nit']))
                                    <tr>
                                        <th>NIT:</th>
                                        <td>{{ $venta['cliente']['nit'] }}</td>
                                    </tr>
                                    @endif
                                    @if(!empty($venta['cliente']['telefono']))
                                    <tr>
                                        <th>Teléfono:</th>
                                        <td>{{ $venta['cliente']['telefono'] }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th>Método de Pago:</th>
                                        <td>
                                            @php
                                                $metodoColor = 'warning';
                                                if ($venta['metodo_pago'] == 'efectivo') $metodoColor = 'success';
                                                if ($venta['metodo_pago'] == 'tarjeta') $metodoColor = 'info';
                                                if ($venta['metodo_pago'] == 'transferencia') $metodoColor = 'primary';
                                            @endphp
                                            <span class="badge bg-{{ $metodoColor }}">
                                                {{ ucfirst($venta['metodo_pago']) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Vendedor:</th>
                                        <td>{{ $venta['usuario']['nombre_completo'] ?? $venta['usuario']['nombres'] ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Detalle del Producto/Servicio -->
                        <div class="mt-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle me-2"></i> Detalle del Item Vendido
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>{{ $venta['descripcion'] }}</h5>
                                            
                                            @if($venta['tipo'] == 'producto' && !empty($venta['producto']))
                                            <div class="mt-3">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th style="width: 40%;">Producto:</th>
                                                        <td>{{ $venta['producto']['nombre'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>SKU:</th>
                                                        <td>{{ $venta['producto']['sku'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Marca:</th>
                                                        <td>{{ $venta['producto']['marca'] ?? 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            @endif
                                            
                                            @if($venta['tipo'] == 'servicio' && !empty($venta['servicio']))
                                            <div class="mt-3">
                                                <table class="table table-sm">
                                                    <tr>
                                                        <th style="width: 40%;">Servicio:</th>
                                                        <td>{{ $venta['servicio']['nombre'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Código:</th>
                                                        <td>{{ $venta['servicio']['codigo'] ?? 'N/A' }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-center mb-3">Resumen Financiero</h6>
                                            
                                            <table class="table table-sm">
                                                <tr>
                                                    <td>Cantidad:</td>
                                                    <td class="text-end"><strong>{{ $venta['cantidad'] }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Precio Unitario:</td>
                                                    <td class="text-end">Q{{ number_format($venta['precio_unitario'], 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Subtotal:</td>
                                                    <td class="text-end">Q{{ number_format($venta['precio_unitario'] * $venta['cantidad'], 2) }}</td>
                                                </tr>
                                                @if($venta['descuento'] > 0)
                                                <tr>
                                                    <td>Descuento:</td>
                                                    <td class="text-end text-danger">-Q{{ number_format($venta['descuento'], 2) }}</td>
                                                </tr>
                                                @endif
                                                <tr class="table-active">
                                                    <td><strong>TOTAL:</strong></td>
                                                    <td class="text-end">
                                                        <strong class="h5">Q{{ number_format($venta['total'], 2) }}</strong>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Observaciones -->
                        @if(!empty($venta['observaciones']))
                        <div class="mt-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-sticky-note me-2"></i> Observaciones
                            </h6>
                            <div class="card">
                                <div class="card-body">
                                    {{ $venta['observaciones'] }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Información Adicional -->
            <div class="col-lg-4">
                <!-- Información del Cliente -->
                @if(!empty($venta['cliente']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i> Información del Cliente
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="avatar-circle bg-primary mb-2">
                                <span class="avatar-text">
                                    {{ substr($venta['cliente']['nombre'], 0, 1) }}
                                </span>
                            </div>
                            <h5>{{ $venta['cliente']['nombre'] }}</h5>
                        </div>
                        
                        <table class="table table-sm">
                            @if(!empty($venta['cliente']['nit']))
                            <tr>
                                <td><i class="fas fa-id-card me-2 text-muted"></i> NIT:</td>
                                <td class="text-end">{{ $venta['cliente']['nit'] }}</td>
                            </tr>
                            @endif
                            @if(!empty($venta['cliente']['email']))
                            <tr>
                                <td><i class="fas fa-envelope me-2 text-muted"></i> Email:</td>
                                <td class="text-end">{{ $venta['cliente']['email'] }}</td>
                            </tr>
                            @endif
                            @if(!empty($venta['cliente']['telefono']))
                            <tr>
                                <td><i class="fas fa-phone me-2 text-muted"></i> Teléfono:</td>
                                <td class="text-end">{{ $venta['cliente']['telefono'] }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><i class="fas fa-user-tag me-2 text-muted"></i> Tipo:</td>
                                <td class="text-end">{{ ucfirst($venta['cliente']['tipo'] ?? 'natural') }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-calendar me-2 text-muted"></i> Registrado:</td>
                                <td class="text-end">
                                    {{ \Carbon\Carbon::parse($venta['cliente']['created_at'] ?? now())->format('d/m/Y') }}
                                </td>
                            </tr>
                        </table>
                        
                        <a href="{{ route('clientes.show', $venta['cliente']['id']) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-external-link-alt me-2"></i> Ver Cliente
                        </a>
                    </div>
                </div>
                @endif
                
                <!-- Información del Producto/Servicio -->
                @if($venta['tipo'] == 'producto' && !empty($venta['producto']))
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-box me-2"></i> Información del Producto
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>SKU:</td>
                                <td class="text-end">{{ $venta['producto']['sku'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Marca:</td>
                                <td class="text-end">{{ $venta['producto']['marca'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Color:</td>
                                <td class="text-end">{{ $venta['producto']['color'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Precio Compra:</td>
                                <td class="text-end">Q{{ number_format($venta['producto']['precio_compra'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Precio Venta:</td>
                                <td class="text-end">Q{{ number_format($venta['producto']['precio_venta'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Stock Actual:</td>
                                <td class="text-end">
                                    <span class="badge {{ ($venta['producto']['stock'] ?? 0) <= ($venta['producto']['stock_minimo'] ?? 0) ? 'bg-danger' : 'bg-success' }}">
                                        {{ $venta['producto']['stock'] ?? 0 }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                        
                        <a href="{{ route('productos.show', $venta['producto']['id']) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-external-link-alt me-2"></i> Ver Producto
                        </a>
                    </div>
                </div>
                @endif
                
                @if($venta['tipo'] == 'servicio' && !empty($venta['servicio']))
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-concierge-bell me-2"></i> Información del Servicio
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>Código:</td>
                                <td class="text-end">{{ $venta['servicio']['codigo'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>Inversión:</td>
                                <td class="text-end">Q{{ number_format($venta['servicio']['inversion_estimada'] ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Precio Venta:</td>
                                <td class="text-end">Q{{ number_format($venta['servicio']['precio_venta'] ?? 0, 2) }}</td>
                            </tr>
                        </table>
                        
                        <a href="{{ route('servicios.show', $venta['servicio']['id']) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-external-link-alt me-2"></i> Ver Servicio
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.avatar-text {
    color: white;
    font-size: 2rem;
    font-weight: bold;
}

.table-sm td, .table-sm th {
    padding: 0.5rem;
}

.card-title {
    font-size: 1.1rem;
}

.badge {
    font-size: 0.8em;
    padding: 0.3em 0.6em;
}
</style>
@endpush