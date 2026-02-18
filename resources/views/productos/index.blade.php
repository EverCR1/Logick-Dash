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
            <div class="d-flex gap-2">
                <a href="{{ route('productos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nuevo Producto
                </a>
            </div>
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
                        <button type="button" class="btn btn-outline-info btn-sm filter-stock-btn" data-stock="todos">
                            Todo stock
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm filter-stock-btn" data-stock="bajo">
                            Stock bajo
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm filter-stock-btn" data-stock="disponible">
                            Con stock
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-stock-btn" data-stock="agotado">
                            Agotado
                        </button>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Buscar por nombre, SKU, marca, color, descripción o ubicación...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <i class="fas fa-info-circle"></i> Búsqueda en nombre, SKU, marca, color, descripción y ubicación
                    </small>
                </div>
            </div>

            <!-- Filtros adicionales -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-tag me-1"></i> Categorías
                            </button>
                            <div class="dropdown-menu p-3" style="min-width: 250px; max-height: 300px; overflow-y: auto;">
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" id="filterCategoriaText" placeholder="Buscar categoría...">
                                </div>
                                <div id="categoriaList">
                                    <!-- Se llenará dinámicamente con JavaScript -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-building me-1"></i> Proveedores
                            </button>
                            <div class="dropdown-menu p-3" style="min-width: 250px; max-height: 300px; overflow-y: auto;">
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" id="filterProveedorText" placeholder="Buscar proveedor...">
                                </div>
                                <div id="proveedorList">
                                    <!-- Se llenará dinámicamente con JavaScript -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-sort me-1"></i> Ordenar por
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item sort-option" href="#" data-sort="nombre_asc">Nombre A-Z</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="nombre_desc">Nombre Z-A</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="precio_asc">Precio menor a mayor</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="precio_desc">Precio mayor a menor</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="stock_asc">Stock menor a mayor</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="stock_desc">Stock mayor a menor</a></li>
                            </ul>
                        </div>
                        
                        <button class="btn btn-sm btn-info" id="btnLimpiarFiltros" title="Limpiar todos los filtros">
                            <i class="fas fa-undo me-1"></i> Limpiar filtros
                        </button>
                    </div>
                </div>
            </div>

            @php
                // Extraer datos de manera segura
                $productosData = [];
                $productosLinks = [];
                $productosMeta = [];
                
                if (isset($productos['data'])) {
                    $productosData = $productos['data'];
                } elseif (isset($productos) && is_array($productos)) {
                    $productosData = $productos;
                }
                
                if (isset($productos['links']) && is_array($productos['links'])) {
                    $productosLinks = $productos['links'];
                }
                
                if (isset($productos['meta']) && is_array($productos['meta'])) {
                    $productosMeta = $productos['meta'];
                }

                // Recopilar categorías y proveedores únicos para filtros
                $categoriasUnicas = [];
                $proveedoresUnicos = [];
                foreach ($productosData as $producto) {
                    if (!empty($producto['categorias'])) {
                        foreach ($producto['categorias'] as $categoria) {
                            $categoriaId = $categoria['id'] ?? '';
                            $categoriaNombre = $categoria['nombre'] ?? '';
                            if ($categoriaId && $categoriaNombre) {
                                $categoriasUnicas[$categoriaId] = [
                                    'id' => $categoriaId,
                                    'nombre' => $categoriaNombre
                                ];
                            }
                        }
                    }
                    
                    if (!empty($producto['proveedor']['id']) && !empty($producto['proveedor']['nombre'])) {
                        $proveedoresUnicos[$producto['proveedor']['id']] = [
                            'id' => $producto['proveedor']['id'],
                            'nombre' => $producto['proveedor']['nombre']
                        ];
                    }
                }
                
                // Ordenar alfabéticamente
                usort($categoriasUnicas, function($a, $b) {
                    return strcmp($a['nombre'], $b['nombre']);
                });
                usort($proveedoresUnicos, function($a, $b) {
                    return strcmp($a['nombre'], $b['nombre']);
                });
            @endphp

            @if(empty($productosData))
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay productos registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer producto</p>
                    <a href="{{ route('productos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Producto
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="productosTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 70px;">Imagen</th>
                                <th>SKU</th>
                                <th>Producto</th>
                                <th>Proveedor</th>
                                <th>Precios</th>
                                <th>Stock</th>
                                <th>Estado</th>
                                <th style="width: 120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosData as $producto)
                            @php
                                // Función para obtener la imagen principal
                                $imagenPrincipal = null;
                                $urlImagen = null;
                                
                                if(isset($producto['imagenes']) && count($producto['imagenes']) > 0) {
                                    foreach($producto['imagenes'] as $imagen) {
                                        if(isset($imagen['es_principal']) && $imagen['es_principal']) {
                                            $imagenPrincipal = $imagen;
                                            break;
                                        }
                                    }
                                    
                                    if(!$imagenPrincipal) {
                                        $imagenPrincipal = $producto['imagenes'][0];
                                    }
                                    
                                    if(!empty($imagenPrincipal['url_thumb'])) {
                                        $urlImagen = $imagenPrincipal['url_thumb'];
                                    } elseif(!empty($imagenPrincipal['url_medium'])) {
                                        $urlImagen = $imagenPrincipal['url_medium'];
                                    } elseif(!empty($imagenPrincipal['url'])) {
                                        $urlImagen = $imagenPrincipal['url'];
                                    }
                                }
                                
                                $stock = $producto['stock'] ?? 0;
                                $stockMinimo = $producto['stock_minimo'] ?? 1;
                                $stockClass = $stock <= 0 ? 'secondary' : ($stock <= $stockMinimo ? 'danger' : ($stock <= $stockMinimo * 2 ? 'warning' : 'success'));
                                $stockEstado = $stock <= 0 ? 'Agotado' : ($stock <= $stockMinimo ? 'Bajo' : 'Normal');
                                
                                // Preparar datos para búsqueda
                                $proveedorNombre = $producto['proveedor']['nombre'] ?? '';
                                
                                // IDs de categorías para filtro
                                $categoriaIds = [];
                                if (!empty($producto['categorias'])) {
                                    $categoriaIds = array_column($producto['categorias'], 'id');
                                }
                            @endphp
                            <tr data-estado="{{ $producto['estado'] ?? 'activo' }}"
                                data-stock="{{ $stock }}"
                                data-stock-minimo="{{ $stockMinimo }}"
                                data-precio="{{ $producto['precio_venta'] ?? 0 }}"
                                data-nombre="{{ strtolower($producto['nombre'] ?? '') }}"
                                data-sku="{{ strtolower($producto['sku'] ?? '') }}"
                                data-marca="{{ strtolower($producto['marca'] ?? '') }}"
                                data-color="{{ strtolower($producto['color'] ?? '') }}"
                                data-descripcion="{{ strtolower($producto['descripcion'] ?? '') }}"
                                data-ubicacion="{{ strtolower($producto['ubicacion'] ?? '') }}"
                                data-proveedor-id="{{ $producto['proveedor']['id'] ?? '' }}"
                                data-proveedor-nombre="{{ strtolower($proveedorNombre) }}"
                                data-categoria-ids="{{ json_encode($categoriaIds) }}">
                                <td class="text-center">
                                    @if($urlImagen)
                                        <img src="{{ $urlImagen }}" 
                                             alt="{{ $producto['nombre'] ?? 'Producto' }}" 
                                             class="img-thumbnail product-list-image"
                                             style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                             onclick="abrirModalImagen('{{ $urlImagen }}', '{{ $producto['nombre'] ?? 'Producto' }}')">
                                        @if(isset($imagenPrincipal['es_principal']) && $imagenPrincipal['es_principal'])
                                            <small class="d-block text-success mt-1">
                                                <i class="fas fa-star fa-xs"></i>
                                            </small>
                                        @endif
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-box-open text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $producto['sku'] ?? '' }}</strong>
                                    @if(!empty($producto['codigo_barras']))
                                    <br>
                                    <small class="text-muted">{{ $producto['codigo_barras'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $producto['nombre'] ?? '' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $producto['marca'] ?? 'Sin marca' }} | {{ $producto['color'] ?? 'Sin color' }}</small>
                                    <br>
                                    @if(!empty($producto['categorias']) && count($producto['categorias']) > 0)
                                        @foreach($producto['categorias'] as $categoria)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $categoria['nombre'] ?? '' }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    {{ $proveedorNombre ?: 'N/A' }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small>Compra: <strong>Q{{ number_format($producto['precio_compra'] ?? 0, 2) }}</strong></small>
                                        <small>Venta: 
                                            @if(!empty($producto['precio_oferta']))
                                                <span class="text-decoration-line-through text-muted">Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</span>
                                                <strong class="text-danger">Q{{ number_format($producto['precio_oferta'], 2) }}</strong>
                                            @else
                                                <strong>Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</strong>
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $stockClass }}">
                                        {{ $stock }} unidades
                                    </span>
                                    <small class="d-block text-muted">{{ $stockEstado }} (Mín: {{ $stockMinimo }})</small>
                                </td>
                                <td>
                                    <span class="badge {{ ($producto['estado'] ?? '') == 'activo' ? 'bg-success' : 'bg-danger' }}">
                                        <i class="fas fa-{{ ($producto['estado'] ?? '') == 'activo' ? 'check-circle' : 'times-circle' }} me-1"></i>
                                        {{ $producto['estado'] ?? 'desconocido' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('productos.show', $producto['id'] ?? '#') }}" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('productos.edit', $producto['id'] ?? '#') }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('productos.destroy', $producto['id'] ?? '#') }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
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

                <!-- Paginación -->
                @if(!empty($productosLinks) && count($productosLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($productosMeta))
                            Mostrando 
                            {{ $productosMeta['from'] ?? 1 }} - 
                            {{ $productosMeta['to'] ?? count($productosData) }} de 
                            {{ $productosMeta['total'] ?? count($productosData) }} productos
                        @else
                            Mostrando {{ count($productosData) }} productos
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($productosLinks as $link)
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

<!-- Modal para ver imagen en grande -->
<div class="modal fade" id="modalImagen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagenTitulo">Imagen del producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="" id="modalImagenSrc" class="img-fluid" alt="" style="max-height: 70vh; width: auto;">
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
// Datos para filtros
const categorias = @json($categoriasParaFiltros);
const proveedores = @json($proveedoresParaFiltros);

document.addEventListener('DOMContentLoaded', function() {
    let currentEstadoFilter = 'todos';
    let currentStockFilter = 'todos';
    let currentCategoriaFilter = null;
    let currentProveedorFilter = null;
    let currentSort = 'nombre_asc';
    let currentSearch = '';
    
    // Inicializar dropdowns de filtros
    inicializarFiltrosDropdown();
    
    // Función para inicializar filtros de dropdown
    function inicializarFiltrosDropdown() {
        // Llenar lista de categorías
        const categoriaList = document.getElementById('categoriaList');
        if (categoriaList) {
            let categoriasHtml = '<div class="form-check mb-2">';
            categoriasHtml += '<input class="form-check-input filter-categoria" type="radio" name="categoriaFilter" id="categoriaTodos" value="" checked>';
            categoriasHtml += '<label class="form-check-label" for="categoriaTodos">Todas las categorías</label>';
            categoriasHtml += '</div>';
            
            categorias.forEach(cat => {
                categoriasHtml += '<div class="form-check mb-2">';
                categoriasHtml += `<input class="form-check-input filter-categoria" type="radio" name="categoriaFilter" id="categoria_${cat.id}" value="${cat.id}">`;
                categoriasHtml += `<label class="form-check-label" for="categoria_${cat.id}">${cat.nombre}</label>`;
                categoriasHtml += '</div>';
            });
            
            categoriaList.innerHTML = categoriasHtml;
        }
        
        // Llenar lista de proveedores
        const proveedorList = document.getElementById('proveedorList');
        if (proveedorList) {
            let proveedoresHtml = '<div class="form-check mb-2">';
            proveedoresHtml += '<input class="form-check-input filter-proveedor" type="radio" name="proveedorFilter" id="proveedorTodos" value="" checked>';
            proveedoresHtml += '<label class="form-check-label" for="proveedorTodos">Todos los proveedores</label>';
            proveedoresHtml += '</div>';
            
            proveedores.forEach(prov => {
                proveedoresHtml += '<div class="form-check mb-2">';
                proveedoresHtml += `<input class="form-check-input filter-proveedor" type="radio" name="proveedorFilter" id="proveedor_${prov.id}" value="${prov.id}">`;
                proveedoresHtml += `<label class="form-check-label" for="proveedor_${prov.id}">${prov.nombre}</label>`;
                proveedoresHtml += '</div>';
            });
            
            proveedorList.innerHTML = proveedoresHtml;
        }
        
        // Eventos para filtros de categoría
        document.querySelectorAll('.filter-categoria').forEach(input => {
            input.addEventListener('change', function() {
                currentCategoriaFilter = this.value || null;
                aplicarFiltros();
            });
        });
        
        // Eventos para filtros de proveedor
        document.querySelectorAll('.filter-proveedor').forEach(input => {
            input.addEventListener('change', function() {
                currentProveedorFilter = this.value || null;
                aplicarFiltros();
            });
        });
        
        // Filtro de texto en dropdowns
        document.getElementById('filterCategoriaText')?.addEventListener('keyup', function() {
            const text = this.value.toLowerCase();
            document.querySelectorAll('#categoriaList .form-check').forEach(item => {
                const label = item.querySelector('label');
                if (label) {
                    const matches = label.textContent.toLowerCase().includes(text);
                    item.style.display = matches ? '' : 'none';
                }
            });
        });
        
        document.getElementById('filterProveedorText')?.addEventListener('keyup', function() {
            const text = this.value.toLowerCase();
            document.querySelectorAll('#proveedorList .form-check').forEach(item => {
                const label = item.querySelector('label');
                if (label) {
                    const matches = label.textContent.toLowerCase().includes(text);
                    item.style.display = matches ? '' : 'none';
                }
            });
        });
    }
    
    // Función para aplicar todos los filtros
    function aplicarFiltros() {
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
        currentSearch = searchText;
        
        const tbody = document.querySelector('#productosTable tbody');
        if (!tbody) return;
        
        let rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Excluir fila de no resultados si existe
        rows = rows.filter(row => row.id !== 'no-results-row');
        
        // Primero aplicar filtros para obtener las filas visibles
        let visibleRows = [];
        
        rows.forEach(row => {
            // Obtener datos específicos para búsqueda
            const nombre = row.getAttribute('data-nombre') || '';
            const sku = row.getAttribute('data-sku') || '';
            const marca = row.getAttribute('data-marca') || '';
            const color = row.getAttribute('data-color') || '';
            const descripcion = row.getAttribute('data-descripcion') || '';
            const ubicacion = row.getAttribute('data-ubicacion') || '';
            const estado = row.getAttribute('data-estado');
            const stock = parseInt(row.getAttribute('data-stock')) || 0;
            const stockMinimo = parseInt(row.getAttribute('data-stock-minimo')) || 1;
            const proveedorId = row.getAttribute('data-proveedor-id');
            
            // Parsear categorías IDs
            let categoriaIds = [];
            try {
                categoriaIds = JSON.parse(row.getAttribute('data-categoria-ids') || '[]');
            } catch (e) {
                categoriaIds = [];
            }
            
            // Filtro de búsqueda en múltiples campos
            let searchMatch = true;
            if (searchText !== '') {
                searchMatch = nombre.includes(searchText) || 
                            sku.includes(searchText) || 
                            marca.includes(searchText) || 
                            color.includes(searchText) || 
                            descripcion.includes(searchText) || 
                            ubicacion.includes(searchText);
            }
            
            // Filtro de estado
            const estadoMatch = currentEstadoFilter === 'todos' || estado === currentEstadoFilter;
            
            // Filtro de stock
            let stockMatch = true;
            if (currentStockFilter !== 'todos') {
                if (currentStockFilter === 'bajo') {
                    stockMatch = stock > 0 && stock <= stockMinimo;
                } else if (currentStockFilter === 'disponible') {
                    stockMatch = stock > 0;
                } else if (currentStockFilter === 'agotado') {
                    stockMatch = stock <= 0;
                }
            }
            
            // Filtro de categoría
            let categoriaMatch = true;
            if (currentCategoriaFilter) {
                categoriaMatch = categoriaIds.includes(parseInt(currentCategoriaFilter));
            }
            
            // Filtro de proveedor
            let proveedorMatch = true;
            if (currentProveedorFilter) {
                proveedorMatch = proveedorId === currentProveedorFilter;
            }
            
            // Guardar estado de visibilidad
            if (estadoMatch && stockMatch && categoriaMatch && proveedorMatch && searchMatch) {
                row.style.display = '';
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });
        
        // Ahora ordenar SOLO las filas visibles
        if (currentSort !== 'ninguno') {
            visibleRows = ordenarFilas(visibleRows, currentSort);
        }
        
        // Reconstruir el tbody con las filas ordenadas (solo las visibles)
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        
        // Agregar primero las filas visibles ordenadas
        visibleRows.forEach(row => tbody.appendChild(row));
        
        // Luego agregar las filas ocultas (opcional, pero las mantenemos en el DOM)
        rows.forEach(row => {
            if (row.style.display === 'none') {
                tbody.appendChild(row);
            }
        });
        
        // Mostrar mensaje si no hay resultados
        mostrarMensajeNoResultados(visibleRows.length, rows.length);
    }

    // Función para ordenar filas (CORREGIDA)
    function ordenarFilas(rows, sortType) {
        return rows.sort((a, b) => {
            // Obtener valores usando getAttribute para asegurar
            const nombreA = (a.getAttribute('data-nombre') || '').toLowerCase();
            const nombreB = (b.getAttribute('data-nombre') || '').toLowerCase();
            
            // Precios - asegurar que sean números
            const precioA = parseFloat(a.getAttribute('data-precio')) || 0;
            const precioB = parseFloat(b.getAttribute('data-precio')) || 0;
            
            // Stock - asegurar que sean números enteros
            const stockA = parseInt(a.getAttribute('data-stock')) || 0;
            const stockB = parseInt(b.getAttribute('data-stock')) || 0;
            
            switch(sortType) {
                case 'nombre_asc':
                    return nombreA.localeCompare(nombreB);
                case 'nombre_desc':
                    return nombreB.localeCompare(nombreA);
                case 'precio_asc':
                    return precioA - precioB;
                case 'precio_desc':
                    return precioB - precioA;
                case 'stock_asc':
                    return stockA - stockB;
                case 'stock_desc':
                    return stockB - stockA;
                default:
                    return 0;
            }
        });
    }

    // Función para limpiar filtros (actualizada)
    window.limpiarFiltros = function() {
        currentEstadoFilter = 'todos';
        currentStockFilter = 'todos';
        currentCategoriaFilter = null;
        currentProveedorFilter = null;
        currentSort = 'nombre_asc'; // Valor por defecto
        
        // Actualizar botones de estado
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Actualizar botones de stock
        document.querySelectorAll('.filter-stock-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.stock === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Resetear radio buttons
        const categoriaTodos = document.getElementById('categoriaTodos');
        if (categoriaTodos) categoriaTodos.click();
        
        const proveedorTodos = document.getElementById('proveedorTodos');
        if (proveedorTodos) proveedorTodos.click();
        
        // Limpiar búsqueda
        document.getElementById('searchInput').value = '';
        
        // Resetear dropdown de ordenamiento (opcional)
        document.querySelectorAll('.sort-option').forEach(opt => {
            opt.classList.remove('active');
        });
        
        aplicarFiltros();
    };

    // Eventos para ordenamiento (mejorado)
    document.querySelectorAll('.sort-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remover clase active de todas las opciones
            document.querySelectorAll('.sort-option').forEach(opt => {
                opt.classList.remove('active');
            });
            
            // Agregar clase active a la opción seleccionada
            this.classList.add('active');
            
            // Actualizar el texto del botón dropdown para mostrar la selección actual
            const sortButton = document.querySelector('.dropdown-toggle[data-bs-toggle="dropdown"]');
            if (sortButton) {
                const sortText = this.textContent.trim();
                sortButton.innerHTML = `<i class="fas fa-sort me-1"></i> ${sortText}`;
            }
            
            currentSort = this.dataset.sort;
            aplicarFiltros();
        });
    });
    
    // Función para mostrar mensaje cuando no hay resultados
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const table = document.getElementById('productosTable');
        const tbody = table.querySelector('tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron productos</h5>
                        <p class="text-muted mb-3">Intenta con otros términos de búsqueda o filtros</p>
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
    
    
    // Eventos para filtros de estado
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentEstadoFilter = this.dataset.filter;
            aplicarFiltros();
        });
    });
    
    // Eventos para filtros de stock
    document.querySelectorAll('.filter-stock-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-stock-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentStockFilter = this.dataset.stock;
            aplicarFiltros();
        });
    });
    
    
    // Búsqueda en tiempo real
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(aplicarFiltros, 300);
    });
    
    // Botón limpiar búsqueda
    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        aplicarFiltros();
        document.getElementById('searchInput').focus();
    });
    
    // Botón limpiar todos los filtros
    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);
    
    // Inicializar botones activos
    const estadoBtn = document.querySelector('.filter-btn[data-filter="todos"]');
    if (estadoBtn) estadoBtn.classList.add('active');
    
    const stockBtn = document.querySelector('.filter-stock-btn[data-stock="todos"]');
    if (stockBtn) stockBtn.classList.add('active');
    
    // Agregar tooltips a los botones
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Función para abrir imagen en modal
function abrirModalImagen(src, titulo) {
    const modalElement = document.getElementById('modalImagen');
    const modalSrc = document.getElementById('modalImagenSrc');
    const modalTitle = document.getElementById('modalImagenTitulo');
    
    if (modalElement && modalSrc && modalTitle) {
        modalSrc.src = src;
        modalTitle.textContent = titulo || 'Imagen del producto';
        
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
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