@extends('layouts.app')

@section('title', 'Servicios - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Servicios</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-concierge-bell me-2"></i>Gestión de Servicios
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('servicios.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nuevo Servicio
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
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
                    
                    <div class="btn-group ms-2" role="group">
                        <button type="button" class="btn btn-outline-info btn-sm filter-margen-btn" data-margen="todos">
                            Todos
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm filter-margen-btn" data-margen="alto">
                            Margen alto (>100%)
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm filter-margen-btn" data-margen="medio">
                            Margen medio (50-100%)
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm filter-margen-btn" data-margen="bajo">
                            Margen bajo (20-50%)
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm filter-margen-btn" data-margen="minimo">
                            Margen mínimo (<20%)
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Buscar por código, nombre, descripción...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
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
                                <i class="fas fa-chart-line me-1"></i> Rango de precios
                            </button>
                            <div class="dropdown-menu p-3" style="min-width: 280px;">
                                <div class="mb-3">
                                    <label class="form-label">Precio mínimo</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" class="form-control" id="precioMin" min="0" step="0.01" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Precio máximo</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" class="form-control" id="precioMax" min="0" step="0.01" placeholder="9999.99">
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary w-100" id="aplicarRangoPrecio">Aplicar</button>
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
                                <li><a class="dropdown-item sort-option" href="#" data-sort="inversion_asc">Inversión menor a mayor</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="inversion_desc">Inversión mayor a menor</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="margen_asc">Margen menor a mayor</a></li>
                                <li><a class="dropdown-item sort-option" href="#" data-sort="margen_desc">Margen mayor a menor</a></li>
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
                $serviciosData = [];
                $serviciosLinks = [];
                $serviciosMeta = [];
                
                if (isset($servicios['data'])) {
                    $serviciosData = $servicios['data'];
                } elseif (isset($servicios) && is_array($servicios)) {
                    $serviciosData = $servicios;
                }
                
                if (isset($servicios['links']) && is_array($servicios['links'])) {
                    $serviciosLinks = $servicios['links'];
                }
                
                if (isset($servicios['meta']) && is_array($servicios['meta'])) {
                    $serviciosMeta = $servicios['meta'];
                }

                // Recopilar categorías únicas para filtros
                $categoriasUnicas = [];
                foreach ($serviciosData as $servicio) {
                    if (!empty($servicio['categorias'])) {
                        foreach ($servicio['categorias'] as $categoria) {
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
                }
                
                // Ordenar alfabéticamente
                usort($categoriasUnicas, function($a, $b) {
                    return strcmp($a['nombre'], $b['nombre']);
                });
            @endphp

            @if(empty($serviciosData))
                <div class="text-center py-5">
                    <i class="fas fa-concierge-bell fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay servicios registrados</h5>
                    <p class="text-muted">Comienza agregando tu primer servicio</p>
                    <a href="{{ route('servicios.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i> Crear Primer Servicio
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="serviciosTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 70px;">Imagen</th>
                                <th>Código</th>
                                <th>Servicio</th>
                                <th>Inversión</th>
                                <th>Precios</th>
                                <th>Margen</th>
                                <th>Estado</th>
                                <th style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serviciosData as $servicio)
                            @php
                                // Obtener imagen
                                $urlImagen = null;
                                if(isset($servicio['imagenes']) && count($servicio['imagenes']) > 0) {
                                    $imagen = $servicio['imagenes'][0];
                                    if(!empty($imagen['url_thumb'])) {
                                        $urlImagen = $imagen['url_thumb'];
                                    } elseif(!empty($imagen['url_medium'])) {
                                        $urlImagen = $imagen['url_medium'];
                                    } elseif(!empty($imagen['url'])) {
                                        $urlImagen = $imagen['url'];
                                    }
                                }
                                
                                // Calcular margen
                                $precio_final = $servicio['precio_oferta'] ?? $servicio['precio_venta'];
                                $inversion = $servicio['inversion_estimada'] ?? 0;
                                $margen = $inversion > 0 ? (($precio_final - $inversion) / $inversion) * 100 : 0;
                                $margenClass = $margen >= 100 ? 'success' : ($margen >= 50 ? 'info' : ($margen >= 20 ? 'warning' : 'danger'));
                                
                                // Determinar categoría de margen para filtro
                                $margenCategoria = 'minimo';
                                if ($margen >= 100) {
                                    $margenCategoria = 'alto';
                                } elseif ($margen >= 50) {
                                    $margenCategoria = 'medio';
                                } elseif ($margen >= 20) {
                                    $margenCategoria = 'bajo';
                                }
                                
                                // Preparar datos para búsqueda
                                $categoriasTexto = '';
                                if (!empty($servicio['categorias'])) {
                                    $categoriasTexto = implode(' ', array_column($servicio['categorias'], 'nombre'));
                                }
                                $searchText = strtolower(
                                    ($servicio['codigo'] ?? '') . ' ' . 
                                    ($servicio['nombre'] ?? '') . ' ' . 
                                    ($servicio['descripcion'] ?? '') . ' ' . 
                                    $categoriasTexto
                                );
                                
                                // IDs de categorías para filtro
                                $categoriaIds = [];
                                if (!empty($servicio['categorias'])) {
                                    $categoriaIds = array_column($servicio['categorias'], 'id');
                                }
                            @endphp
                            <tr data-estado="{{ $servicio['estado'] ?? 'activo' }}"
                                data-margen="{{ $margenCategoria }}"
                                data-margen-valor="{{ $margen }}"
                                data-precio="{{ $precio_final }}"
                                data-inversion="{{ $inversion }}"
                                data-nombre="{{ strtolower($servicio['nombre'] ?? '') }}"
                                data-categoria-ids="{{ json_encode($categoriaIds) }}"
                                data-search="{{ $searchText }}">
                                <td class="text-center">
                                    @if($urlImagen)
                                        <img src="{{ $urlImagen }}" 
                                             alt="{{ $servicio['nombre'] ?? 'Servicio' }}" 
                                             class="img-thumbnail service-list-image"
                                             style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                             onclick="abrirModalImagen('{{ $urlImagen }}', '{{ $servicio['nombre'] ?? 'Servicio' }}')">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-concierge-bell text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $servicio['codigo'] ?? '' }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $servicio['nombre'] ?? '' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($servicio['descripcion'] ?? 'Sin descripción', 80) }}</small>
                                    @if(!empty($servicio['categorias']) && count($servicio['categorias']) > 0)
                                        <br>
                                        @foreach($servicio['categorias'] as $categoria)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $categoria['nombre'] ?? '' }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        Q{{ number_format($servicio['inversion_estimada'] ?? 0, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small>Venta: 
                                            @if(!empty($servicio['precio_oferta']))
                                                <span class="text-decoration-line-through text-muted">Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}</span>
                                                <strong class="text-danger">Q{{ number_format($servicio['precio_oferta'], 2) }}</strong>
                                            @else
                                                <strong>Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}</strong>
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $margenClass }}" title="Margen de ganancia">
                                        <i class="fas fa-chart-line me-1"></i>
                                        {{ number_format($margen, 1) }}%
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        Ganancia: Q{{ number_format($precio_final - $inversion, 2) }}
                                    </small>
                                </td>
                                <td>
                                    <form action="{{ route('servicios.change-status', $servicio['id'] ?? '#') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm btn-{{ ($servicio['estado'] ?? '') == 'activo' ? 'success' : 'secondary' }}"
                                                onclick="return confirm('¿Estás seguro de cambiar el estado de este servicio?')">
                                            <i class="fas fa-{{ ($servicio['estado'] ?? '') == 'activo' ? 'check-circle' : 'times-circle' }} me-1"></i>
                                            {{ $servicio['estado'] ?? 'desconocido' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('servicios.show', $servicio['id'] ?? '#') }}" class="btn btn-sm btn-info" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('servicios.edit', $servicio['id'] ?? '#') }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('servicios.destroy', $servicio['id'] ?? '#') }}" method="POST" 
                                              class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este servicio? Esta acción no se puede deshacer.')">
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
                @if(!empty($serviciosLinks) && count($serviciosLinks) > 0)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        @if(!empty($serviciosMeta))
                            Mostrando 
                            {{ $serviciosMeta['from'] ?? 1 }} - 
                            {{ $serviciosMeta['to'] ?? count($serviciosData) }} de 
                            {{ $serviciosMeta['total'] ?? count($serviciosData) }} servicios
                        @else
                            Mostrando {{ count($serviciosData) }} servicios
                        @endif
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            @foreach($serviciosLinks as $link)
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
                <h5 class="modal-title" id="modalImagenTitulo">Imagen del servicio</h5>
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
const categorias = @json(array_values($categoriasUnicas));

document.addEventListener('DOMContentLoaded', function() {
    let currentEstadoFilter = 'todos';
    let currentMargenFilter = 'todos';
    let currentCategoriaFilter = null;
    let currentSort = 'nombre_asc';
    let currentSearch = '';
    let precioMin = null;
    let precioMax = null;
    
    // Inicializar dropdown de categorías
    inicializarFiltrosCategoria();
    
    function inicializarFiltrosCategoria() {
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
        
        // Eventos para filtros de categoría
        document.querySelectorAll('.filter-categoria').forEach(input => {
            input.addEventListener('change', function() {
                currentCategoriaFilter = this.value || null;
                aplicarFiltros();
            });
        });
        
        // Filtro de texto en dropdown
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
    }
    
    // Función para aplicar todos los filtros
    function aplicarFiltros() {
        const searchText = document.getElementById('searchInput').value.toLowerCase().trim();
        currentSearch = searchText;
        
        const tbody = document.querySelector('#serviciosTable tbody');
        let rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Excluir fila de no resultados si existe
        rows = rows.filter(row => row.id !== 'no-results-row');
        
        let visibleRows = [];
        
        rows.forEach(row => {
            const estado = row.dataset.estado;
            const margen = row.dataset.margen;
            const precio = parseFloat(row.dataset.precio) || 0;
            const categoriaIds = JSON.parse(row.dataset.categoriaIds || '[]');
            const searchData = row.dataset.search || '';
            
            // Filtro de estado
            const estadoMatch = currentEstadoFilter === 'todos' || estado === currentEstadoFilter;
            
            // Filtro de margen
            const margenMatch = currentMargenFilter === 'todos' || margen === currentMargenFilter;
            
            // Filtro de categoría
            let categoriaMatch = true;
            if (currentCategoriaFilter) {
                categoriaMatch = categoriaIds.includes(parseInt(currentCategoriaFilter));
            }
            
            // Filtro de rango de precio
            let precioMatch = true;
            if (precioMin !== null) {
                precioMatch = precio >= precioMin;
            }
            if (precioMatch && precioMax !== null) {
                precioMatch = precio <= precioMax;
            }
            
            // Filtro de búsqueda
            const searchMatch = searchText === '' || searchData.includes(searchText);
            
            // Mostrar u ocultar fila
            if (estadoMatch && margenMatch && categoriaMatch && precioMatch && searchMatch) {
                row.style.display = '';
                visibleRows.push(row);
            } else {
                row.style.display = 'none';
            }
        });
        
        // Aplicar ordenamiento
        visibleRows = ordenarFilas(visibleRows, currentSort);
        
        // Reordenar la tabla
        const allRows = rows.filter(row => visibleRows.includes(row));
        const hiddenRows = rows.filter(row => !visibleRows.includes(row));
        
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        
        visibleRows.forEach(row => tbody.appendChild(row));
        hiddenRows.forEach(row => tbody.appendChild(row));
        
        // Mostrar mensaje si no hay resultados
        mostrarMensajeNoResultados(visibleRows.length, rows.length);
    }
    
    // Función para ordenar filas
    function ordenarFilas(rows, sortType) {
        return rows.sort((a, b) => {
            switch(sortType) {
                case 'nombre_asc':
                    return (a.dataset.nombre || '').localeCompare(b.dataset.nombre || '');
                case 'nombre_desc':
                    return (b.dataset.nombre || '').localeCompare(a.dataset.nombre || '');
                case 'precio_asc':
                    return (parseFloat(a.dataset.precio) || 0) - (parseFloat(b.dataset.precio) || 0);
                case 'precio_desc':
                    return (parseFloat(b.dataset.precio) || 0) - (parseFloat(a.dataset.precio) || 0);
                case 'inversion_asc':
                    return (parseFloat(a.dataset.inversion) || 0) - (parseFloat(b.dataset.inversion) || 0);
                case 'inversion_desc':
                    return (parseFloat(b.dataset.inversion) || 0) - (parseFloat(a.dataset.inversion) || 0);
                case 'margen_asc':
                    return (parseFloat(a.dataset.margenValor) || 0) - (parseFloat(b.dataset.margenValor) || 0);
                case 'margen_desc':
                    return (parseFloat(b.dataset.margenValor) || 0) - (parseFloat(a.dataset.margenValor) || 0);
                default:
                    return 0;
            }
        });
    }
    
    // Función para mostrar mensaje cuando no hay resultados
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const table = document.getElementById('serviciosTable');
        const tbody = table.querySelector('tbody');
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="8" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron servicios</h5>
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
    
    // Función para limpiar filtros
    window.limpiarFiltros = function() {
        currentEstadoFilter = 'todos';
        currentMargenFilter = 'todos';
        currentCategoriaFilter = null;
        currentSort = 'nombre_asc';
        precioMin = null;
        precioMax = null;
        
        // Actualizar botones de estado
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.filter === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Actualizar botones de margen
        document.querySelectorAll('.filter-margen-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.margen === 'todos') {
                btn.classList.add('active');
            }
        });
        
        // Resetear radio buttons de categoría
        document.getElementById('categoriaTodos')?.click();
        
        // Limpiar inputs de precio
        document.getElementById('precioMin').value = '';
        document.getElementById('precioMax').value = '';
        
        // Limpiar búsqueda
        document.getElementById('searchInput').value = '';
        
        aplicarFiltros();
    };
    
    // Eventos para filtros de estado
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentEstadoFilter = this.dataset.filter;
            aplicarFiltros();
        });
    });
    
    // Eventos para filtros de margen
    document.querySelectorAll('.filter-margen-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-margen-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentMargenFilter = this.dataset.margen;
            aplicarFiltros();
        });
    });
    
    // Evento para aplicar rango de precio
    document.getElementById('aplicarRangoPrecio').addEventListener('click', function() {
        precioMin = document.getElementById('precioMin').value ? parseFloat(document.getElementById('precioMin').value) : null;
        precioMax = document.getElementById('precioMax').value ? parseFloat(document.getElementById('precioMax').value) : null;
        aplicarFiltros();
    });
    
    // Eventos para ordenamiento
    document.querySelectorAll('.sort-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            currentSort = this.dataset.sort;
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
    document.querySelector('.filter-btn[data-filter="todos"]').classList.add('active');
    document.querySelector('.filter-margen-btn[data-margen="todos"]').classList.add('active');
    
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
        modalTitle.textContent = titulo || 'Imagen del servicio';
        
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}
</script>
@endpush

@push('styles')
<style>
.service-list-image {
    transition: all 0.2s ease-in-out;
    border: 1px solid #dee2e6;
}

.service-list-image:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    cursor: pointer;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.table-hover tbody tr:hover .service-list-image {
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

.filter-margen-btn.active {
    background-color: #0dcaf0;
    color: #000;
    border-color: #0dcaf0;
}

.filter-margen-btn[data-margen="alto"].active {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

.filter-margen-btn[data-margen="medio"].active {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.filter-margen-btn[data-margen="bajo"].active {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.filter-margen-btn[data-margen="minimo"].active {
    background-color: #dc3545;
    border-color: #dc3545;
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
    
    .service-list-image {
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