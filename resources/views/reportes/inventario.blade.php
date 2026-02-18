@extends('layouts.app')

@section('title', 'Reporte de Inventario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Inventario</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-boxes me-2"></i>Reporte de Inventario
            </h5>
            <div>
                <button class="btn btn-success" onclick="exportarReporte()" title="Exportar reporte">
                    <i class="fas fa-file-excel me-2"></i> Exportar
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-stock-btn active" data-stock="todos">
                            Todos
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm filter-stock-btn" data-stock="normal">
                            Stock normal
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm filter-stock-btn" data-stock="bajo">
                            Stock bajo
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-stock-btn" data-stock="agotado">
                            Agotado
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" id="searchInput" 
                               placeholder="Buscar producto, SKU, categoría...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch" title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filtros avanzados -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header py-2">
                            <h6 class="mb-0">Filtros avanzados</h6>
                        </div>
                        <div class="card-body py-3">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Categoría</label>
                                    <select class="form-select form-select-sm" id="categoriaFilter">
                                        <option value="">Todas las categorías</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria['id'] }}">{{ $categoria['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Proveedor</label>
                                    <select class="form-select form-select-sm" id="proveedorFilter">
                                        <option value="">Todos los proveedores</option>
                                        @foreach($proveedores as $proveedor)
                                            <option value="{{ $proveedor['id'] }}">{{ $proveedor['nombre'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select form-select-sm" id="estadoFilter">
                                        <option value="todos">Todos</option>
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2 d-flex align-items-end">
                                    <button class="btn btn-sm btn-info me-2" id="btnAplicarFiltros">
                                        <i class="fas fa-filter me-1"></i> Aplicar
                                    </button>
                                    <button class="btn btn-sm btn-secondary" id="btnLimpiarFiltros">
                                        <i class="fas fa-undo me-1"></i> Limpiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards de resumen -->
            <div class="row mb-4" id="resumenCards">
                <div class="col-md-3 mb-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-1">Total Productos</h6>
                            <h3 class="mb-0" id="totalProductos">{{ $data['resumen']['total_productos'] ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card bg-success text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-1">Valor Compra</h6>
                            <h3 class="mb-0" id="valorCompra">Q{{ number_format($data['resumen']['valor_total_inventario'] ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card bg-info text-white">
                        <div class="card-body py-3">
                            <h6 class="mb-1">Valor Venta</h6>
                            <h3 class="mb-0" id="valorVenta">Q{{ number_format($data['resumen']['valor_venta_total'] ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-2">
                    <div class="card bg-warning">
                        <div class="card-body py-3">
                            <h6 class="mb-1">Margen Estimado</h6>
                            @php
                                $compra = $data['resumen']['valor_total_inventario'] ?? 0;
                                $venta = $data['resumen']['valor_venta_total'] ?? 0;
                                $margen = $compra > 0 ? (($venta - $compra) / $compra) * 100 : 0;
                            @endphp
                            <h3 class="mb-0" id="margen">{{ number_format($margen, 2) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas de stock -->
            <div class="row mb-4" id="alertasContainer">
                <div class="col-md-6">
                    <div class="alert alert-danger mb-0" id="bajoStockAlert" style="{{ ($data['resumen']['productos_bajo_stock'] ?? 0) > 0 ? '' : 'display: none;' }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong id="bajoStockCount">{{ $data['resumen']['productos_bajo_stock'] ?? 0 }}</strong> productos con stock bajo
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-secondary mb-0" id="agotadosAlert" style="{{ ($data['resumen']['productos_agotados'] ?? 0) > 0 ? '' : 'display: none;' }}">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong id="agotadosCount">{{ $data['resumen']['productos_agotados'] ?? 0 }}</strong> productos agotados
                    </div>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="productosTable">
                    <thead class="bg-warning">
                        <tr>
                            <th>Producto</th>
                            <th>SKU</th>
                            <th>Categorías</th>
                            <th>Proveedor</th>
                            <th>Stock</th>
                            <th>Stock Mínimo</th>
                            <th>Precio Compra</th>
                            <th>Precio Venta</th>
                            <th>Valor Inventario</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['productos'] ?? [] as $producto)
                        @php
                            $stock = $producto['stock'] ?? 0;
                            $stockMinimo = $producto['stock_minimo'] ?? 1;
                            
                            if ($stock <= 0) {
                                $stockClass = 'secondary';
                                $stockText = 'Agotado';
                            } elseif ($stock <= $stockMinimo) {
                                $stockClass = 'danger';
                                $stockText = 'Bajo';
                            } else {
                                $stockClass = 'success';
                                $stockText = 'Normal';
                            }
                            
                            $valorInventario = ($producto['stock'] ?? 0) * ($producto['precio_compra'] ?? 0);
                            
                            $categoriasTexto = '';
                            if (!empty($producto['categorias'])) {
                                $categoriasTexto = implode(' ', array_column($producto['categorias'], 'nombre'));
                            }
                            
                            $categoriaIds = [];
                            if (!empty($producto['categorias'])) {
                                $categoriaIds = array_column($producto['categorias'], 'id');
                            }
                            
                            $searchText = strtolower(
                                ($producto['nombre'] ?? '') . ' ' .
                                ($producto['sku'] ?? '') . ' ' .
                                $categoriasTexto . ' ' .
                                ($producto['proveedor']['nombre'] ?? '')
                            );
                        @endphp
                        <tr data-producto-id="{{ $producto['id'] ?? '' }}"
                            data-stock="{{ $stock }}"
                            data-stock-minimo="{{ $stockMinimo }}"
                            data-proveedor-id="{{ $producto['proveedor']['id'] ?? '' }}"
                            data-categoria-ids="{{ json_encode($categoriaIds) }}"
                            data-estado="{{ $producto['estado'] ?? 'activo' }}"
                            data-precio-compra="{{ $producto['precio_compra'] ?? 0 }}"
                            data-precio-venta="{{ $producto['precio_venta'] ?? 0 }}"
                            data-valor="{{ $valorInventario }}"
                            data-search="{{ $searchText }}">
                            <td>
                                <strong>{{ $producto['nombre'] ?? 'N/A' }}</strong>
                                <br>
                                <small>{{ $producto['marca'] ?? '' }}</small>
                            </td>
                            <td>{{ $producto['sku'] ?? 'N/A' }}</td>
                            <td>
                                @if(!empty($producto['categorias']))
                                    @foreach($producto['categorias'] as $cat)
                                        <span class="badge bg-secondary">{{ $cat['nombre'] ?? '' }}</span>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $producto['proveedor']['nombre'] ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $stockClass }} stock-badge">{{ $stock }}</span>
                                <small class="d-block stock-text">{{ $stockText }}</small>
                            </td>
                            <td class="stock-minimo">{{ $stockMinimo }}</td>
                            <td class="precio-compra">Q{{ number_format($producto['precio_compra'] ?? 0, 2) }}</td>
                            <td class="precio-venta">Q{{ number_format($producto['precio_venta'] ?? 0, 2) }}</td>
                            <td class="valor-inventario"><strong>Q{{ number_format($valorInventario, 2) }}</strong></td>
                            <td>
                                @if(($producto['estado'] ?? '') == 'activo')
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr id="no-productos-row">
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h5>No hay productos para mostrar</h5>
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
    // Variables globales
    let currentStockFilter = 'todos';
    let categoriaFilter = document.getElementById('categoriaFilter')?.value || '';
    let proveedorFilter = document.getElementById('proveedorFilter')?.value || '';
    let estadoFilter = document.getElementById('estadoFilter')?.value || 'todos';
    let currentSearch = '';
    
    // Referencias a elementos DOM
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const btnAplicarFiltros = document.getElementById('btnAplicarFiltros');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    
    // Eventos para filtros de stock
    document.querySelectorAll('.filter-stock-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-stock-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentStockFilter = this.dataset.stock;
            aplicarFiltros();
        });
    });
    
    // Evento aplicar filtros
    if (btnAplicarFiltros) {
        btnAplicarFiltros.addEventListener('click', function() {
            categoriaFilter = document.getElementById('categoriaFilter')?.value || '';
            proveedorFilter = document.getElementById('proveedorFilter')?.value || '';
            estadoFilter = document.getElementById('estadoFilter')?.value || 'todos';
            aplicarFiltros();
        });
    }
    
    // Evento limpiar filtros
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', limpiarFiltros);
    }
    
    // Búsqueda en tiempo real
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = this.value.toLowerCase().trim();
                aplicarFiltros();
            }, 300);
        });
    }
    
    // Botón limpiar búsqueda
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                currentSearch = '';
                aplicarFiltros();
                searchInput.focus();
            }
        });
    }
    
    // Función principal para aplicar filtros
    function aplicarFiltros() {
        const tbody = document.querySelector('#productosTable tbody');
        if (!tbody) return;
        
        let rows = Array.from(tbody.querySelectorAll('tr'));
        rows = rows.filter(row => !row.id || row.id !== 'no-productos-row');
        
        let totalProductos = 0;
        let valorCompraTotal = 0;
        let valorVentaTotal = 0;
        let productosBajoStock = 0;
        let productosAgotados = 0;
        let visibleRows = [];
        
        rows.forEach(row => {
            const stock = parseInt(row.dataset.stock) || 0;
            const stockMinimo = parseInt(row.dataset.stockMinimo) || 1;
            const proveedorId = row.dataset.proveedorId;
            const estado = row.dataset.estado;
            const categoriaIds = JSON.parse(row.dataset.categoriaIds || '[]');
            const precioVenta = parseFloat(row.dataset.precioVenta) || 0;
            const valor = parseFloat(row.dataset.valor) || 0;
            const searchData = row.dataset.search || '';
            
            // Filtro de stock
            let stockMatch = true;
            if (currentStockFilter !== 'todos') {
                if (currentStockFilter === 'bajo') {
                    stockMatch = stock > 0 && stock <= stockMinimo;
                } else if (currentStockFilter === 'normal') {
                    stockMatch = stock > stockMinimo;
                } else if (currentStockFilter === 'agotado') {
                    stockMatch = stock <= 0;
                }
            }
            
            // Filtro de categoría
            let categoriaMatch = true;
            if (categoriaFilter) {
                categoriaMatch = categoriaIds.includes(parseInt(categoriaFilter));
            }
            
            // Filtro de proveedor
            let proveedorMatch = true;
            if (proveedorFilter) {
                proveedorMatch = proveedorId === proveedorFilter;
            }
            
            // Filtro de estado
            let estadoMatch = true;
            if (estadoFilter && estadoFilter !== 'todos') {
                estadoMatch = estado === estadoFilter;
            }
            
            // Filtro de búsqueda
            const searchMatch = !currentSearch || searchData.includes(currentSearch);
            
            if (stockMatch && categoriaMatch && proveedorMatch && estadoMatch && searchMatch) {
                row.style.display = '';
                visibleRows.push(row);
                totalProductos++;
                valorCompraTotal += valor;
                valorVentaTotal += stock * precioVenta;
                
                if (stock > 0 && stock <= stockMinimo) {
                    productosBajoStock++;
                } else if (stock <= 0) {
                    productosAgotados++;
                }
            } else {
                row.style.display = 'none';
            }
        });
        
        // Reordenar tabla
        const allRows = rows.filter(row => visibleRows.includes(row));
        const hiddenRows = rows.filter(row => !visibleRows.includes(row));
        
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        
        visibleRows.forEach(row => tbody.appendChild(row));
        hiddenRows.forEach(row => tbody.appendChild(row));
        
        // Actualizar resumen - Verificar que los elementos existan
        actualizarResumen(totalProductos, valorCompraTotal, valorVentaTotal, productosBajoStock, productosAgotados);
        
        mostrarMensajeNoResultados(visibleRows.length, rows.length);
    }
    
    // Función para actualizar el resumen
    function actualizarResumen(totalProductos, valorCompraTotal, valorVentaTotal, productosBajoStock, productosAgotados) {
        const totalProductosEl = document.getElementById('totalProductos');
        const valorCompraEl = document.getElementById('valorCompra');
        const valorVentaEl = document.getElementById('valorVenta');
        const margenEl = document.getElementById('margen');
        const bajoStockCountEl = document.getElementById('bajoStockCount');
        const agotadosCountEl = document.getElementById('agotadosCount');
        const bajoStockAlertEl = document.getElementById('bajoStockAlert');
        const agotadosAlertEl = document.getElementById('agotadosAlert');
        
        if (totalProductosEl) totalProductosEl.textContent = totalProductos;
        
        if (valorCompraEl) {
            valorCompraEl.textContent = 'Q' + valorCompraTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
        
        if (valorVentaEl) {
            valorVentaEl.textContent = 'Q' + valorVentaTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
        
        const margen = valorCompraTotal > 0 ? ((valorVentaTotal - valorCompraTotal) / valorCompraTotal) * 100 : 0;
        if (margenEl) margenEl.textContent = margen.toFixed(2) + '%';
        
        // Actualizar alertas
        if (bajoStockCountEl) bajoStockCountEl.textContent = productosBajoStock;
        if (agotadosCountEl) agotadosCountEl.textContent = productosAgotados;
        
        if (bajoStockAlertEl) {
            bajoStockAlertEl.style.display = productosBajoStock > 0 ? '' : 'none';
        }
        
        if (agotadosAlertEl) {
            agotadosAlertEl.style.display = productosAgotados > 0 ? '' : 'none';
        }
    }
    
    // Función para mostrar mensaje cuando no hay resultados
    function mostrarMensajeNoResultados(visibleCount, totalRows) {
        const tbody = document.querySelector('#productosTable tbody');
        if (!tbody) return;
        
        let noResultsRow = document.getElementById('no-results-row');
        
        if (visibleCount === 0 && totalRows > 0) {
            if (!noResultsRow) {
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-row';
                noResultsRow.innerHTML = `
                    <td colspan="10" class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron productos</h5>
                        <p class="text-muted mb-3">Intenta con otros filtros</p>
                        <button class="btn btn-sm btn-primary" onclick="window.limpiarFiltros()">
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
});

// Hacer la función global
window.limpiarFiltros = function() {
    // Resetear filtros de stock
    const stockTodosBtn = document.querySelector('.filter-stock-btn[data-stock="todos"]');
    if (stockTodosBtn) {
        stockTodosBtn.click();
    }
    
    // Resetear selects
    const categoriaFilter = document.getElementById('categoriaFilter');
    const proveedorFilter = document.getElementById('proveedorFilter');
    const estadoFilter = document.getElementById('estadoFilter');
    const searchInput = document.getElementById('searchInput');
    
    if (categoriaFilter) categoriaFilter.value = '';
    if (proveedorFilter) proveedorFilter.value = '';
    if (estadoFilter) estadoFilter.value = 'todos';
    if (searchInput) {
        searchInput.value = '';
        window.currentSearch = '';
    }
    
    // Disparar el click en aplicar filtros
    const btnAplicar = document.getElementById('btnAplicarFiltros');
    if (btnAplicar) {
        btnAplicar.click();
    }
};

function exportarReporte() {
    alert('Funcionalidad de exportación próximamente');
}
</script>
@endpush