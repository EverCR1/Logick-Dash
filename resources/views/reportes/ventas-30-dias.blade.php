{{-- resources/views/reportes/ventas-30-dias.blade.php --}}
@extends('layouts.app')

@section('title', 'Ventas Últimos 30 Días')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Ventas 30 Días</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>Ventas de los Últimos 30 Días
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-light active" id="btnMontos">Montos</button>
                        <button type="button" class="btn btn-light" id="btnCantidad">Cantidad</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="ventas30DiasChart" style="height: 400px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Resumen del Período
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $totalVentas = array_sum(array_column($ventas30Dias, 'total'));
                        $totalTransacciones = array_sum(array_column($ventas30Dias, 'cantidad'));
                        $promedioDiario = $totalTransacciones > 0 ? $totalVentas / $totalTransacciones : 0;
                        $diasConVentas = count(array_filter($ventas30Dias, fn($d) => $d['cantidad'] > 0));
                        $totales = array_column($ventas30Dias, 'total');
                        $ventaMaxima = !empty($totales) ? max($totales) : 0;
                        $totalesFiltrados = array_filter($totales, fn($v) => $v > 0);
                        $ventaMinima = !empty($totalesFiltrados) ? min($totalesFiltrados) : 0;
                    @endphp

                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="border p-3 rounded text-center">
                                <h6 class="text-muted">Total Ventas</h6>
                                <h4 class="text-success fw-bold">Q {{ number_format($totalVentas, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border p-3 rounded text-center">
                                <h6 class="text-muted">Transacciones</h6>
                                <h4 class="text-primary fw-bold">{{ number_format($totalTransacciones) }}</h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border p-3 rounded text-center">
                                <h6 class="text-muted">Promedio por Venta</h6>
                                <h4 class="text-info fw-bold">Q {{ number_format($promedioDiario, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border p-3 rounded text-center">
                                <h6 class="text-muted">Días con Ventas</h6>
                                <h4 class="text-warning fw-bold">{{ $diasConVentas }} / 30</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-3 rounded text-center">
                                <h6 class="text-muted">Venta Máxima</h6>
                                <h4 class="text-danger fw-bold">Q {{ number_format($ventaMaxima, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border p-3 rounded text-center">
                                <h6 class="text-muted">Venta Mínima</h6>
                                <h4 class="text-secondary fw-bold">Q {{ number_format($ventaMinima, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>Detalle por Día
                    </h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventas30Dias as $dia)
                            <tr>
                                <td>{{ $dia['fecha'] }}</td>
                                <td class="text-center">
                                    @if($dia['cantidad'] > 0)
                                        <span class="badge bg-success">{{ $dia['cantidad'] }}</span>
                                    @else
                                        <span class="badge bg-secondary">0</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($dia['total'] > 0)
                                        Q {{ number_format($dia['total'], 2) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Definir variables primero
    const ventasData = @json($ventas30Dias);
    const fechas = ventasData.map(item => item.fecha);
    const montos = ventasData.map(item => item.total);
    const cantidades = ventasData.map(item => item.cantidad);
    
    const ctxVentas = document.getElementById('ventas30DiasChart').getContext('2d');
    
    // Inicializar el chart sin callback problemático
    window.ventasChart = new Chart(ctxVentas, {
        type: 'line',
        data: {
            labels: fechas,
            datasets: [{
                label: 'Monto (Q)',
                data: montos,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(75, 192, 192)',
                pointBorderColor: 'white',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.dataset.label === 'Monto (Q)') {
                                label += 'Q ' + context.raw.toFixed(2);
                            } else {
                                label += context.raw;
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        // Usar función anónima normal, no arrow function para evitar problemas con 'this'
                        callback: function(value) {
                            if (window.ventasChart && window.ventasChart.data && window.ventasChart.data.datasets && window.ventasChart.data.datasets[0]) {
                                if (window.ventasChart.data.datasets[0].label === 'Monto (Q)') {
                                    return 'Q' + value;
                                }
                            }
                            return value;
                        }
                    }
                }
            }
        }
    });

    // Toggle entre montos y cantidad
    document.getElementById('btnMontos').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('btnCantidad').classList.remove('active');
        
        if (window.ventasChart) {
            window.ventasChart.data.datasets[0].label = 'Monto (Q)';
            window.ventasChart.data.datasets[0].data = montos;
            window.ventasChart.data.datasets[0].borderColor = 'rgb(75, 192, 192)';
            window.ventasChart.data.datasets[0].backgroundColor = 'rgba(75, 192, 192, 0.1)';
            window.ventasChart.update();
        }
    });

    document.getElementById('btnCantidad').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('btnMontos').classList.remove('active');
        
        if (window.ventasChart) {
            window.ventasChart.data.datasets[0].label = 'Cantidad de Ventas';
            window.ventasChart.data.datasets[0].data = cantidades;
            window.ventasChart.data.datasets[0].borderColor = 'rgb(255, 159, 64)';
            window.ventasChart.data.datasets[0].backgroundColor = 'rgba(255, 159, 64, 0.1)';
            window.ventasChart.update();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.border {
    transition: all 0.3s ease;
}
.border:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
@endpush