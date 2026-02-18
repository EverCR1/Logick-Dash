@extends('layouts.app')

@section('title', 'Gestión de Proveedores')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Proveedores</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-truck me-2"></i>Gestión de Proveedores
            </h5>
            <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Proveedor
            </a>
        </div>
        <div class="card-body">

            <!-- Filtros y búsqueda en tiempo real -->
            <div class="row mb-4">
                <div class="col-md-8">
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
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Buscar por nombre, email o teléfono...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped" id="proveedoresTable">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Estado</th>
                            <th style="width: 250px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedores as $proveedor)
                        <tr data-estado="{{ $proveedor['estado'] ?? 'activo' }}"
                            data-search="{{ strtolower(
                                ($proveedor['nombre'] ?? '') . ' ' . 
                                ($proveedor['email'] ?? '') . ' ' . 
                                ($proveedor['telefono'] ?? '') . ' ' .
                                ($proveedor['direccion'] ?? '')
                            ) }}">
                            <td>{{ $proveedor['id'] }}</td>
                            <td>
                                <strong>{{ $proveedor['nombre'] }}</strong>
                                @if(isset($proveedor['descripcion']) && !empty($proveedor['descripcion']))
                                    <small class="text-muted d-block">{{ Str::limit($proveedor['descripcion'], 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if(isset($proveedor['email']) && !empty($proveedor['email']))
                                    <a href="mailto:{{ $proveedor['email'] }}">
                                        <i class="fas fa-envelope me-1"></i>{{ $proveedor['email'] }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($proveedor['telefono']) && !empty($proveedor['telefono']))
                                    <i class="fas fa-phone me-1"></i>{{ $proveedor['telefono'] }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($proveedor['direccion']) && !empty($proveedor['direccion']))
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($proveedor['direccion'], 30) }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $estadoClass = ($proveedor['estado'] ?? 'activo') == 'activo' ? 'bg-success' : 'bg-danger';
                                    $estadoIcon = ($proveedor['estado'] ?? 'activo') == 'activo' ? 'check-circle' : 'times-circle';
                                @endphp
                                <span class="badge {{ $estadoClass }}">
                                    <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                    {{ ucfirst($proveedor['estado'] ?? 'activo') }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('proveedores.show', $proveedor['id']) }}" 
                                       class="btn btn-sm btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('proveedores.edit', $proveedor['id']) }}" 
                                       class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    @if(($proveedor['estado'] ?? 'activo') == 'activo')
                                    <form action="{{ route('proveedores.changeStatus', $proveedor['id']) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de desactivar este proveedor?')">
                                        @csrf
                                        <input type="hidden" name="estado" value="inactivo">
                                        <button type="submit" class="btn btn-sm btn-secondary" title="Desactivar">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('proveedores.changeStatus', $proveedor['id']) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de activar este proveedor?')">
                                        @csrf
                                        <input type="hidden" name="estado" value="activo">
                                        <button type="submit" class="btn btn-sm btn-success" title="Activar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <form action="{{ route('proveedores.destroy', $proveedor['id']) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar este proveedor? Esta acción no se puede deshacer.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No hay proveedores registrados</p>
                                <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Crear primer proveedor
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentFilter = 'todos';
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#proveedoresTable tbody tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            if (row.id === 'no-results-row') return;
            
            const estado = row.dataset.estado;
            const searchData = row.dataset.search || '';
            
            const estadoMatch = currentFilter === 'todos' || estado === currentFilter;
            const searchMatch = searchText === '' || searchData.includes(searchText);
            
            if (estadoMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        mostrarMensajeNoResultados(visibleCount);
    }
    
    function mostrarMensajeNoResultados(visibleCount) {
        const table = document.getElementById('proveedoresTable');
        const tbody = table.querySelector('tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        const dataRows = Array.from(tbody.querySelectorAll('tr')).filter(row => !row.id);
        
        if (visibleCount === 0 && dataRows.length > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No se encontraron proveedores con los filtros aplicados</p>
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
    
    window.limpiarFiltros = function() {
        currentFilter = 'todos';
        
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === 'todos') {
                btn.classList.add('active');
            }
        });
        
        document.getElementById('searchInput').value = '';
        aplicarFiltros();
    };
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            aplicarFiltros();
        });
    });
    
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(aplicarFiltros, 300);
    });
    
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
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

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .btn-group .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
    
    .row.mb-4 {
        flex-direction: column;
    }
    
    .col-md-4, .col-md-8 {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .btn-group {
        flex-wrap: wrap;
    }
}
</style>
@endpush