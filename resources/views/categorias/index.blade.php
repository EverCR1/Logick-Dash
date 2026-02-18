@extends('layouts.app')

@section('title', 'Categorías')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Categorías</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-sitemap text-primary me-2"></i>Árbol de Categorías
            </h5>
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nueva Categoría
            </a>
            @endif
        </div>
        <div class="card-body">

            <!-- Filtros y búsqueda en tiempo real -->
            <div class="row mb-4">
                <div class="col-md-7">
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
                    
                    <div class="btn-group ms-2" role="group">
                        <button type="button" class="btn btn-outline-info btn-sm" id="expandAll">
                            <i class="fas fa-expand-arrows-alt me-1"></i> Expandir todo
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="collapseAll">
                            <i class="fas fa-compress-arrows-alt me-1"></i> Colapsar todo
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" id="searchInput" 
                               placeholder="Buscar categorías...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Contador de resultados -->
            <div class="mb-3" id="resultCounter" style="display: none;">
                <small class="text-muted">
                    <span id="visibleCount">0</span> categorías encontradas
                </small>
            </div>

            <div class="tree-view">
                @if(count($categorias) > 0)
                    <ul class="list-unstyled" id="categoriaTree">
                        @include('categorias.partials.tree', ['categorias' => $categorias, 'nivel' => 0])
                    </ul>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-sitemap fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay categorías registradas</h5>
                        <p class="text-muted">Comienza creando tu primera categoría</p>
                        @if(in_array($userRole, ['administrador', 'vendedor']))
                        <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Crear Primera Categoría
                        </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
/* Estilos base del árbol */
.tree-view ul {
    padding-left: 20px;
    list-style: none;
}

.tree-view > ul {
    padding-left: 0;
}

.tree-view li {
    margin: 8px 0;
    position: relative;
    list-style: none;
}

/* Estilos para los items de categoría */
.tree-view .category-item {
    border-radius: 12px;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
    border-left: 4px solid;
}

.tree-view .category-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

/* Colores pastel por nivel */
.tree-view .level-color-0 {
    background-color: #f8e3e3 !important;  /* Rosa pastel */
    border-left-color: #ff9e9e !important;
}

.tree-view .level-color-1 {
    background-color: #e3f0e3 !important;  /* Verde pastel */
    border-left-color: #a8d5a8 !important;
}

.tree-view .level-color-2 {
    background-color: #e3e9f8 !important;  /* Azul pastel */
    border-left-color: #a8c0ff !important;
}

.tree-view .level-color-3 {
    background-color: #f3e3f8 !important;  /* Lavanda pastel */
    border-left-color: #d8a8ff !important;
}

.tree-view .level-color-4 {
    background-color: #f8f0e3 !important;  /* Durazno pastel */
    border-left-color: #ffd8a8 !important;
}

.tree-view .level-color-5 {
    background-color: #e3f8f0 !important;  /* Menta pastel */
    border-left-color: #a8ffd8 !important;
}

/* Líneas conectoras */
.tree-view .level-1 .category-item::after,
.tree-view .level-2 .category-item::after,
.tree-view .level-3 .category-item::after,
.tree-view .level-4 .category-item::after,
.tree-view .level-5 .category-item::after {
    content: '';
    position: absolute;
    left: -25px;
    top: 50%;
    width: 20px;
    height: 2px;
    background: #d0d0d0;
    transform: translateY(-50%);
    z-index: 0;
}

.tree-view .category-item .category-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
}

.tree-view .category-item .category-desc {
    color: #6c757d;
    font-size: 0.85rem;
    margin-top: 5px;
    display: block;
}

.tree-view .category-item .badge {
    margin-top: 5px;
    padding: 5px 10px;
    font-size: 0.75rem;
    border-radius: 20px;
}

.tree-view .category-actions {
    display: flex;
    gap: 10px;
}

.tree-view .category-actions .btn {
    padding: 5px 8px;
    border-radius: 8px;
    transition: all 0.2s ease;
    background: white;
    border: 1px solid #e0e0e0;
    min-width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.tree-view .category-actions .btn:hover {
    transform: scale(1.1);
    background: #f8f9fa;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.tree-view .category-actions .btn i {
    font-size: 1rem;
}

.tree-view .toggle-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: white;
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    transition: all 0.2s ease;
    cursor: pointer;
    flex-shrink: 0;
}

.tree-view .toggle-btn:hover {
    transform: scale(1.1);
    background: #f8f9fa;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

.tree-view .toggle-btn i {
    color: #6c757d;
    font-size: 0.8rem;
}

/* Estilos para botones de filtro */
.filter-btn.active {
    background-color: #0d6efd !important;
    color: white !important;
    border-color: #0d6efd !important;
}

.filter-btn[data-filter="activo"].active {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.filter-btn[data-filter="inactivo"].active {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}

/* Animaciones */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.tree-view li {
    animation: slideDown 0.3s ease;
}

/* Asegurar que los collapse funcionen */
.collapse:not(.show) {
    display: none;
}

.collapse.show {
    display: block;
}

/* Líneas verticales para conectar niveles */
.tree-view li {
    position: relative;
}

.tree-view li::before {
    content: '';
    position: absolute;
    left: 14px;
    top: -8px;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #d0d0d0, transparent);
    z-index: 0;
}

.tree-view > ul > li::before {
    display: none;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .tree-view .category-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .tree-view .category-actions {
        align-self: flex-end;
        margin-top: 5px;
    }
    
    .tree-view ul {
        padding-left: 15px;
    }
    
    .tree-view .level-1 .category-item,
    .tree-view .level-2 .category-item,
    .tree-view .level-3 .category-item,
    .tree-view .level-4 .category-item,
    .tree-view .level-5 .category-item {
        margin-left: 15px;
    }
}
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentFilter = 'todos';
    
    // Función para aplicar filtros de búsqueda
    function aplicarFiltros() {
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
        const items = document.querySelectorAll('.category-item');
        let visibleCount = 0;
        
        items.forEach(item => {
            const categoryName = item.querySelector('.category-name')?.textContent.toLowerCase() || '';
            const categoryDesc = item.querySelector('.category-desc')?.textContent.toLowerCase() || '';
            const categoryEstado = item.dataset.estado;
            
            // Verificar filtro de estado
            const estadoMatch = currentFilter === 'todos' || categoryEstado === currentFilter;
            
            // Verificar búsqueda
            const searchMatch = searchText === '' || 
                               categoryName.includes(searchText) || 
                               categoryDesc.includes(searchText);
            
            const categoryItem = item.closest('li');
            if (categoryItem && estadoMatch && searchMatch) {
                categoryItem.style.display = '';
                visibleCount++;
                
                // Mostrar padres si el hijo es visible
                let parent = categoryItem.parentElement?.closest('li');
                while (parent) {
                    parent.style.display = '';
                    parent = parent.parentElement?.closest('li');
                }
            } else if (categoryItem) {
                categoryItem.style.display = 'none';
            }
        });
        
        // Mostrar contador de resultados
        const counter = document.getElementById('resultCounter');
        const visibleSpan = document.getElementById('visibleCount');
        if (searchText !== '' || currentFilter !== 'todos') {
            if (visibleSpan) visibleSpan.textContent = visibleCount;
            if (counter) counter.style.display = 'block';
            
            if (visibleCount === 0) {
                mostrarMensajeNoResultados();
            } else {
                ocultarMensajeNoResultados();
            }
        } else {
            if (counter) counter.style.display = 'none';
            ocultarMensajeNoResultados();
        }
    }
    
    // Función para mostrar mensaje de no resultados
    function mostrarMensajeNoResultados() {
        let noResultsMsg = document.getElementById('no-results-message');
        if (!noResultsMsg) {
            noResultsMsg = document.createElement('div');
            noResultsMsg.id = 'no-results-message';
            noResultsMsg.className = 'text-center py-5';
            noResultsMsg.innerHTML = `
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron categorías</h5>
                <p class="text-muted mb-3">Intenta con otros términos de búsqueda</p>
                <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                    <i class="fas fa-undo me-2"></i>Limpiar filtros
                </button>
            `;
            document.querySelector('.tree-view').appendChild(noResultsMsg);
        }
    }
    
    function ocultarMensajeNoResultados() {
        const noResultsMsg = document.getElementById('no-results-message');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
    }
    
    // Función para limpiar filtros
    window.limpiarFiltros = function() {
        currentFilter = 'todos';
        
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === 'todos') {
                btn.classList.add('active');
            }
        });
        
        document.getElementById('searchInput').value = '';
        document.getElementById('resultCounter').style.display = 'none';
        ocultarMensajeNoResultados();
        
        // Mostrar todos los items
        document.querySelectorAll('.category-item').forEach(item => {
            const li = item.closest('li');
            if (li) li.style.display = '';
        });
    };
    
    // Eventos para filtros de estado
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            aplicarFiltros();
        });
    });
    
    // Búsqueda en tiempo real
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(aplicarFiltros, 300);
        });
    }
    
    // Botón limpiar búsqueda
    const clearSearch = document.getElementById('clearSearch');
    if (clearSearch) {
        clearSearch.addEventListener('click', limpiarFiltros);
    }
    
    // Expandir todo
    const expandAll = document.getElementById('expandAll');
    if (expandAll) {
        expandAll.addEventListener('click', function() {
            document.querySelectorAll('.tree-view .collapse').forEach(el => {
                el.classList.add('show');
            });
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-chevron-down';
                }
            });
        });
    }
    
    // Colapsar todo
    const collapseAll = document.getElementById('collapseAll');
    if (collapseAll) {
        collapseAll.addEventListener('click', function() {
            document.querySelectorAll('.tree-view .collapse.show').forEach(el => {
                el.classList.remove('show');
            });
            document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-chevron-right';
                }
            });
        });
    }
    
    // Manejar collapse en el árbol
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.dataset.target);
            if (target) {
                target.classList.toggle('show');
                const icon = this.querySelector('i');
                if (icon) {
                    if (target.classList.contains('show')) {
                        icon.className = 'fas fa-chevron-down';
                    } else {
                        icon.className = 'fas fa-chevron-right';
                    }
                }
            }
        });
    });
});
</script>
@endpush