@extends('layouts.app')

@section('title', 'Categorías')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Categorías</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Alertas de sesión --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
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

            {{-- Filtros --}}
            <div class="row mb-4">
                <div class="col-md-7">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">Todos</button>
                        <button type="button" class="btn btn-outline-success  btn-sm filter-btn" data-filter="activo">Activos</button>
                        <button type="button" class="btn btn-outline-danger   btn-sm filter-btn" data-filter="inactivo">Inactivos</button>
                    </div>
                    <div class="btn-group ms-2" role="group">
                        <button type="button" class="btn btn-outline-info      btn-sm" id="expandAll">
                            <i class="fas fa-expand-arrows-alt me-1"></i>Expandir todo
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="collapseAll">
                            <i class="fas fa-compress-arrows-alt me-1"></i>Colapsar todo
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" id="searchInput"
                               placeholder="Buscar categorías...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-3" id="resultCounter" style="display:none;">
                <small class="text-muted"><span id="visibleCount">0</span> categorías encontradas</small>
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
                                <i class="fas fa-plus me-2"></i>Crear Primera Categoría
                            </a>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalCatImagen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: min(560px, 92vw);">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
 
            {{-- Header --}}
            <div class="modal-header py-2 px-3" style="background:#0f172a; border:none;">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-image text-success"></i>
                    <span id="modalCatNombre" class="text-white fw-semibold" style="font-size:0.9rem;"></span>
                </div>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
 
            {{-- Imagen --}}
            <div class="modal-body p-0 text-center bg-dark position-relative" style="min-height:200px;">
                {{-- Spinner mientras carga --}}
                <div id="modalCatSpinner"
                     class="position-absolute top-50 start-50 translate-middle"
                     style="z-index:2;">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
                <img id="modalCatImgSrc"
                     src="#"
                     alt="Imagen"
                     class="img-fluid"
                     style="max-height:70vh; width:auto; display:none; position:relative; z-index:1;"
                     onload="imagenCargada()"
                     onerror="imagenError()">
            </div>
 
            {{-- Footer --}}
            <div class="modal-footer py-2 px-3 justify-content-between"
                 style="background:#f8fafc; border-top:1px solid #e2e8f0;">
                <small class="text-muted" id="modalCatInfo"></small>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
 
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ── ÁRBOL ───────────────────────────────────────────────────── */
.tree-view ul  { padding-left: 20px; list-style: none; }
.tree-view > ul { padding-left: 0; }
.tree-view li  { margin: 8px 0; position: relative; list-style: none; }

.tree-view li::before {
    content: '';
    position: absolute;
    left: 14px; top: -8px; bottom: 0;
    width: 1px;
    background: linear-gradient(to bottom, #e2e8f0, transparent);
}
.tree-view > ul > li::before { display: none; }

/* ── ITEM ────────────────────────────────────────────────────── */
.category-item {
    border-radius: 10px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
    border-left: 3px solid transparent;
    position: relative;
    z-index: 1;
}
.category-item:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    background: #f0fdf4 !important;
    border-left-color: #22c55e !important;
}

/* Colores por nivel */
.level-color-0 { background: #fef2f2; border-left-color: #fca5a5; }
.level-color-1 { background: #f0fdf4; border-left-color: #86efac; }
.level-color-2 { background: #eff6ff; border-left-color: #93c5fd; }
.level-color-3 { background: #faf5ff; border-left-color: #c4b5fd; }
.level-color-4 { background: #fffbeb; border-left-color: #fcd34d; }
.level-color-5 { background: #f0fdfa; border-left-color: #6ee7b7; }

/* Conector horizontal para hijos */
.level-1 .category-item::after,
.level-2 .category-item::after,
.level-3 .category-item::after,
.level-4 .category-item::after,
.level-5 .category-item::after {
    content: '';
    position: absolute;
    left: -22px; top: 50%;
    width: 18px; height: 1px;
    background: #e2e8f0;
    transform: translateY(-50%);
}

/* ── IMAGEN MINIATURA ────────────────────────────────────────── */
.cat-thumb {
    width: 36px; height: 36px;
    border-radius: 7px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
    flex-shrink: 0;
    transition: transform 0.15s;
    cursor: zoom-in; 
}
.category-item:hover .cat-thumb {
    transform: scale(1.08);
    box-shadow: 0 2px 8px rgba(34,197,94,0.3);
}

.cat-thumb-placeholder {
    width: 36px; height: 36px;
    border-radius: 7px;
    background: #f1f5f9;
    border: 1px dashed #cbd5e1;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 0.85rem;
}

/* ── TEXTO ───────────────────────────────────────────────────── */
.category-name { font-weight: 600; color: #0f172a; font-size: 0.9rem; }
.category-desc { font-size: 0.78rem; color: #64748b; }
.badge-estado  { font-size: 0.68rem; padding: 2px 7px; }

/* ── TOGGLE ──────────────────────────────────────────────────── */
.toggle-btn {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: white;
    border: 1px solid #e2e8f0;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; flex-shrink: 0;
    transition: all 0.15s;
    font-size: 0.7rem; color: #64748b;
}
.toggle-btn:hover { background: #f0fdf4; border-color: #22c55e; color: #16a34a; }

/* ── ACCIONES ────────────────────────────────────────────────── */
.category-actions { display: flex; gap: 4px; flex-shrink: 0; }
.category-actions .btn {
    width: 30px; height: 30px;
    padding: 0;
    border-radius: 6px;
    background: white;
    border: 1px solid #e2e8f0;
    display: inline-flex; align-items: center; justify-content: center;
    transition: all 0.15s;
    font-size: 0.8rem;
}
.category-actions .btn:hover {
    background: #f0fdf4;
    border-color: #22c55e;
    transform: scale(1.1);
}

/* ── FILTROS ─────────────────────────────────────────────────── */
.filter-btn.active                         { background: #22c55e; color: white; border-color: #22c55e; }
.filter-btn[data-filter="activo"].active   { background: #22c55e; border-color: #22c55e; }
.filter-btn[data-filter="inactivo"].active { background: #ef4444; border-color: #ef4444; }

/* ── COLLAPSE ────────────────────────────────────────────────── */
.collapse:not(.show) { display: none; }
.collapse.show       { display: block; }

/* ── ANIMACIÓN ───────────────────────────────────────────────── */
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.tree-view li { animation: slideDown 0.2s ease; }

/* ── RESPONSIVE ──────────────────────────────────────────────── */
@media (max-width: 768px) {
    .category-item { flex-direction: column; align-items: flex-start; }
    .category-actions { align-self: flex-end; }
    .tree-view ul { padding-left: 12px; }
}

.flex-1 { flex: 1; }
.min-w-0 { min-width: 0; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentFilter = 'todos';

    function aplicarFiltros() {
        const txt   = document.getElementById('searchInput').value.toLowerCase().trim();
        const items = document.querySelectorAll('.category-item');
        let visible = 0;

        items.forEach(item => {
            const name  = item.querySelector('.category-name')?.textContent.toLowerCase() || '';
            const desc  = item.querySelector('.category-desc')?.textContent.toLowerCase() || '';
            const estado= item.dataset.estado;

            const ok = (currentFilter === 'todos' || estado === currentFilter) &&
                       (txt === '' || name.includes(txt) || desc.includes(txt));

            const li = item.closest('li');
            if (!li) return;

            if (ok) {
                li.style.display = '';
                visible++;
                // Mostrar ancestros
                let parent = li.parentElement?.closest('li');
                while (parent) { parent.style.display = ''; parent = parent.parentElement?.closest('li'); }
            } else {
                li.style.display = 'none';
            }
        });

        const counter   = document.getElementById('resultCounter');
        const countSpan = document.getElementById('visibleCount');
        const hayFiltro = txt !== '' || currentFilter !== 'todos';

        if (hayFiltro) {
            if (countSpan) countSpan.textContent = visible;
            if (counter) counter.style.display = 'block';
            visible === 0 ? mostrarNoResultados() : ocultarNoResultados();
        } else {
            if (counter) counter.style.display = 'none';
            ocultarNoResultados();
        }
    }

    function mostrarNoResultados() {
        if (document.getElementById('no-results-msg')) return;
        const div = document.createElement('div');
        div.id = 'no-results-msg';
        div.className = 'text-center py-5';
        div.innerHTML = `
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No se encontraron categorías</h5>
            <p class="text-muted mb-3">Intenta con otros términos</p>
            <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                <i class="fas fa-undo me-2"></i>Limpiar filtros
            </button>`;
        document.querySelector('.tree-view').appendChild(div);
    }

    function ocultarNoResultados() {
        document.getElementById('no-results-msg')?.remove();
    }

    window.limpiarFiltros = function () {
        currentFilter = 'todos';
        document.getElementById('searchInput').value = '';
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === 'todos'));
        document.getElementById('resultCounter').style.display = 'none';
        ocultarNoResultados();
        document.querySelectorAll('.category-item').forEach(i => { const li = i.closest('li'); if (li) li.style.display = ''; });
    };

    // Filtros estado
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            aplicarFiltros();
        });
    });

    // Búsqueda
    let st;
    document.getElementById('searchInput').addEventListener('keyup', function () {
        clearTimeout(st); st = setTimeout(aplicarFiltros, 300);
    });
    document.getElementById('clearSearch').addEventListener('click', limpiarFiltros);

    // Expandir / Colapsar todo
    document.getElementById('expandAll')?.addEventListener('click', () => {
        document.querySelectorAll('.tree-view .collapse').forEach(el => el.classList.add('show'));
        document.querySelectorAll('[data-bs-toggle="collapse"] i').forEach(i => i.className = 'fas fa-chevron-down');
    });
    document.getElementById('collapseAll')?.addEventListener('click', () => {
        document.querySelectorAll('.tree-view .collapse.show').forEach(el => el.classList.remove('show'));
        document.querySelectorAll('[data-bs-toggle="collapse"] i').forEach(i => i.className = 'fas fa-chevron-right');
    });

    // Toggle individual
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.dataset.target);
            if (!target) return;
            target.classList.toggle('show');
            const icon = this.querySelector('i');
            if (icon) icon.className = target.classList.contains('show') ? 'fas fa-chevron-down' : 'fas fa-chevron-right';
        });
    });
});
</script>
<script>
/**
 * Abre el modal con la imagen de la categoría.
 * Se llama desde onclick="abrirModalImagen(this)" en cada cat-thumb.
 */
function abrirModalImagen(el) {
    const fullUrl = el.dataset.full || el.src;
    const nombre  = el.dataset.nombre || el.alt || 'Imagen';
 
    // Resetear estado
    const img     = document.getElementById('modalCatImgSrc');
    const spinner = document.getElementById('modalCatSpinner');
    const info    = document.getElementById('modalCatInfo');
 
    img.style.display = 'none';
    spinner.style.display = '';
    info.textContent = '';
    document.getElementById('modalCatNombre').textContent = nombre;
 
    // Asignar src — onload/onerror se encargan del resto
    img.src = fullUrl;
 
    new bootstrap.Modal(document.getElementById('modalCatImagen')).show();
}
 
/** Oculta spinner y muestra imagen cuando terminó de cargar */
function imagenCargada() {
    const img     = document.getElementById('modalCatImgSrc');
    const spinner = document.getElementById('modalCatSpinner');
    const info    = document.getElementById('modalCatInfo');
 
    spinner.style.display = 'none';
    img.style.display = '';
 
    // Mostrar dimensiones si están disponibles
    if (img.naturalWidth && img.naturalHeight) {
        info.textContent = img.naturalWidth + ' × ' + img.naturalHeight + ' px';
    }
}
 
/** Muestra error si la imagen no cargó */
function imagenError() {
    const spinner = document.getElementById('modalCatSpinner');
    spinner.innerHTML = `
        <div class="text-center text-muted">
            <i class="fas fa-image-slash fa-2x mb-2 d-block"></i>
            <small>No se pudo cargar la imagen</small>
        </div>`;
}
</script>
@endpush