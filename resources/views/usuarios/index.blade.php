@extends('layouts.app')

@section('title', 'Gestión de Usuarios - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>Gestión de Usuarios
            </h5>
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Usuario
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error_relaciones'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>No se puede eliminar el usuario:</strong>
                    <div class="mt-2">
                        {!! session('error_relaciones') !!}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filtros y búsqueda -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">
                            Todos
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm filter-btn" data-filter="activo">
                            Activos
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm filter-btn" data-filter="inactivo">
                            Inactivos
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nombre, email o username...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped" id="usersTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr data-estado="{{ $usuario['estado'] }}">
                            <td>{{ $usuario['id'] }}</td>
                            <td>
                                <strong>{{ $usuario['username'] }}</strong>
                            </td>
                            <td>{{ $usuario['nombres'] }} {{ $usuario['apellidos'] }}</td>
                            <td>
                                <a href="mailto:{{ $usuario['email'] }}">{{ $usuario['email'] }}</a>
                            </td>
                            <td>
                                @php
                                    $rolClass = [
                                        'administrador' => 'bg-danger',
                                        'vendedor' => 'bg-success',
                                        'analista' => 'bg-info'
                                    ][$usuario['rol']] ?? 'bg-secondary';
                                    
                                    $rolText = [
                                        'administrador' => 'Administrador',
                                        'vendedor' => 'Vendedor',
                                        'analista' => 'Analista'
                                    ][$usuario['rol']] ?? $usuario['rol'];
                                @endphp
                                <span class="badge {{ $rolClass }}">
                                    <i class="fas fa-{{ $usuario['rol'] == 'administrador' ? 'crown' : ($usuario['rol'] == 'vendedor' ? 'cash-register' : 'chart-line') }} me-1"></i>
                                    {{ $rolText }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $estadoClass = $usuario['estado'] == 'activo' ? 'bg-success' : 'bg-danger';
                                    $estadoIcon = $usuario['estado'] == 'activo' ? 'check-circle' : 'times-circle';
                                @endphp
                                <span class="badge {{ $estadoClass }}">
                                    <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                    {{ ucfirst($usuario['estado']) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('usuarios.show', $usuario['id']) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('usuarios.edit', $usuario['id']) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($usuario['estado'] == 'activo')
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="cambiarEstado({{ $usuario['id'] }}, 'inactivo')" title="Desactivar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-sm btn-success" onclick="cambiarEstado({{ $usuario['id'] }}, 'activo')" title="Activar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacion({{ $usuario['id'] }}, '{{ $usuario['nombres'] }} {{ $usuario['apellidos'] }}')" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay usuarios registrados</p>
                                <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-2"></i>Crear primer usuario
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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
// Función para cambiar estado
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
    
    const existingInput = document.querySelector('#formCambiarEstado input[name="estado"]');
    if (existingInput) existingInput.remove();
    
    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'estado';
    input.value = nuevoEstado;
    document.getElementById('formCambiarEstado').appendChild(input);
    
    modal.show();
}

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    document.getElementById('usuarioNombre').textContent = nombre;
    document.getElementById('formEliminar').action = `{{ url('usuarios') }}/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

// Filtrado por estado
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        const rows = document.querySelectorAll('#usersTable tbody tr');
        
        rows.forEach(row => {
            if (filter === 'todos') {
                row.style.display = '';
            } else {
                const estado = row.dataset.estado;
                row.style.display = estado === filter ? '' : 'none';
            }
        });
    });
});

// Búsqueda en tiempo real
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
});
</script>
@endpush