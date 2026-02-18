@extends('layouts.app')

@section('title', 'Módulo de Auditoría')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Auditoría</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Módulo de Auditoría
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('auditoria.estadisticas') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar me-2"></i> Estadísticas
                </a>
                <a href="{{ route('auditoria.index') }}" class="btn btn-secondary">
                    <i class="fas fa-sync-alt me-2"></i> Actualizar
                </a>
            </div>
        </div>
        
        <div class="card-body">

            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('auditoria.index') }}" class="row g-3" id="filterForm">
                        <div class="col-md-2">
                            <label class="form-label">Módulo</label>
                            <select name="modulo" class="form-select">
                                <option value="todos" {{ $params['modulo'] == 'todos' ? 'selected' : '' }}>Todos</option>
                                @foreach($modulos as $modulo)
                                    <option value="{{ $modulo }}" {{ $params['modulo'] == $modulo ? 'selected' : '' }}>
                                        {{ ucfirst($modulo) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Acción</label>
                            <select name="accion" class="form-select">
                                <option value="todos" {{ $params['accion'] == 'todos' ? 'selected' : '' }}>Todas</option>
                                <option value="CREAR" {{ $params['accion'] == 'CREAR' ? 'selected' : '' }}>Creaciones</option>
                                <option value="EDITAR" {{ $params['accion'] == 'EDITAR' ? 'selected' : '' }}>Ediciones</option>
                                <option value="ELIMINAR" {{ $params['accion'] == 'ELIMINAR' ? 'selected' : '' }}>Eliminaciones</option>
                                <option value="CAMBIO_ESTADO" {{ $params['accion'] == 'CAMBIO_ESTADO' ? 'selected' : '' }}>Cambios de estado</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" 
                                   value="{{ $params['fecha_inicio'] }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control" 
                                   value="{{ $params['fecha_fin'] }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Búsqueda</label>
                            <input type="text" name="busqueda" class="form-control" 
                                   placeholder="Buscar..." value="{{ $params['busqueda'] }}">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @php
                $auditoriaData = $auditoria['data'] ?? [];
                $auditoriaMeta = $auditoria['meta'] ?? [];
                $auditoriaLinks = $auditoria['links'] ?? [];
            @endphp

            @if(empty($auditoriaData))
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay registros de auditoría</h5>
                    <p class="text-muted">Los movimientos del sistema aparecerán aquí</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Módulo</th>
                                <th>Descripción</th>
                                <th>IP</th>
                                <th style="width: 100px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($auditoriaData as $log)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($log['created_at'])->timezone('America/Guatemala')->format('d/m/Y H:i:s') }}
                                    <small class="text-muted d-block">
                                        {{ \Carbon\Carbon::parse($log['created_at'])->timezone('America/Guatemala')->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ $log['usuario_nombre'] ?? 'Sistema' }}</strong>
                                    <small class="text-muted d-block">{{ $log['usuario_rol'] ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $log['badge_class'] ?? 'bg-secondary' }}">
                                        <i class="fas {{ $log['icono'] ?? 'fa-history' }} me-1"></i>
                                        {{ $log['accion_legible'] ?? $log['accion'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-cube me-1"></i>
                                        {{ ucfirst($log['modulo'] ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>{{ $log['descripcion'] ?? 'N/A' }}</td>
                                <td>
                                    <code>{{ $log['ip_address'] ?? 'N/A' }}</code>
                                </td>
                                <td>
                                    <a href="{{ route('auditoria.show', $log['id']) }}" 
                                       class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if(!empty($auditoriaLinks))
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $auditoriaMeta['from'] ?? 1 }} - 
                        {{ $auditoriaMeta['to'] ?? count($auditoriaData) }} de 
                        {{ $auditoriaMeta['total'] ?? count($auditoriaData) }} registros
                    </div>
                    <nav>
                        <ul class="pagination mb-0">
                            @foreach($auditoriaLinks as $link)
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
    // Auto submit cuando cambian selects
    document.querySelectorAll('select[name="modulo"], select[name="accion"]').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

@push('styles')
<style>
.table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}
code {
    font-size: 0.85em;
    background: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
}
</style>
@endpush