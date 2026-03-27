@extends('layouts.app')

@section('title', 'Detalle de Categoría')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
    <li class="breadcrumb-item active">{{ $categoria['nombre'] }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">

            {{-- Alertas --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- Card principal --}}
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tag text-primary me-2"></i>{{ $categoria['nombre'] }}
                    </h5>
                    @if(in_array($userRole, ['administrador', 'vendedor']))
                    <div class="d-flex gap-2">
                        <a href="{{ route('categorias.edit', $categoria['id']) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        <form action="{{ route('categorias.destroy', $categoria['id']) }}"
                              method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta categoría?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash me-1"></i>Eliminar
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <div class="card-body">
                    <div class="row g-4">

                        {{-- Imagen --}}
                        @php
                            $imagen   = $categoria['imagen'] ?? null;
                            $imgUrl   = $imagen['url_medium'] ?? ($imagen['url'] ?? null);
                            $imgThumb = $imagen['url_thumb']  ?? $imgUrl;
                        @endphp

                        @if($imgUrl)
                        <div class="col-md-4 text-center">
                            <img src="{{ $imgUrl }}"
                                 alt="{{ $categoria['nombre'] }}"
                                 class="cat-show-img"
                                 data-full="{{ $imgUrl }}"
                                 onclick="abrirImagen(this)">
                            <p class="text-muted small mt-2 mb-0">
                                <i class="fas fa-image me-1"></i>Imagen de la categoría
                            </p>
                        </div>
                        <div class="col-md-8">
                        @else
                        <div class="col-12">
                        @endif

                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width:130px;">ID</td>
                                    <td><span class="fw-bold">#{{ $categoria['id'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nombre</td>
                                    <td><span class="fw-bold">{{ $categoria['nombre'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Descripción</td>
                                    <td>
                                        @if(!empty($categoria['descripcion']))
                                            {{ $categoria['descripcion'] }}
                                        @else
                                            <span class="text-muted fst-italic">Sin descripción</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Estado</td>
                                    <td>
                                        @php $esActivo = ($categoria['estado'] ?? 'activo') === 'activo'; @endphp
                                        <span class="badge bg-{{ $esActivo ? 'success' : 'secondary' }}">
                                            <i class="fas fa-{{ $esActivo ? 'check-circle' : 'times-circle' }} me-1"></i>
                                            {{ ucfirst($categoria['estado'] ?? 'activo') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Creada</td>
                                    <td>
                                        <small>{{ isset($categoria['created_at']) ? \Carbon\Carbon::parse($categoria['created_at'])->format('d/m/Y H:i') : 'N/A' }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Actualizada</td>
                                    <td>
                                        <small>{{ isset($categoria['updated_at']) ? \Carbon\Carbon::parse($categoria['updated_at'])->format('d/m/Y H:i') : 'N/A' }}</small>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>{{-- row --}}
                </div>
            </div>

            {{-- Jerarquía --}}
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-sitemap me-2 text-primary"></i>Jerarquía
                    </h6>
                </div>
                <div class="card-body">
                    @if(!empty($categoria['parent']))
                        <div class="hierarchy-item parent mb-2">
                            <i class="fas fa-level-up-alt text-warning me-2"></i>
                            <span class="text-muted me-1">Padre:</span>
                            <a href="{{ route('categorias.show', $categoria['parent']['id']) }}" class="text-decoration-none fw-semibold">
                                {{ $categoria['parent']['nombre'] }}
                            </a>
                        </div>
                    @endif

                    <div class="hierarchy-item current mb-2">
                        <i class="fas fa-tag text-primary me-2"></i>
                        <span class="fw-bold">{{ $categoria['nombre'] }}</span>
                        <span class="badge bg-light text-dark ms-2 small">Actual</span>
                    </div>

                    @php
                        $hijos = $categoria['children_recursive'] ?? $categoria['children'] ?? [];
                    @endphp
                    @if(!empty($hijos))
                        <div class="hierarchy-item children">
                            <i class="fas fa-level-down-alt text-info me-2"></i>
                            <span class="text-muted me-1">Subcategorías ({{ count($hijos) }}):</span>
                            <div class="mt-2 ms-4 d-flex flex-wrap gap-1">
                                @foreach($hijos as $hijo)
                                    <a href="{{ route('categorias.show', $hijo['id']) }}"
                                       class="badge bg-info text-white text-decoration-none p-2">
                                        {{ $hijo['nombre'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(empty($categoria['parent']) && empty($hijos))
                        <p class="text-muted mb-0 small">Esta es una categoría raíz sin subcategorías.</p>
                    @endif
                </div>
            </div>

            {{-- Productos --}}
            @if(!empty($categoria['productos']) && count($categoria['productos']) > 0)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-boxes me-2 text-primary"></i>
                        Productos ({{ $categoria['productos_count'] ?? count($categoria['productos']) }})
                    </h6>
                    <a href="{{ route('productos.index', ['categoria' => $categoria['id']]) }}" class="btn btn-sm btn-outline-primary">
                        Ver todos
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-head-dark">
                                <tr>
                                    <th>Producto</th>
                                    <th>SKU</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_slice($categoria['productos'], 0, 5) as $producto)
                                <tr>
                                    <td>
                                        <a href="{{ route('productos.show', $producto['id']) }}" class="text-decoration-none fw-semibold">
                                            {{ $producto['nombre'] }}
                                        </a>
                                    </td>
                                    <td><small class="text-muted">{{ $producto['sku'] ?? 'N/A' }}</small></td>
                                    <td>Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ ($producto['stock'] ?? 0) > 0 ? 'success' : 'danger' }}">
                                            {{ $producto['stock'] ?? 0 }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Sidebar stats --}}
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-6">
                            <div class="stat-mini p-3 rounded text-center" style="background:#eff6ff;">
                                <i class="fas fa-box text-primary fa-lg mb-1"></i>
                                <div class="fw-bold fs-4">{{ $categoria['productos_count'] ?? 0 }}</div>
                                <small class="text-muted">Productos</small>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="stat-mini p-3 rounded text-center" style="background:#f0fdf4;">
                                <i class="fas fa-sitemap text-success fa-lg mb-1"></i>
                                <div class="fw-bold fs-4">{{ count($hijos ?? []) }}</div>
                                <small class="text-muted">Subcategorías</small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Acciones rápidas --}}
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Acciones</h6>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('categorias.edit', $categoria['id']) }}" class="btn btn-outline-warning">
                        <i class="fas fa-edit me-2"></i>Editar categoría
                    </a>
                    <a href="{{ route('categorias.create') }}?parent_id={{ $categoria['id'] }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i>Agregar subcategoría
                    </a>
                    <form action="{{ route('categorias.change-status', $categoria['id']) }}"
                          method="POST"
                          onsubmit="return confirm('¿Cambiar estado de esta categoría?')">
                        @csrf
                        <input type="hidden" name="estado"
                               value="{{ ($categoria['estado'] ?? 'activo') === 'activo' ? 'inactivo' : 'activo' }}">
                        <button type="submit" class="btn btn-outline-{{ ($categoria['estado'] ?? 'activo') === 'activo' ? 'secondary' : 'success' }} w-100">
                            <i class="fas fa-{{ ($categoria['estado'] ?? 'activo') === 'activo' ? 'ban' : 'check' }} me-2"></i>
                            {{ ($categoria['estado'] ?? 'activo') === 'activo' ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>

    <div class="mt-2">
        <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver al listado
        </a>
    </div>
</div>

{{-- Modal imagen --}}
<div class="modal fade" id="modalImagen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body text-center p-0 position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2"
                        data-bs-dismiss="modal" style="z-index:10;"></button>
                <img id="modalImgSrc" src="#" alt="Imagen" class="img-fluid rounded" style="max-height:80vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.cat-show-img {
    width: 100%;
    max-height: 220px;
    object-fit: contain;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    cursor: zoom-in;
    transition: transform 0.2s;
}
.cat-show-img:hover { transform: scale(1.02); }

.hierarchy-item { padding: 8px 12px; border-radius: 8px; }
.hierarchy-item.parent  { background: #fff7ed; border-left: 3px solid #f59e0b; }
.hierarchy-item.current { background: #eff6ff; border-left: 3px solid #3b82f6; }
.hierarchy-item.children{ background: #faf5ff; border-left: 3px solid #8b5cf6; }

.stat-mini { transition: all 0.2s; }
.stat-mini:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
</style>
@endpush

@push('scripts')
<script>
function abrirImagen(el) {
    document.getElementById('modalImgSrc').src = el.dataset.full || el.src;
    new bootstrap.Modal(document.getElementById('modalImagen')).show();
}
</script>
@endpush