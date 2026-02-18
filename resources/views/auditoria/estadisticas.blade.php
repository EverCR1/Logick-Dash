@extends('layouts.app')

@section('title', 'Estadísticas de Auditoría')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('auditoria.index') }}">Auditoría</a></li>
    <li class="breadcrumb-item active">Estadísticas</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Selector de período -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('auditoria.estadisticas') }}" class="row align-items-center">
                        <div class="col-md-2">
                            <label class="form-label">Período</label>
                            <select name="dias" class="form-select" onchange="this.form.submit()">
                                <option value="7" {{ $dias == 7 ? 'selected' : '' }}>Últimos 7 días</option>
                                <option value="15" {{ $dias == 15 ? 'selected' : '' }}>Últimos 15 días</option>
                                <option value="30" {{ $dias == 30 ? 'selected' : '' }}>Últimos 30 días</option>
                                <option value="60" {{ $dias == 60 ? 'selected' : '' }}>Últimos 60 días</option>
                                <option value="90" {{ $dias == 90 ? 'selected' : '' }}>Últimos 90 días</option>
                            </select>
                        </div>
                        <div class="col-md-10">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Estadísticas del 
                                {{ \Carbon\Carbon::parse($estadisticas['fecha_inicio'] ?? now()->subDays($dias))->timezone('America/Guatemala')->format('d/m/Y') }} 
                                al 
                                {{ \Carbon\Carbon::parse($estadisticas['fecha_fin'] ?? now())->timezone('America/Guatemala')->format('d/m/Y') }}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Acciones</h6>
                            <h3 class="mb-0">{{ number_format($estadisticas['total_acciones'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-history fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Creaciones</h6>
                            @php
                                $creaciones = collect($estadisticas['acciones_por_tipo'] ?? [])->firstWhere('accion', 'CREAR');
                            @endphp
                            <h3 class="mb-0">{{ number_format($creaciones['total'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-plus-circle fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-dark-50 mb-2">Ediciones</h6>
                            @php
                                $ediciones = collect($estadisticas['acciones_por_tipo'] ?? [])->firstWhere('accion', 'EDITAR');
                            @endphp
                            <h3 class="mb-0">{{ number_format($ediciones['total'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-edit fa-3x text-dark-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Eliminaciones</h6>
                            @php
                                $eliminaciones = collect($estadisticas['acciones_por_tipo'] ?? [])->firstWhere('accion', 'ELIMINAR');
                            @endphp
                            <h3 class="mb-0">{{ number_format($eliminaciones['total'] ?? 0) }}</h3>
                        </div>
                        <i class="fas fa-trash fa-3x text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <!-- Acciones por día -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-2"></i>Acciones por Día
                </div>
                <div class="card-body">
                    <canvas id="accionesPorDiaChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Distribución por acción -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i>Distribución por Acción
                </div>
                <div class="card-body">
                    <canvas id="accionesPorTipoChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Módulos más activos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-cubes me-2"></i>Módulos más Activos
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Módulo</th>
                                    <th class="text-center">Acciones</th>
                                    <th class="text-center">% del total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAcciones = $estadisticas['total_acciones'] ?? 1;
                                @endphp
                                @foreach(($estadisticas['acciones_por_modulo'] ?? []) as $modulo)
                                <tr>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <i class="fas fa-cube me-1"></i>
                                            {{ ucfirst($modulo['modulo']) }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ number_format($modulo['total']) }}</td>
                                    <td class="text-center">
                                        {{ number_format(($modulo['total'] / $totalAcciones) * 100, 1) }}%
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar bg-info" 
                                                 style="width: {{ ($modulo['total'] / $totalAcciones) * 100 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usuarios más activos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users me-2"></i>Usuarios más Activos
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th class="text-center">Rol</th>
                                    <th class="text-center">Acciones</th>
                                    <th class="text-center">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($estadisticas['usuarios_activos'] ?? []) as $usuario)
                                <tr>
                                    <td>
                                        <strong>{{ $usuario['usuario_nombre'] }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $usuario['usuario_rol'] ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">{{ number_format($usuario['total']) }}</td>
                                    <td class="text-center">
                                        {{ number_format(($usuario['total'] / $totalAcciones) * 100, 1) }}%
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

    <!-- Detalles por módulo -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-2"></i>Detalle por Módulo y Acción
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>Módulo</th>
                                    <th class="text-center bg-success text-white">Creaciones</th>
                                    <th class="text-center bg-warning">Ediciones</th>
                                    <th class="text-center bg-danger text-white">Eliminaciones</th>
                                    <th class="text-center bg-info">Cambios Estado</th>
                                    <th class="text-center bg-primary text-white">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($estadisticas['acciones_por_modulo'] ?? []) as $modulo)
                                <tr>
                                    <td>
                                        <strong>{{ ucfirst($modulo['modulo']) }}</strong>
                                    </td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">-</td>
                                    <td class="text-center">
                                        <strong>{{ number_format($modulo['total']) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <th>Total</th>
                                    <th class="text-center">{{ number_format($creaciones['total'] ?? 0) }}</th>
                                    <th class="text-center">{{ number_format($ediciones['total'] ?? 0) }}</th>
                                    <th class="text-center">{{ number_format($eliminaciones['total'] ?? 0) }}</th>
                                    <th class="text-center">
                                        @php
                                            $cambiosEstado = collect($estadisticas['acciones_por_tipo'] ?? [])->firstWhere('accion', 'CAMBIO_ESTADO');
                                        @endphp
                                        {{ number_format($cambiosEstado['total'] ?? 0) }}
                                    </th>
                                    <th class="text-center">{{ number_format($totalAcciones) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
    // Gráfico de acciones por día
    const ctxDia = document.getElementById('accionesPorDiaChart').getContext('2d');
    const accionesPorDia = @json($estadisticas['acciones_por_dia'] ?? []);
    
    // Procesar fechas para mostrarlas correctamente
    const fechas = accionesPorDia.map(item => {
        // Las fechas vienen en formato Y-m-d de la BD (UTC)
        // Las mostramos sin conversión adicional porque son solo fechas
        const partes = item.fecha.split('-');
        return `${partes[2]}/${partes[1]}`; // formato dd/mm
    });
    
    new Chart(ctxDia, {
        type: 'line',
        data: {
            labels: fechas,
            datasets: [{
                label: 'Acciones',
                data: accionesPorDia.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });

    // Gráfico de distribución por tipo de acción
    const ctxTipo = document.getElementById('accionesPorTipoChart').getContext('2d');
    const accionesPorTipo = @json($estadisticas['acciones_por_tipo'] ?? []);
    
    const colores = {
        'CREAR': '#198754',
        'EDITAR': '#ffc107',
        'ELIMINAR': '#dc3545',
        'CAMBIO_ESTADO': '#0dcaf0'
    };

    const labelsMap = {
        'CREAR': 'Creaciones',
        'EDITAR': 'Ediciones',
        'ELIMINAR': 'Eliminaciones',
        'CAMBIO_ESTADO': 'Cambios Estado'
    };

    new Chart(ctxTipo, {
        type: 'doughnut',
        data: {
            labels: accionesPorTipo.map(item => labelsMap[item.accion] || item.accion),
            datasets: [{
                data: accionesPorTipo.map(item => item.total),
                backgroundColor: accionesPorTipo.map(item => colores[item.accion] || '#6c757d'),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            }
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card {
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    font-weight: 600;
}

.progress {
    border-radius: 10px;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.9em;
    padding: 0.5em 0.75em;
}

.text-white-50 {
    color: rgba(255,255,255,0.7) !important;
}

.text-dark-50 {
    color: rgba(0,0,0,0.7) !important;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .col-md-3 .card-body {
        padding: 1rem;
    }
    
    .col-md-3 h3 {
        font-size: 1.5rem;
    }
    
    .col-md-3 i {
        font-size: 2rem;
    }
    
    .table {
        font-size: 0.9rem;
    }
}
</style>
@endpush