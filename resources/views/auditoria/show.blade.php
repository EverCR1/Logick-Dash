@extends('layouts.app')

@section('title', 'Detalle de Auditoría')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('auditoria.index') }}">Auditoría</a></li>
    <li class="breadcrumb-item active">Detalle #{{ $log['id'] ?? '' }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Información principal -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas {{ $log['icono'] ?? 'fa-history' }} me-2"></i>
                        Detalle de Auditoría
                    </h5>
                    <a href="{{ route('auditoria.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="150">ID:</th>
                                    <td><strong>#{{ $log['id'] }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Fecha y Hora:</th>
                                    <td>
                                        {{ \Carbon\Carbon::parse($log['created_at'])->timezone('America/Guatemala')->format('d/m/Y H:i:s') }}
                                        <small class="text-muted d-block">
                                            {{ \Carbon\Carbon::parse($log['created_at'])->timezone('America/Guatemala')->diffForHumans() }}
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Módulo:</th>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <i class="fas fa-cube me-1"></i>
                                            {{ ucfirst($log['modulo'] ?? 'N/A') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tabla:</th>
                                    <td><code>{{ $log['tabla'] ?? 'N/A' }}</code></td>
                                </tr>
                                <tr>
                                    <th>ID Registro:</th>
                                    <td><strong>{{ $log['registro_id'] ?? 'N/A' }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th width="150">Acción:</th>
                                    <td>
                                        <span class="badge {{ $log['badge_class'] ?? 'bg-secondary' }}" style="font-size: 1em;">
                                            <i class="fas {{ $log['icono'] ?? 'fa-history' }} me-2"></i>
                                            {{ $log['accion_legible'] ?? $log['accion'] }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Usuario:</th>
                                    <td>
                                        <strong>{{ $log['usuario_nombre'] ?? 'Sistema' }}</strong>
                                        <small class="text-muted d-block">{{ $log['usuario_rol'] ?? 'N/A' }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>IP:</th>
                                    <td><code>{{ $log['ip_address'] ?? 'N/A' }}</code></td>
                                </tr>
                                <tr>
                                    <th>Navegador:</th>
                                    <td>
                                        <small class="text-truncate" style="max-width: 300px; display: inline-block;" 
                                               title="{{ $log['user_agent'] ?? 'N/A' }}">
                                            {{ $log['user_agent'] ?? 'N/A' }}
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ $log['descripcion'] ?? 'Sin descripción' }}
                    </div>

                    <!-- Valores Anteriores y Nuevos -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white py-2">
                                    <i class="fas fa-history me-2"></i>Valores Anteriores
                                </div>
                                <div class="card-body p-0">
                                    @if(!empty($log['valores_anteriores']))
                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-sm table-striped mb-0">
                                                <thead class="sticky-top bg-light">
                                                    <tr>
                                                        <th width="150">Campo</th>
                                                        <th>Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($log['valores_anteriores'] as $campo => $valor)
                                                        <tr>
                                                            <td><strong>{{ $campo }}</strong></td>
                                                            <td>
                                                                @if(is_array($valor) || is_object($valor))
                                                                    <pre class="mb-0" style="font-size: 0.85em;">{{ json_encode($valor, JSON_PRETTY_PRINT) }}</pre>
                                                                @elseif(is_bool($valor))
                                                                    {{ $valor ? 'true' : 'false' }}
                                                                @elseif($valor === null)
                                                                    <em class="text-muted">null</em>
                                                                @else
                                                                    {{ $valor }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-minus-circle fa-2x mb-2"></i>
                                            <p class="mb-0">No hay valores anteriores (creación)</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white py-2">
                                    <i class="fas fa-check-circle me-2"></i>Valores Nuevos
                                </div>
                                <div class="card-body p-0">
                                    @if(!empty($log['valores_nuevos']))
                                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-sm table-striped mb-0">
                                                <thead class="sticky-top bg-light">
                                                    <tr>
                                                        <th width="150">Campo</th>
                                                        <th>Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($log['valores_nuevos'] as $campo => $valor)
                                                        <tr>
                                                            <td><strong>{{ $campo }}</strong></td>
                                                            <td>
                                                                @if(is_array($valor) || is_object($valor))
                                                                    <pre class="mb-0" style="font-size: 0.85em;">{{ json_encode($valor, JSON_PRETTY_PRINT) }}</pre>
                                                                @elseif(is_bool($valor))
                                                                    {{ $valor ? 'true' : 'false' }}
                                                                @elseif($valor === null)
                                                                    <em class="text-muted">null</em>
                                                                @else
                                                                    {{ $valor }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="fas fa-minus-circle fa-2x mb-2"></i>
                                            <p class="mb-0">No hay valores nuevos (eliminación)</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="col-md-4">
            <!-- Resumen de cambios -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-chart-simple me-2"></i>Resumen de Cambios
                </div>
                <div class="card-body">
                    @php
                        $anteriores = $log['valores_anteriores'] ?? [];
                        $nuevos = $log['valores_nuevos'] ?? [];
                        $cambios = [];
                        
                        if (!empty($anteriores) && !empty($nuevos)) {
                            foreach ($nuevos as $campo => $valor) {
                                if (isset($anteriores[$campo]) && $anteriores[$campo] != $valor) {
                                    $cambios[] = $campo;
                                }
                            }
                        }
                    @endphp

                    @if(!empty($cambios))
                        <h6>Campos modificados:</h6>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach($cambios as $campo)
                                <span class="badge bg-warning text-dark">{{ $campo }}</span>
                            @endforeach
                        </div>
                    @endif

                    <h6>Información adicional:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-calendar me-2 text-primary"></i>
                            <strong>Día:</strong> {{ \Carbon\Carbon::parse($log['created_at'])->timezone('America/Guatemala')->format('l, d \d\e F \d\e Y') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock me-2 text-primary"></i>
                            <strong>Hora exacta:</strong> {{ \Carbon\Carbon::parse($log['created_at'])->timezone('America/Guatemala')->format('H:i:s') }}
                        </li>
                        @if($log['usuario_rol'] ?? false)
                        <li class="mb-2">
                            <i class="fas fa-user-tag me-2 text-primary"></i>
                            <strong>Rol:</strong> {{ ucfirst($log['usuario_rol']) }}
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('auditoria.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Volver al listado
                        </a>
                        <a href="{{ route('auditoria.estadisticas') }}" class="btn btn-outline-info">
                            <i class="fas fa-chart-bar me-2"></i>Ver estadísticas
                        </a>
                        @if($log['tabla'] ?? false && $log['registro_id'] ?? false)
                            @php
                                $rutaDetalle = '';
                                switch($log['modulo']) {
                                    case 'usuarios':
                                        $rutaDetalle = route('usuarios.show', $log['registro_id']);
                                        break;
                                    case 'clientes':
                                        $rutaDetalle = route('clientes.show', $log['registro_id']);
                                        break;
                                }
                            @endphp
                            @if($rutaDetalle)
                                <a href="{{ $rutaDetalle }}" class="btn btn-outline-success" target="_blank">
                                    <i class="fas fa-external-link-alt me-2"></i>Ver registro original
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection