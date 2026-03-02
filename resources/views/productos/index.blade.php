@extends('layouts.app')

@section('title', 'Productos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Productos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-boxes me-2"></i>Gestión de Productos
            </h5>
            <a href="{{ route('productos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nuevo Producto
            </a>
        </div>

        <div class="card-body">

            {{-- Fila 1: Estado, Stock, Ordenamiento --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap gap-2 align-items-center">

                        {{-- Filtro estado --}}
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-secondary btn-sm filter-btn active" data-filter="todos">Todos</button>
                            <button class="btn btn-outline-success  btn-sm filter-btn" data-filter="activo">Activos</button>
                            <button class="btn btn-outline-danger   btn-sm filter-btn" data-filter="inactivo">Inactivos</button>
                        </div>

                        {{-- Filtro stock --}}
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-info      btn-sm filter-stock-btn active" data-stock="todos">Todo stock</button>
                            <button class="btn btn-outline-danger    btn-sm filter-stock-btn" data-stock="bajo">Stock bajo</button>
                            <button class="btn btn-outline-success   btn-sm filter-stock-btn" data-stock="disponible">Con stock</button>
                            <button class="btn btn-outline-secondary btn-sm filter-stock-btn" data-stock="agotado">Agotado</button>
                        </div>

                        {{-- Ordenamiento --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    id="sortDropdownBtn" data-bs-toggle="dropdown">
                                <i class="fas fa-sort me-1"></i><span id="sortLabel">Nombre A-Z</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item sort-option active" href="#" data-sort="nombre_asc">Nombre A-Z</a></li>
                                <li><a class="dropdown-item sort-option"        href="#" data-sort="nombre_desc">Nombre Z-A</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="precio_asc">Precio menor a mayor</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="precio_desc">Precio mayor a menor</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="stock_asc">Stock menor a mayor</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="stock_desc">Stock mayor a menor</a></li>
                            </ul>
                        </div>

                        {{-- Categorías --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-tag me-1"></i> Categorías
                            </button>
                            <div class="dropdown-menu p-3" style="min-width:250px; max-height:300px; overflow-y:auto;">
                                <input type="text" class="form-control form-control-sm mb-2"
                                       id="filterCategoriaText" placeholder="Buscar categoría...">
                                <div id="categoriaList"></div>
                            </div>
                        </div>

                        {{-- Proveedores --}}
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-building me-1"></i> Proveedores
                            </button>
                            <div class="dropdown-menu p-3" style="min-width:250px; max-height:300px; overflow-y:auto;">
                                <input type="text" class="form-control form-control-sm mb-2"
                                       id="filterProveedorText" placeholder="Buscar proveedor...">
                                <div id="proveedorList"></div>
                            </div>
                        </div>

                        {{-- Limpiar --}}
                        <button class="btn btn-sm btn-outline-danger" id="btnLimpiarFiltros">
                            <i class="fas fa-undo me-1"></i> Limpiar filtros
                        </button>

                    </div>
                </div>
            </div>

            {{-- Fila 2: Búsqueda --}}
            <div class="row mb-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput"
                               placeholder="Buscar por nombre, SKU, marca, color, descripción o ubicación...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Datos iniciales PHP --}}
            @php
                $productosData  = $productos['data']  ?? (is_array($productos) ? $productos : []);
                $productosLinks = $productos['links'] ?? [];
                $productosMeta  = [
                    'total' => $productos['total'] ?? 0,
                    'from'  => $productos['from']  ?? 0,
                    'to'    => $productos['to']    ?? 0,
                ];
            @endphp

            {{-- Tabla siempre en el DOM --}}
            <div id="tabla-container">

                <div id="empty-state" class="text-center py-5"
                     style="{{ empty($productosData) ? '' : 'display:none;' }}">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay productos registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer producto</p>
                    <a href="{{ route('productos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Producto
                    </a>
                </div>

                <div class="table-responsive" id="table-wrapper"
                     style="{{ empty($productosData) ? 'display:none;' : '' }}">
                    <table class="table table-hover table-striped" id="productosTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width:70px;">Imagen</th>
                                <th>SKU</th>
                                <th>Producto</th>
                                <th>Proveedor</th>
                                <th>Precios</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th style="width:120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosData as $producto)
                            @php
                                $imagenPrincipal = null;
                                $urlImagen       = null;
                                if (!empty($producto['imagenes'])) {
                                    foreach ($producto['imagenes'] as $img) {
                                        if ($img['es_principal'] ?? false) { $imagenPrincipal = $img; break; }
                                    }
                                    if (!$imagenPrincipal) $imagenPrincipal = $producto['imagenes'][0];
                                    $urlImagen = $imagenPrincipal['url_thumb'] ?? $imagenPrincipal['url_medium'] ?? $imagenPrincipal['url'] ?? null;
                                }
                                $stock      = $producto['stock'] ?? 0;
                                $stockMin   = $producto['stock_minimo'] ?? 1;
                                $stockClass = $stock <= 0 ? 'secondary' : ($stock <= $stockMin ? 'danger' : ($stock <= $stockMin * 2 ? 'warning' : 'success'));
                                $stockLabel = $stock <= 0 ? 'Agotado' : ($stock <= $stockMin ? 'Bajo' : 'Normal');
                                $catIds     = array_column($producto['categorias'] ?? [], 'id');
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if($urlImagen)
                                        <img src="{{ $urlImagen }}" alt="{{ $producto['nombre'] ?? '' }}"
                                             class="img-thumbnail product-list-image"
                                             style="width:60px;height:60px;object-fit:cover;cursor:pointer;"
                                             onclick="abrirModalImagen('{{ $urlImagen }}','{{ $producto['nombre'] ?? '' }}')">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                             style="width:60px;height:60px;">
                                            <i class="fas fa-box-open text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $producto['sku'] ?? '' }}</strong>
                                    @if(!empty($producto['codigo_barras']))
                                        <br><small class="text-muted">{{ $producto['codigo_barras'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $producto['nombre'] ?? '' }}</strong><br>
                                    <small class="text-muted">{{ $producto['marca'] ?? 'Sin marca' }} | {{ $producto['color'] ?? 'Sin color' }}</small><br>
                                    @foreach($producto['categorias'] ?? [] as $cat)
                                        <span class="badge bg-secondary me-1 mb-1">{{ $cat['nombre'] ?? '' }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $producto['proveedor']['nombre'] ?? 'N/A' }}</td>
                                <td>
                                    <small>Compra: <strong>Q{{ number_format($producto['precio_compra'] ?? 0, 2) }}</strong></small><br>
                                    <small>Venta:
                                        @if(!empty($producto['precio_oferta']))
                                            <span class="text-decoration-line-through text-muted">Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</span>
                                            <strong class="text-danger">Q{{ number_format($producto['precio_oferta'], 2) }}</strong>
                                        @else
                                            <strong>Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</strong>
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $stockClass }}">{{ $stock }} unidades</span>
                                    <small class="d-block text-muted">{{ $stockLabel }} (Mín: {{ $stockMin }})</small>
                                </td>
                                <td>
                                    <span class="badge {{ ($producto['estado'] ?? '') === 'activo' ? 'bg-success' : 'bg-danger' }}">
                                        <i class="fas fa-{{ ($producto['estado'] ?? '') === 'activo' ? 'check-circle' : 'times-circle' }} me-1"></i>
                                        {{ $producto['estado'] ?? 'desconocido' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('productos.show', $producto['id'] ?? '#') }}" class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('productos.edit', $producto['id'] ?? '#') }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('productos.destroy', $producto['id'] ?? '#') }}" method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Contador y paginación --}}
                <div class="d-flex justify-content-between align-items-center mt-3"
                     id="paginacion-container"
                     style="{{ empty($productosData) ? 'display:none;' : '' }}">
                    <div class="text-muted" id="contador-wrap">
                        @if(($productosMeta['total'] ?? 0) > 0)
                            Mostrando {{ $productosMeta['from'] }} - {{ $productosMeta['to'] }} de
                            <strong>{{ $productosMeta['total'] }}</strong> productos
                        @else
                            Mostrando {{ count($productosData) }} productos
                        @endif
                    </div>
                    <nav>
                        <div id="paginacion-wrap">
                            <ul class="pagination mb-0">
                                @foreach($productosLinks as $link)
                                    @if(is_array($link))
                                    <li class="page-item {{ ($link['active'] ?? false) ? 'active' : '' }} {{ empty($link['url']) ? 'disabled' : '' }}">
                                        <a class="page-link" href="{{ $link['url'] ?? '#' }}">{!! $link['label'] ?? '' !!}</a>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </nav>
                </div>

            </div>{{-- fin #tabla-container --}}

        </div>
    </div>
</div>

{{-- Modal imagen --}}
<div class="modal fade" id="modalImagen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagenTitulo">Imagen del producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="" id="modalImagenSrc" class="img-fluid" style="max-height:70vh;width:auto;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Datos de categorías y proveedores disponibles para los dropdowns de filtro
const categoriasDisponibles = @json($categoriasParaFiltros);
const proveedoresDisponibles = @json($proveedoresParaFiltros);

document.addEventListener('DOMContentLoaded', function () {

    // Estado actual de todos los filtros activos
    let estado        = 'todos';
    let stock         = 'todos';
    let categoriaId   = '';
    let proveedorId   = '';
    let sort          = 'nombre_asc';
    let search        = '';
    let searchTimeout = null;

    // Referencias al DOM reutilizadas en múltiples funciones
    const getTabla       = () => document.querySelector('#productosTable tbody');
    const paginacionWrap = document.getElementById('paginacion-wrap');
    const contadorWrap   = document.getElementById('contador-wrap');
    const tableWrapper   = document.getElementById('table-wrapper');
    const paginacionCont = document.getElementById('paginacion-container');
    const emptyState     = document.getElementById('empty-state');

    // Construye los radios de categoría y proveedor en los dropdowns
    function construirDropdowns() {
        const buildRadios = (containerId, items, name, idKey, labelKey) => {
            const container = document.getElementById(containerId);
            if (!container) return;
            let html = `<div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="${name}" id="${name}Todos" value="" checked>
                <label class="form-check-label" for="${name}Todos">Todos</label>
            </div>`;
            items.forEach(item => {
                html += `<div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="${name}" id="${name}_${item[idKey]}" value="${item[idKey]}">
                    <label class="form-check-label" for="${name}_${item[idKey]}">${item[labelKey]}</label>
                </div>`;
            });
            container.innerHTML = html;
        };

        buildRadios('categoriaList',  categoriasDisponibles,  'categoriaFilter',  'id', 'nombre');
        buildRadios('proveedorList',   proveedoresDisponibles, 'proveedorFilter',  'id', 'nombre');

        // Eventos de selección en categorías y proveedores
        document.querySelectorAll('input[name="categoriaFilter"]').forEach(r => {
            r.addEventListener('change', function () { categoriaId = this.value; fetchFiltrado(); });
        });
        document.querySelectorAll('input[name="proveedorFilter"]').forEach(r => {
            r.addEventListener('change', function () { proveedorId = this.value; fetchFiltrado(); });
        });

        // Búsqueda dentro de los dropdowns
        document.getElementById('filterCategoriaText')?.addEventListener('keyup', function () {
            const txt = this.value.toLowerCase();
            document.querySelectorAll('#categoriaList .form-check').forEach(el => {
                el.style.display = el.querySelector('label').textContent.toLowerCase().includes(txt) ? '' : 'none';
            });
        });
        document.getElementById('filterProveedorText')?.addEventListener('keyup', function () {
            const txt = this.value.toLowerCase();
            document.querySelectorAll('#proveedorList .form-check').forEach(el => {
                el.style.display = el.querySelector('label').textContent.toLowerCase().includes(txt) ? '' : 'none';
            });
        });
    }

    // Muestra la tabla y oculta el estado vacío
    function mostrarTabla() {
        if (tableWrapper)   tableWrapper.style.display   = '';
        if (paginacionCont) paginacionCont.style.display = '';
        if (emptyState)     emptyState.style.display     = 'none';
    }

    // Muestra el estado vacío con un mensaje y botón de limpiar
    function mostrarEmptyState(msg) {
        if (tableWrapper)   tableWrapper.style.display   = 'none';
        if (paginacionCont) paginacionCont.style.display = 'none';
        if (emptyState) {
            emptyState.style.display = '';
            emptyState.innerHTML = `
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">${msg}</h5>
                <p class="text-muted mb-3">Intenta con otros términos o filtros</p>
                <button class="btn btn-sm btn-primary" onclick="limpiarFiltros()">
                    <i class="fas fa-undo me-2"></i>Limpiar filtros
                </button>`;
        }
    }

    // Envía la petición AJAX con todos los filtros activos al endpoint de filtrado
    function fetchFiltrado(page = 1) {
        mostrarLoader();

        const params = new URLSearchParams({ estado, stock, sort, search, page,
            categoria_id: categoriaId, proveedor_id: proveedorId });

        fetch(`{{ route('productos.filter') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) renderTabla(data.productos);
            else mostrarError('Error al filtrar productos');
        })
        .catch(() => mostrarError('Error de conexión'));
    }

    // Construye la imagen de miniatura para cada fila de la tabla
    function buildImagen(imagenes) {
        if (!imagenes || imagenes.length === 0) {
            return `<div class="bg-light d-flex align-items-center justify-content-center rounded" style="width:60px;height:60px;">
                        <i class="fas fa-box-open text-muted"></i>
                    </div>`;
        }
        const principal = imagenes.find(i => i.es_principal) ?? imagenes[0];
        const url = principal.url_thumb ?? principal.url_medium ?? principal.url ?? '';
        const nombre = principal.nombre_original ?? 'producto';
        return url
            ? `<img src="${url}" alt="${nombre}" class="img-thumbnail product-list-image"
                    style="width:60px;height:60px;object-fit:cover;cursor:pointer;"
                    onclick="abrirModalImagen('${url}','${nombre}')">`
            : `<div class="bg-light d-flex align-items-center justify-content-center rounded" style="width:60px;height:60px;">
                   <i class="fas fa-box-open text-muted"></i>
               </div>`;
    }

    // Transforma los datos paginados de la API en filas HTML e inyecta en la tabla
    function renderTabla(paginado) {
        const registros = paginado.data  ?? [];
        const links     = paginado.links ?? [];
        const total     = paginado.total ?? 0;
        const from      = paginado.from  ?? 0;
        const to        = paginado.to    ?? 0;

        if (registros.length === 0) {
            mostrarEmptyState('No se encontraron productos');
            actualizarContador(0, 0, 0);
            actualizarPaginacion([]);
            return;
        }

        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;

        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const fmt  = (val, dec = 2) => parseFloat(val ?? 0).toLocaleString('es-GT', { minimumFractionDigits: dec });

        tbody.innerHTML = '';

        registros.forEach(p => {
            const s      = parseInt(p.stock ?? 0);
            const sMin   = parseInt(p.stock_minimo ?? 1);
            const sClass = s <= 0 ? 'secondary' : (s <= sMin ? 'danger' : (s <= sMin * 2 ? 'warning' : 'success'));
            const sLabel = s <= 0 ? 'Agotado' : (s <= sMin ? 'Bajo' : 'Normal');
            const activo = (p.estado ?? '') === 'activo';
            const precioOferta = p.precio_oferta
                ? `<span class="text-decoration-line-through text-muted">Q${fmt(p.precio_venta)}</span>
                   <strong class="text-danger">Q${fmt(p.precio_oferta)}</strong>`
                : `<strong>Q${fmt(p.precio_venta)}</strong>`;

            const cats = (p.categorias ?? []).map(c => `<span class="badge bg-secondary me-1 mb-1">${c.nombre}</span>`).join('');

            const showUrl   = `/productos/${p.id}`;
            const editUrl   = `/productos/${p.id}/editar`;
            const deleteUrl = `/productos/${p.id}`;

            tbody.insertAdjacentHTML('beforeend', `
                <tr>
                    <td class="text-center">${buildImagen(p.imagenes)}</td>
                    <td>
                        <strong>${p.sku ?? ''}</strong>
                        ${p.codigo_barras ? `<br><small class="text-muted">${p.codigo_barras}</small>` : ''}
                    </td>
                    <td>
                        <strong>${p.nombre ?? ''}</strong><br>
                        <small class="text-muted">${p.marca ?? 'Sin marca'} | ${p.color ?? 'Sin color'}</small><br>
                        ${cats}
                    </td>
                    <td>${p.proveedor?.nombre ?? 'N/A'}</td>
                    <td>
                        <small>Compra: <strong>Q${fmt(p.precio_compra)}</strong></small><br>
                        <small>Venta: ${precioOferta}</small>
                    </td>
                    <td>
                        <span class="badge bg-${sClass}">${s} unidades</span>
                        <small class="d-block text-muted">${sLabel} (Mín: ${sMin})</small>
                    </td>
                    <td>
                        <span class="badge ${activo ? 'bg-success' : 'bg-danger'}">
                            <i class="fas fa-${activo ? 'check-circle' : 'times-circle'} me-1"></i>
                            ${p.estado ?? 'desconocido'}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="${showUrl}"  class="btn btn-sm btn-info"    title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="${editUrl}"  class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                            <form action="${deleteUrl}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.')">
                                <input type="hidden" name="_token"  value="${csrf}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>`);
        });

        actualizarContador(from, to, total);
        actualizarPaginacion(links);
    }

    // Actualiza el texto del contador de resultados
    function actualizarContador(from, to, total) {
        if (!contadorWrap) return;
        contadorWrap.innerHTML = total > 0
            ? `Mostrando ${from} - ${to} de <strong>${total}</strong> productos encontrados`
            : 'Sin resultados';
    }

    // Renderiza los botones de paginación según los links devueltos por la API
    function actualizarPaginacion(links) {
        if (!paginacionWrap) return;
        if (!links || links.length <= 3) { paginacionWrap.innerHTML = ''; return; }
        let html = '<ul class="pagination mb-0">';
        links.forEach(link => {
            const active   = link.active ? 'active'   : '';
            const disabled = !link.url   ? 'disabled' : '';
            let pageNum = null;
            if (link.url) {
                const m = link.url.match(/[?&]page=(\d+)/);
                pageNum = m ? m[1] : null;
            }
            const click = pageNum ? `onclick="cambiarPagina(${pageNum}); return false;"` : '';
            html += `<li class="page-item ${active} ${disabled}">
                        <a class="page-link" href="#" ${click}>${link.label}</a>
                     </li>`;
        });
        html += '</ul>';
        paginacionWrap.innerHTML = html;
    }

    function mostrarLoader() {
        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;
        tbody.innerHTML = `
            <tr><td colspan="8" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="text-muted mt-2 mb-0">Buscando productos...</p>
            </td></tr>`;
    }

    function mostrarError(msg) {
        mostrarTabla();
        const tbody = getTabla();
        if (!tbody) return;
        tbody.innerHTML = `
            <tr><td colspan="8" class="text-center py-4 text-danger">
                <i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>${msg}
            </td></tr>`;
    }

    // Cambia a la página indicada manteniendo los filtros activos
    window.cambiarPagina = function (page) {
        fetchFiltrado(page);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // Resetea todos los filtros y regarga el index original
    window.limpiarFiltros = function () {
        estado = 'todos'; stock = 'todos'; categoriaId = ''; proveedorId = ''; sort = 'nombre_asc'; search = '';

        document.getElementById('searchInput').value = '';
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.toggle('active', b.dataset.filter === 'todos'));
        document.querySelectorAll('.filter-stock-btn').forEach(b => b.classList.toggle('active', b.dataset.stock === 'todos'));
        document.querySelectorAll('.sort-option').forEach(b => b.classList.toggle('active', b.dataset.sort === 'nombre_asc'));
        document.getElementById('sortLabel').textContent = 'Nombre A-Z';

        const catTodos  = document.getElementById('categoriaFilterTodos');
        const provTodos = document.getElementById('proveedorFilterTodos');
        if (catTodos)  catTodos.checked  = true;
        if (provTodos) provTodos.checked = true;

        window.location.href = "{{ route('productos.index') }}";
    };

    // Event listeners
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            estado = this.dataset.filter;
            fetchFiltrado();
        });
    });

    document.querySelectorAll('.filter-stock-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-stock-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            stock = this.dataset.stock;
            fetchFiltrado();
        });
    });

    document.querySelectorAll('.sort-option').forEach(opt => {
        opt.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelectorAll('.sort-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('sortLabel').textContent = this.textContent.trim();
            sort = this.dataset.sort;
            fetchFiltrado();
        });
    });

    document.getElementById('searchInput').addEventListener('keyup', function () {
        clearTimeout(searchTimeout);
        search = this.value;
        searchTimeout = setTimeout(() => fetchFiltrado(), 350);
    });

    document.getElementById('clearSearch').addEventListener('click', function () {
        document.getElementById('searchInput').value = '';
        search = '';
        fetchFiltrado();
        document.getElementById('searchInput').focus();
    });

    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);

    construirDropdowns();
});

// Abre el modal con la imagen del producto ampliada
function abrirModalImagen(src, titulo) {
    const el = document.getElementById('modalImagen');
    if (!el) return;
    document.getElementById('modalImagenSrc').src       = src;
    document.getElementById('modalImagenTitulo').textContent = titulo || 'Imagen del producto';
    new bootstrap.Modal(el).show();
}
</script>
@endpush

@push('styles')
<style>
.product-list-image {
    transition: all 0.2s ease-in-out;
    border: 1px solid #dee2e6;
}

.product-list-image:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    cursor: pointer;
}


.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.table-hover tbody tr:hover .product-list-image {
    border-color: #0d6efd;
}

.badge {
    font-size: 0.85em;
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

.filter-stock-btn.active {
    background-color: #0dcaf0;
    color: #000;
    border-color: #0dcaf0;
}

.filter-stock-btn[data-stock="bajo"].active {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.filter-stock-btn[data-stock="disponible"].active {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

.filter-stock-btn[data-stock="agotado"].active {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

/* Dropdowns de filtros */
.dropdown-menu {
    max-height: 300px;
    overflow-y: auto;
}

.form-check {
    padding-left: 1.5rem;
}



/* Estilos responsivos */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .product-list-image {
        width: 50px !important;
        height: 50px !important;
    }
    
    .btn-group .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
    
    .d-flex.flex-wrap {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .dropdown {
        width: 100%;
    }
    
    .dropdown .btn {
        width: 100%;
        text-align: left;
    }

}

</style>
@endpush