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
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Filtros --}}
            <div class="row mb-3">
                <div class="col-md-7">
                    <div class="d-flex flex-wrap gap-2">
                        {{-- Estado --}}
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">Todos</button>
                            <button class="btn btn-outline-success  btn-sm filter-btn" data-filter="activo">Activos</button>
                            <button class="btn btn-outline-danger   btn-sm filter-btn" data-filter="inactivo">Inactivos</button>
                        </div>
                        {{-- Rol --}}
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary btn-sm filter-rol-btn active" data-rol="todos">Todos los roles</button>
                            <button class="btn btn-outline-danger   btn-sm filter-rol-btn" data-rol="administrador">Administrador</button>
                            <button class="btn btn-outline-success  btn-sm filter-rol-btn" data-rol="vendedor">Vendedor</button>
                            <button class="btn btn-outline-info     btn-sm filter-rol-btn" data-rol="analista">Analista</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput"
                               placeholder="Buscar por nombre, email o username..."
                               value="{{ $search ?? '' }}">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            @php
                $usuariosData  = $usuarios['data'] ?? (is_array($usuarios) ? $usuarios : []);
                $usuariosLinks = $usuarios['links'] ?? [];
                $usuariosMeta  = [
                    'current_page' => $usuarios['current_page'] ?? 1,
                    'per_page'     => $usuarios['per_page']     ?? 20,
                    'total'        => $usuarios['total']        ?? 0,
                    'from'         => $usuarios['from']         ?? 0,
                    'to'           => $usuarios['to']           ?? 0,
                ];
                $startNumber = (($usuariosMeta['current_page'] - 1) * $usuariosMeta['per_page']) + 1;
            @endphp

            <div class="table-responsive">
                <table class="table table-hover table-striped" id="usersTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th style="width:60px;">No.</th>
                            <th>Usuario</th>
                            <th>Nombre completo</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th style="width:200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuariosData as $index => $usuario)
                        @php
                            $rol      = $usuario['rol'] ?? 'vendedor';
                            $estado   = $usuario['estado'] ?? 'activo';
                            $rolClass = ['administrador' => 'bg-danger', 'vendedor' => 'bg-success', 'analista' => 'bg-info'][$rol] ?? 'bg-secondary';
                            $rolText  = ['administrador' => 'Administrador', 'vendedor' => 'Vendedor', 'analista' => 'Analista'][$rol] ?? $rol;
                            $rolIcon  = ['administrador' => 'crown', 'vendedor' => 'cash-register', 'analista' => 'chart-line'][$rol] ?? 'user';
                        @endphp
                        <tr data-estado="{{ $estado }}" data-rol="{{ $rol }}">
                            <td><span class="fw-bold">{{ $startNumber + $index }}</span></td>
                            <td><strong>{{ $usuario['username'] ?? 'N/A' }}</strong></td>
                            <td>{{ trim(($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? '')) }}</td>
                            <td>
                                @if(!empty($usuario['email']))
                                    <a href="mailto:{{ $usuario['email'] }}">{{ $usuario['email'] }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $rolClass }}">
                                    <i class="fas fa-{{ $rolIcon }} me-1"></i>{{ $rolText }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $estado === 'activo' ? 'bg-success' : 'bg-danger' }}">
                                    <i class="fas fa-{{ $estado === 'activo' ? 'check-circle' : 'times-circle' }} me-1"></i>
                                    {{ ucfirst($estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('usuarios.show', $usuario['id'] ?? '#') }}"
                                       class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('usuarios.edit', $usuario['id'] ?? '#') }}"
                                       class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($estado === 'activo')
                                        <button type="button" class="btn btn-sm btn-secondary"
                                                onclick="cambiarEstado({{ $usuario['id'] ?? 0 }}, 'inactivo')"
                                                title="Desactivar">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-success"
                                                onclick="cambiarEstado({{ $usuario['id'] ?? 0 }}, 'activo')"
                                                title="Activar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmarEliminacion({{ $usuario['id'] ?? 0 }}, '{{ trim(($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? '')) }}')"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="empty-initial">
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No hay usuarios registrados</h5>
                                <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-2"></i>Crear primer usuario
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Contador y paginación --}}
            @if(!empty($usuariosData))
            <div class="d-flex justify-content-between align-items-center mt-3" id="paginacion-container">
                <div class="text-muted" id="contador-wrap">
                    @if($usuariosMeta['total'] > 0)
                        Mostrando {{ $usuariosMeta['from'] }} - {{ $usuariosMeta['to'] }} de
                        <strong>{{ $usuariosMeta['total'] }}</strong> usuarios
                    @else
                        Mostrando {{ count($usuariosData) }} usuarios
                    @endif
                </div>
                @if(!empty($usuariosLinks) && count($usuariosLinks) > 3)
                <nav>
                    <ul class="pagination mb-0">
                        @foreach($usuariosLinks as $link)
                            @if(is_array($link))
                            <li class="page-item {{ ($link['active'] ?? false) ? 'active' : '' }} {{ empty($link['url']) ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $link['url'] ?? '#' }}">{!! $link['label'] ?? '' !!}</a>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>
                @endif
            </div>
            @endif

        </div>
    </div>
</div>

{{-- Modal cambiar estado --}}
<div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="modalEstadoHeader">
                <h5 class="modal-title" id="modalEstadoTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalEstadoBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formCambiarEstado" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn" id="btnConfirmarEstado">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal eliminar --}}
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar al usuario <strong id="usuarioNombre"></strong>?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-exclamation-circle me-2"></i>Esta acción no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" action="">
                    @csrf @method('DELETE')
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
document.addEventListener('DOMContentLoaded', function () {

    let filtroEstado = 'todos';
    let filtroRol    = 'todos';
    let searchTimeout = null;

    // Aplica los filtros de estado, rol y búsqueda sobre las filas de la tabla
    function aplicarFiltros() {
        const texto = document.getElementById('searchInput').value.toLowerCase().trim();
        const rows  = Array.from(document.querySelectorAll('#usersTable tbody tr'))
                          .filter(r => r.id !== 'no-results-row' && r.id !== 'empty-initial');

        let visibles = 0;

        rows.forEach(row => {
            const estadoMatch  = filtroEstado === 'todos' || row.dataset.estado === filtroEstado;
            const rolMatch     = filtroRol    === 'todos' || row.dataset.rol    === filtroRol;
            const textoMatch   = texto === '' || row.textContent.toLowerCase().includes(texto);

            if (estadoMatch && rolMatch && textoMatch) {
                row.style.display = '';
                visibles++;
            } else {
                row.style.display = 'none';
            }
        });

        actualizarContador(visibles);
        mostrarSinResultados(visibles, rows.length);
    }

    // Actualiza el texto del contador con el número de filas visibles
    function actualizarContador(visibles) {
        const wrap = document.getElementById('contador-wrap');
        if (!wrap) return;
        const total = document.querySelectorAll('#usersTable tbody tr:not(#no-results-row):not(#empty-initial)').length;
        wrap.innerHTML = visibles === total
            ? wrap.innerHTML  // sin filtros activos, dejar el texto original de PHP
            : `Mostrando <strong>${visibles}</strong> de ${total} usuarios`;
    }

    // Muestra u oculta la fila de "sin resultados"
    function mostrarSinResultados(visibles, total) {
        let noResults = document.getElementById('no-results-row');
        if (visibles === 0 && total > 0) {
            if (!noResults) {
                noResults = document.createElement('tr');
                noResults.id = 'no-results-row';
                noResults.innerHTML = `
                    <td colspan="7" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron usuarios</h5>
                        <p class="text-muted mb-3">Intenta con otros términos o filtros</p>
                        <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                            <i class="fas fa-undo me-2"></i>Limpiar filtros
                        </button>
                    </td>`;
                document.querySelector('#usersTable tbody').appendChild(noResults);
            }
        } else if (noResults) {
            noResults.remove();
        }
    }

    // Resetea todos los filtros a su estado inicial
    window.limpiarFiltros = function () {
        filtroEstado = 'todos';
        filtroRol    = 'todos';
        document.getElementById('searchInput').value = '';
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === 'todos'));
        document.querySelectorAll('.filter-rol-btn').forEach(b => b.classList.toggle('active', b.dataset.rol === 'todos'));
        aplicarFiltros();
    };

    // Event listeners — estado
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filtroEstado = this.dataset.filter;
            aplicarFiltros();
        });
    });

    // Event listeners — rol
    document.querySelectorAll('.filter-rol-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-rol-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filtroRol = this.dataset.rol;
            aplicarFiltros();
        });
    });

    // Búsqueda con debounce de 300ms
    document.getElementById('searchInput').addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(aplicarFiltros, 300);
    });

    document.getElementById('clearSearch').addEventListener('click', function () {
        document.getElementById('searchInput').value = '';
        aplicarFiltros();
        this.previousElementSibling.focus();
    });
});

// Abre el modal de cambio de estado con estilos según la acción
function cambiarEstado(id, nuevoEstado) {
    if (!id) return;
    const activar = nuevoEstado === 'activo';
    document.getElementById('modalEstadoHeader').className = `modal-header ${activar ? 'bg-success text-white' : 'bg-warning'}`;
    document.getElementById('modalEstadoTitle').textContent = activar ? 'Activar Usuario' : 'Desactivar Usuario';
    document.getElementById('modalEstadoBody').textContent  = activar
        ? '¿Está seguro que desea activar este usuario?'
        : '¿Está seguro que desea desactivar este usuario?';
    document.getElementById('btnConfirmarEstado').className = `btn ${activar ? 'btn-success' : 'btn-warning'}`;
    document.getElementById('btnConfirmarEstado').textContent = activar ? 'Sí, activar' : 'Sí, desactivar';
    document.getElementById('formCambiarEstado').action = `{{ url('usuarios') }}/${id}/cambiar-estado`;

    const prev = document.querySelector('#formCambiarEstado input[name="estado"]');
    if (prev) prev.remove();
    const input = document.createElement('input');
    input.type = 'hidden'; input.name = 'estado'; input.value = nuevoEstado;
    document.getElementById('formCambiarEstado').appendChild(input);

    new bootstrap.Modal(document.getElementById('modalCambiarEstado')).show();
}

// Abre el modal de confirmación de eliminación
function confirmarEliminacion(id, nombre) {
    if (!id) return;
    document.getElementById('usuarioNombre').textContent = nombre;
    document.getElementById('formEliminar').action = `{{ url('usuarios') }}/${id}`;
    new bootstrap.Modal(document.getElementById('modalEliminar')).show();
}
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