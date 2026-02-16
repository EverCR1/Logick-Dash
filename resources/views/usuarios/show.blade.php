@extends('layouts.app')

@section('title', 'Detalle de Usuario - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Detalle de Usuario</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-circle me-2"></i>
                    Detalle del Usuario: {{ $usuario['nombres'] ?? 'N/A' }} {{ $usuario['apellidos'] ?? '' }}
                </h5>
                <div>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    <a href="{{ route('usuarios.edit', $usuario['id']) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(isset($usuario) && $usuario)
                <!-- Estado del usuario -->
                <div class="row mb-4">
                    <div class="col-12">
                        @php
                            $estadoClass = $usuario['estado'] == 'activo' ? 'success' : 'danger';
                            $estadoIcon = $usuario['estado'] == 'activo' ? 'check-circle' : 'times-circle';
                        @endphp
                        <div class="alert alert-{{ $estadoClass }} d-flex align-items-center" role="alert">
                            <i class="fas fa-{{ $estadoIcon }} fa-2x me-3"></i>
                            <div>
                                <strong>Estado del usuario:</strong>
                                <span class="badge bg-{{ $estadoClass }} ms-2">
                                    {{ ucfirst($usuario['estado']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Información personal -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-user me-2"></i>Información Personal
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%"><i class="fas fa-id-card me-2 text-muted"></i>ID:</th>
                                        <td><strong>#{{ $usuario['id'] }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-user me-2 text-muted"></i>Nombres:</th>
                                        <td>{{ $usuario['nombres'] }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-user me-2 text-muted"></i>Apellidos:</th>
                                        <td>{{ $usuario['apellidos'] }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-calendar me-2 text-muted"></i>Fecha Registro:</th>
                                        <td>{{ isset($usuario['created_at']) ? \Carbon\Carbon::parse($usuario['created_at'])->format('d/m/Y H:i') : 'No disponible' }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-sync me-2 text-muted"></i>Última Actualización:</th>
                                        <td>{{ isset($usuario['updated_at']) ? \Carbon\Carbon::parse($usuario['updated_at'])->format('d/m/Y H:i') : 'No disponible' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Información de contacto -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-address-card me-2"></i>Información de Contacto
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%"><i class="fas fa-envelope me-2 text-muted"></i>Email:</th>
                                        <td>
                                            <a href="mailto:{{ $usuario['email'] }}">
                                                {{ $usuario['email'] }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-at me-2 text-muted"></i>Username:</th>
                                        <td>{{ $usuario['username'] }}</td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-phone me-2 text-muted"></i>Teléfono:</th>
                                        <td>
                                            @if(!empty($usuario['telefono']))
                                                <a href="tel:{{ $usuario['telefono'] }}">{{ $usuario['telefono'] }}</a>
                                            @else
                                                <span class="text-muted">No registrado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><i class="fas fa-map-marker-alt me-2 text-muted"></i>Dirección:</th>
                                        <td>
                                            @if(!empty($usuario['direccion']))
                                                {{ $usuario['direccion'] }}
                                            @else
                                                <span class="text-muted">No registrada</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Información de rol y permisos -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-shield-alt me-2"></i>Rol y Permisos
                                </h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $rolClass = [
                                        'administrador' => 'danger',
                                        'vendedor' => 'success',
                                        'analista' => 'info'
                                    ][$usuario['rol']] ?? 'secondary';
                                    
                                    $rolIcon = [
                                        'administrador' => 'crown',
                                        'vendedor' => 'cash-register',
                                        'analista' => 'chart-line'
                                    ][$usuario['rol']] ?? 'user';
                                    
                                    $permisos = [
                                        'administrador' => [
                                            'Acceso total al sistema',
                                            'Gestión de usuarios',
                                            'Configuración del sistema',
                                            'Ver todos los reportes',
                                            'Gestionar productos y ventas'
                                        ],
                                        'vendedor' => [
                                            'Registrar ventas',
                                            'Ver productos',
                                            'Ver clientes',
                                            'Generar facturas',
                                            'Ver sus propias ventas'
                                        ],
                                        'analista' => [
                                            'Ver reportes',
                                            'Ver estadísticas',
                                            'Exportar datos',
                                            'Ver productos y ventas',
                                            'No puede modificar datos'
                                        ]
                                    ];
                                @endphp
                                
                                <div class="text-center mb-3">
                                    <span class="badge bg-{{ $rolClass }} p-3" style="font-size: 1.2rem;">
                                        <i class="fas fa-{{ $rolIcon }} me-2"></i>
                                        {{ ucfirst($usuario['rol']) }}
                                    </span>
                                </div>
                                
                                <h6 class="mt-3 mb-2">Permisos asignados:</h6>
                                <ul class="list-group">
                                    @foreach($permisos[$usuario['rol']] as $permiso)
                                        <li class="list-group-item">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            {{ $permiso }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de actividad -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>Resumen de Actividad
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-3">
                                            <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                            <h3>0</h3>
                                            <small class="text-muted">Ventas realizadas</small>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="border rounded p-3">
                                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                            <h3>-</h3>
                                            <small class="text-muted">Último acceso</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Esta sección mostrará estadísticas detalladas cuando el usuario tenga actividad en el sistema.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    No se encontró información del usuario
                </div>
            @endif
        </div>
        
        @if(isset($usuario) && $usuario)
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                </a>
                <div>
                    @if($usuario['estado'] == 'activo')
                    <button type="button" class="btn btn-warning" onclick="cambiarEstado({{ $usuario['id'] }}, 'inactivo')">
                        <i class="fas fa-ban me-2"></i>Desactivar Usuario
                    </button>
                    @else
                    <button type="button" class="btn btn-success" onclick="cambiarEstado({{ $usuario['id'] }}, 'activo')">
                        <i class="fas fa-check me-2"></i>Activar Usuario
                    </button>
                    @endif
                    <button type="button" class="btn btn-danger" onclick="confirmarEliminacion({{ $usuario['id'] }}, '{{ $usuario['nombres'] }} {{ $usuario['apellidos'] }}')">
                        <i class="fas fa-trash me-2"></i>Eliminar Usuario
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal para cambiar estado -->
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="modalEstadoHeader">
                <h5 class="modal-title" id="modalEstadoTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalEstadoBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formCambiarEstado" method="POST" action="">
                    @csrf
                    @method('POST')
                    <button type="submit" class="btn" id="btnConfirmarEstado">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar el usuario <strong id="usuarioNombre"></strong>?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" action="">
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
@endsection

@push('scripts')
<script>
function cambiarEstado(id, nuevoEstado) {
    const modal = new bootstrap.Modal(document.getElementById('modalCambiarEstado'));
    const title = nuevoEstado === 'activo' ? 'Activar Usuario' : 'Desactivar Usuario';
    const message = nuevoEstado === 'activo' 
        ? '¿Está seguro que desea activar este usuario?' 
        : '¿Está seguro que desea desactivar este usuario?';
    const headerClass = nuevoEstado === 'activo' ? 'bg-success text-white' : 'bg-warning';
    const btnClass = nuevoEstado === 'activo' ? 'btn-success' : 'btn-warning';
    
    document.getElementById('modalEstadoHeader').className = `modal-header ${headerClass}`;
    document.getElementById('modalEstadoTitle').textContent = title;
    document.getElementById('modalEstadoBody').textContent = message;
    document.getElementById('btnConfirmarEstado').className = `btn ${btnClass}`;
    document.getElementById('btnConfirmarEstado').textContent = nuevoEstado === 'activo' ? 'Sí, activar' : 'Sí, desactivar';
    
    document.getElementById('formCambiarEstado').action = `{{ url('usuarios') }}/${id}/cambiar-estado`;
    
    // Eliminar input existente si hay
    const existingInput = document.querySelector('#formCambiarEstado input[name="estado"]');
    if (existingInput) existingInput.remove();
    
    // Agregar campo oculto para el nuevo estado
    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'estado';
    input.value = nuevoEstado;
    document.getElementById('formCambiarEstado').appendChild(input);
    
    modal.show();
}

function confirmarEliminacion(id, nombre) {
    document.getElementById('usuarioNombre').textContent = nombre;
    document.getElementById('formEliminar').action = `{{ url('usuarios') }}/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
</script>
@endpush