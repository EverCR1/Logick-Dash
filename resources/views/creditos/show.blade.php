@extends('layouts.app')

@section('title', 'Ver Crédito')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('creditos.index') }}">Créditos</a></li>
    <li class="breadcrumb-item active">{{ $credito['nombre_cliente'] ?? 'Crédito' }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Información del Crédito</h5>
                    <div>
                        <a href="{{ route('creditos.edit', $credito['id'] ?? '#') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($credito))
                    <div class="row">
                        <!-- Información básica -->
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Cliente:</th>
                                    <td>
                                        <strong>{{ $credito['nombre_cliente'] ?? 'N/A' }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        @php
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
                                        @endphp
                                        <span class="badge bg-{{ $estadoColors[$credito['estado'] ?? 'activo'] }}">
                                            {{ $estadoLabels[$credito['estado'] ?? 'activo'] }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Producto/Servicio:</th>
                                    <td>{{ $credito['producto_o_servicio_dado'] ?? 'No especificado' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Crédito:</th>
                                    <td>
                                        {{ isset($credito['fecha_credito']) ? \Carbon\Carbon::parse($credito['fecha_credito'])->format('d/m/Y') : 'N/A' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Capital Total:</th>
                                    <td>
                                        <strong>Q{{ number_format($credito['capital'] ?? 0, 2) }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Capital Restante:</th>
                                    <td>
                                        @if(($credito['capital_restante'] ?? 0) > 0)
                                            <strong class="text-danger">Q{{ number_format($credito['capital_restante'], 2) }}</strong>
                                        @else
                                            <strong class="text-success">Q0.00</strong>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Último Pago:</th>
                                    <td>
                                        @if($credito['fecha_ultimo_pago'])
                                            {{ \Carbon\Carbon::parse($credito['fecha_ultimo_pago'])->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">Q{{ number_format($credito['ultima_cantidad_pagada'] ?? 0, 2) }}</small>
                                        @else
                                            <span class="text-muted">Sin pagos registrados</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Porcentaje Pagado:</th>
                                    <td>
                                        @php
                                            $porcentajePagado = $credito['capital'] > 0 ? 
                                                (($credito['capital'] - $credito['capital_restante']) / $credito['capital']) * 100 : 0;
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 10px;">
                                                <div class="progress-bar bg-{{ $estadoColors[$credito['estado'] ?? 'activo'] }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $porcentajePagado }}%"
                                                     aria-valuenow="{{ $porcentajePagado }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <strong class="ms-2">{{ number_format($porcentajePagado, 1) }}%</strong>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Historial de pagos -->
                    <div class="mt-4">
                        <h6 class="text-muted mb-3">Historial de Pagos</h6>
                        
                        @if(isset($credito['pagos']) && count($credito['pagos']) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Tipo</th>
                                        <th>Observaciones</th>
                                        <th>Fecha Registro</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($credito['pagos'] as $pago)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($pago['fecha_pago'])->format('d/m/Y') }}</td>
                                        <td>
                                            <strong>Q{{ number_format($pago['monto'], 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($pago['tipo'] === 'pago_total')
                                                <span class="badge bg-success">Pago Total</span>
                                            @else
                                                <span class="badge bg-info">Abono</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $pago['observaciones'] ?? 'Sin observaciones' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($pago['created_at'])->format('d/m/Y H:i') }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="1">Total Pagado:</th>
                                        <th colspan="4">
                                            Q{{ number_format($credito['capital'] - $credito['capital_restante'], 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No hay pagos registrados para este crédito.
                        </div>
                        @endif
                    </div>

                    @else
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No se pudo cargar la información del crédito.
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('creditos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver a la lista
                        </a>
                        <div class="d-flex gap-2">
                            <form action="{{ route('creditos.change-status', $credito['id'] ?? '#') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-{{ $credito['estado'] == 'pagado' ? 'warning' : 'success' }}">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    Cambiar Estado
                                </button>
                            </form>
                            
                            @if(($credito['capital_restante'] ?? 0) > 0)
                            <button type="button" class="btn btn-primary" id="btnRegistrarPagoShow">
                                <i class="fas fa-money-bill-wave me-2"></i>Registrar Pago
                            </button>
                            @endif
                            
                            <form action="{{ route('creditos.destroy', $credito['id'] ?? '#') }}" method="POST" 
                                  class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este crédito?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar con opciones de pago -->
        <div class="col-md-4">
            <!-- Panel de pago rápido -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Registrar Pago Rápido</h6>
                </div>
                <div class="card-body">
                    @php
                        $capitalRestante = $credito['capital_restante'] ?? 0;
                    @endphp
                    
                    @if($capitalRestante > 0)
                    <div class="mb-3">
                        <p class="mb-2">
                            <strong>Capital restante:</strong>
                            <span class="float-end text-danger">Q{{ number_format($capitalRestante, 2) }}</span>
                        </p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <!-- Botón para pago total -->
                        <button type="button" class="btn btn-success w-100" id="btnPagoTotalRapido">
                            <i class="fas fa-check-circle me-2"></i>Registrar Pago Total
                        </button>
                        
                        <!-- Botones para abonos comunes -->
                        @php
                            $abonosSugeridos = [
                                round($capitalRestante * 0.25, 2),
                                round($capitalRestante * 0.50, 2),
                                round($capitalRestante * 0.75, 2),
                                $capitalRestante
                            ];
                        @endphp
                        
                        @foreach($abonosSugeridos as $index => $abono)
                            @if($abono > 0 && $abono < $capitalRestante)
                            <button type="button" class="btn btn-outline-primary w-100 btn-abono-rapido" 
                                    data-monto="{{ $abono }}">
                                <i class="fas fa-money-bill-wave me-2"></i>Abono: Q{{ number_format($abono, 2) }}
                            </button>
                            @endif
                        @endforeach
                    </div>
                    @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Crédito completamente pagado.
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Resumen financiero -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Resumen Financiero</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Capital Original:</span>
                            <strong>Q{{ number_format($credito['capital'] ?? 0, 2) }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Pagado:</span>
                            <strong class="text-success">Q{{ number_format(($credito['capital'] ?? 0) - ($credito['capital_restante'] ?? 0), 2) }}</strong>
                        </div>
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Restante:</span>
                            <strong class="{{ ($credito['capital_restante'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                Q{{ number_format($credito['capital_restante'] ?? 0, 2) }}
                            </strong>
                        </div>
                        @if($credito['fecha_ultimo_pago'])
                        <div class="list-group-item d-flex justify-content-between">
                            <span>Último Pago:</span>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($credito['fecha_ultimo_pago'])->format('d/m/Y') }}
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar pago (dinámico) -->
@include('creditos.partials._modal_registrar_pago_dinamico')
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const creditoId = '{{ $credito["id"] ?? "#" }}';
    const cliente = '{{ $credito["nombre_cliente"] ?? "N/A" }}';
    const capitalRestante = {{ $credito['capital_restante'] ?? 0 }};
    
    // Botón principal de registrar pago
    const btnRegistrarPagoShow = document.getElementById('btnRegistrarPagoShow');
    if (btnRegistrarPagoShow) {
        btnRegistrarPagoShow.addEventListener('click', function() {
            if (window.abrirModalPago) {
                window.abrirModalPago(creditoId, cliente, capitalRestante);
            }
        });
    }
    
    // Botón de pago total rápido
    const btnPagoTotalRapido = document.getElementById('btnPagoTotalRapido');
    if (btnPagoTotalRapido) {
        btnPagoTotalRapido.addEventListener('click', function() {
            if (window.abrirModalPago) {
                window.abrirModalPago(creditoId, cliente, capitalRestante);
                
                // Programar para seleccionar pago total después de que se abra el modal
                setTimeout(() => {
                    const tipoPagoTotal = document.getElementById('tipoPagoTotal');
                    if (tipoPagoTotal) {
                        tipoPagoTotal.checked = true;
                        tipoPagoTotal.dispatchEvent(new Event('change'));
                    }
                }, 100);
            }
        });
    }
    
    // Botones de abono rápido
    document.querySelectorAll('.btn-abono-rapido').forEach(btn => {
        btn.addEventListener('click', function() {
            const monto = this.dataset.monto;
            
            if (window.abrirModalPago) {
                window.abrirModalPago(creditoId, cliente, capitalRestante);
                
                // Programar para establecer el monto después de que se abra el modal
                setTimeout(() => {
                    const montoInput = document.getElementById('montoPago');
                    if (montoInput) {
                        montoInput.value = monto;
                        montoInput.dispatchEvent(new Event('input'));
                    }
                }, 100);
            }
        });
    });
});
</script>
@endpush