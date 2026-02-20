@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

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
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn" data-filter="todos">
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nombre, email o username..." value="{{ $search ?? '' }}">
                    </div>
                </div>
            </div>

            @php
                // Extraer datos de manera segura
                $usuariosData = [];
                $usuariosLinks = [];
                $usuariosMeta = [];
                
                if (isset($usuarios['data'])) {
                    $usuariosData = $usuarios['data'];
                } elseif (isset($usuarios) && is_array($usuarios)) {
                    $usuariosData = $usuarios;
                }
                
                if (isset($usuarios['links']) && is_array($usuarios['links'])) {
                    $usuariosLinks = $usuarios['links'];
                }
                
                if (isset($usuarios['meta']) && is_array($usuarios['meta'])) {
                    $usuariosMeta = $usuarios['meta'];
                }
                
                // Calcular el número inicial para la paginación
                $currentPage = $usuariosMeta['current_page'] ?? 1;
                $perPage = $usuariosMeta['per_page'] ?? 20;
                $startNumber = ($currentPage - 1) * $perPage + 1;
            @endphp

            <div class="table-responsive">
                <table class="table table-hover table-striped" id="usersTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th style="width: 60px;">No.</th>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuariosData as $index => $usuario)
                        <tr data-estado="{{ $usuario['estado'] ?? 'activo' }}">
                            <td>
                                <span class="fw-bold">{{ $startNumber + $index }}</span>
                            </td>
                            <td>
                                <strong>{{ $usuario['username'] ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ ($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? '') }}</td>
                            <td>
                                @if(!empty($usuario['email'] ?? ''))
                                    <a href="mailto:{{ $usuario['email'] }}">{{ $usuario['email'] }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @php
                                    $rol = $usuario['rol'] ?? 'vendedor';
                                    $rolClass = [
                                        'administrador' => 'bg-danger',
                                        'vendedor' => 'bg-success',
                                        'analista' => 'bg-info'
                                    ][$rol] ?? 'bg-secondary';
                                    
                                    $rolText = [
                                        'administrador' => 'Administrador',
                                        'vendedor' => 'Vendedor',
                                        'analista' => 'Analista'
                                    ][$rol] ?? $rol;
                                    
                                    $rolIcon = [
                                        'administrador' => 'crown',
                                        'vendedor' => 'cash-register',
                                        'analista' => 'chart-line'
                                    ][$rol] ?? 'user';
                                @endphp
                                <span class="badge {{ $rolClass }}">
                                    <i class="fas fa-{{ $rolIcon }} me-1"></i>
                                    {{ $rolText }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $estado = $usuario['estado'] ?? 'activo';
                                    $estadoClass = $estado == 'activo' ? 'bg-success' : 'bg-danger';
                                    $estadoIcon = $estado == 'activo' ? 'check-circle' : 'times-circle';
                                @endphp
                                <span class="badge {{ $estadoClass }}">
                                    <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                    {{ ucfirst($estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('usuarios.show', $usuario['id'] ?? '#') }}" class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('usuarios.edit', $usuario['id'] ?? '#') }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(($usuario['estado'] ?? 'activo') == 'activo')
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="cambiarEstado({{ $usuario['id'] ?? 0 }}, 'inactivo')" title="Desactivar">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-sm btn-success" onclick="cambiarEstado({{ $usuario['id'] ?? 0 }}, 'activo')" title="Activar">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacion({{ $usuario['id'] ?? 0 }}, '{{ ($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? '') }}')" title="Eliminar">
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

            <!-- Paginación -->
            @if(!empty($usuariosLinks) && count($usuariosLinks) > 0)
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    @if(!empty($usuariosMeta))
                        Mostrando 
                        {{ $usuariosMeta['from'] ?? $startNumber }} - 
                        {{ $usuariosMeta['to'] ?? ($startNumber + count($usuariosData) - 1) }} de 
                        {{ $usuariosMeta['total'] ?? count($usuariosData) }} usuarios
                    @else
                        Mostrando {{ count($usuariosData) }} usuarios
                    @endif
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0">
                        @foreach($usuariosLinks as $link)
                            @if(is_array($link))
                                <li class="page-item {{ ($link['active'] ?? false) ? 'active' : '' }} {{ empty($link['url']) ? 'disabled' : '' }}">
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
// Variables para filtros
let currentEstadoFilter = 'todos';

// Función para aplicar todos los filtros
function aplicarFiltros() {
    const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#usersTable tbody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        // Verificar si la fila es la de "no resultados"
        if (row.id === 'no-results-row') return;
        
        const estado = row.dataset.estado;
        const rowText = row.textContent.toLowerCase();
        
        // Verificar filtro de estado
        const estadoMatch = currentEstadoFilter === 'todos' || estado === currentEstadoFilter;
        
        // Verificar búsqueda
        const searchMatch = searchText === '' || rowText.includes(searchText);
        
        // Mostrar u ocultar fila
        if (estadoMatch && searchMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    mostrarMensajeNoResultados(visibleCount);
}

// Función para mostrar mensaje cuando no hay resultados
function mostrarMensajeNoResultados(visibleCount) {
    const table = document.getElementById('usersTable');
    let noResultsRow = document.getElementById('no-results-row');
    
    if (visibleCount === 0) {
        if (!noResultsRow) {
            const tbody = table.querySelector('tbody');
            noResultsRow = document.createElement('tr');
            noResultsRow.id = 'no-results-row';
            noResultsRow.innerHTML = `
                <td colspan="7" class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No se encontraron usuarios con los filtros aplicados</p>
                    <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                        <i class="fas fa-undo me-2"></i>Limpiar filtros
                    </button>
                </td>
            `;
            tbody.appendChild(noResultsRow);
        }
    } else {
        if (noResultsRow) {
            noResultsRow.remove();
        }
    }
}

// Función para limpiar filtros
window.limpiarFiltros = function() {
    // Resetear filtro de estado
    currentEstadoFilter = 'todos';
    
    // Actualizar botones de estado
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.filter === 'todos') {
            btn.classList.add('active');
        }
    });
    
    // Limpiar búsqueda
    document.getElementById('searchInput').value = '';
    
    // Aplicar filtros
    aplicarFiltros();
};

// Función para cambiar estado
function cambiarEstado(id, nuevoEstado) {
    if (!id) {
        alert('ID de usuario no válido');
        return;
    }
    
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
    if (!id) {
        alert('ID de usuario no válido');
        return;
    }
    
    document.getElementById('usuarioNombre').textContent = nombre;
    document.getElementById('formEliminar').action = `{{ url('usuarios') }}/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}

// Filtrado por estado
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentEstadoFilter = this.dataset.filter;
        aplicarFiltros();
    });
});

// Búsqueda en tiempo real
let searchTimeout;
document.getElementById('searchInput').addEventListener('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(aplicarFiltros, 300);
});

// Inicializar botones activos
document.addEventListener('DOMContentLoaded', function() {
    // Establecer filtro inicial basado en URL o por defecto 'todos'
    const urlParams = new URLSearchParams(window.location.search);
    const estadoParam = urlParams.get('estado') || 'todos';
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        if (btn.dataset.filter === estadoParam) {
            btn.classList.add('active');
        }
    });
    
    currentEstadoFilter = estadoParam;
    
    // Agregar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

@push('styles')
<style>
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Estilos para botones de filtro activos */
.filter-btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}

.filter-btn[data-filter="activo"].active {
    background-color: #198754;
    border-color: #198754;
}

.filter-btn[data-filter="inactivo"].active {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Estilo para el número correlativo */
th:first-child, td:first-child {
    font-weight: 500;
    color: #495057;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .btn-group .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
    
    .row.mb-3 {
        flex-direction: column;
    }
    
    .col-md-6 {
        margin-bottom: 0.5rem;
    }
}
</style>
@endpush