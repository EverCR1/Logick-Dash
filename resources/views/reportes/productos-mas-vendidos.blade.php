@extends('layouts.app')

@section('title', 'Productos Más Vendidos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reportes.index') }}">Reportes</a></li>
    <li class="breadcrumb-item active">Productos Más Vendidos</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-chart-bar me-2"></i>Productos Más Vendidos
            </h5>
            <div>
                <button class="btn btn-success" onclick="exportarReporte()" title="Exportar reporte">
                    <i class="fas fa-file-excel me-2"></i> Exportar
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filtros en tiempo real -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-periodo-btn active" data-periodo="hoy">
                            Hoy
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-periodo-btn" data-periodo="semana">
                            Esta semana
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-periodo-btn" data-periodo="mes">
                            Este mes
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm filter-periodo-btn" data-periodo="personalizado">
                            Personalizado
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
                                    <label class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control form-control-sm" id="fechaInicio" value="{{ $fechaInicio }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control form-control-sm" id="fechaFin" value="{{ $fechaFin }}">
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Límite</label>
                                    <select class="form-select form-select-sm" id="limiteFilter">
                                        <option value="10">10 productos</option>
                                        <option value="20" selected>20 productos</option>
                                        <option value="50">50 productos</option>
                                        <option value="100">100 productos</option>
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

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="productosTable">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th>SKU</th>
                            <th>Categoría</th>
                            <th>Veces Vendido</th>
                            <th>Unidades Vendidas</th>
                            <th>Total Vendido</th>
                            <th>Stock Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['productos'] ?? [] as $index => $item)
                        @php
                            $producto = $item['producto'] ?? [];
                            $categoriasTexto = '';
                            if (!empty($producto['categorias'])) {
                                $categoriasTexto = implode(' ', array_column($producto['categorias'], 'nombre'));
                            }
                            $searchText = strtolower(
                                ($producto['nombre'] ?? '') . ' ' .
                                ($producto['sku'] ?? '') . ' ' .
                                $categoriasTexto
                            );
                        @endphp
                        <tr data-index="{{ $index }}"
                            data-producto-id="{{ $producto['id'] ?? '' }}"
                            data-producto-nombre="{{ strtolower($producto['nombre'] ?? '') }}"
                            data-sku="{{ strtolower($producto['sku'] ?? '') }}"
                            data-categoria="{{ strtolower($categoriasTexto) }}"
                            data-veces-vendido="{{ $item['veces_vendido'] ?? 0 }}"
                            data-unidades="{{ $item['total_unidades'] ?? 0 }}"
                            data-total="{{ $item['total_vendido'] ?? 0 }}"
                            data-search="{{ $searchText }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $producto['nombre'] ?? 'N/A' }}</strong>
                                <br>
                                <small class="text-muted">{{ $producto['marca'] ?? '' }}</small>
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
                            <td>
                                <span class="badge bg-info">{{ $item['veces_vendido'] ?? 0 }}</span>
                            </td>
                            <td>{{ $item['total_unidades'] ?? 0 }}</td>
                            <td><strong>Q{{ number_format($item['total_vendido'] ?? 0, 2) }}</strong></td>
                            <td>
                                @php
                                    $stock = $producto['stock'] ?? 0;
                                    $stockMinimo = $producto['stock_minimo'] ?? 1;
                                    $stockClass = $stock <= 0 ? 'secondary' : ($stock <= $stockMinimo ? 'danger' : 'success');
                                @endphp
                                <span class="badge bg-{{ $stockClass }}">{{ $stock }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-productos-row">
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <h5>No hay datos de ventas en el período seleccionado</h5>
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
    let currentPeriodo = 'mes';
    let fechaInicio = document.getElementById('fechaInicio').value;
    let fechaFin = document.getElementById('fechaFin').value;
    let limiteFilter = '20';
    let currentSearch = '';
    let isLoading = false;

    function getFechasPorPeriodo(periodo) {
        const hoy = new Date();
        const pad = d => d.toISOString().split('T')[0];
        switch(periodo) {
            case 'hoy':
                return { inicio: pad(hoy), fin: pad(hoy) };
            case 'semana':
                const inicioSemana = new Date(hoy);
                inicioSemana.setDate(hoy.getDate() - hoy.getDay() + (hoy.getDay() === 0 ? -6 : 1));
                return { inicio: pad(inicioSemana), fin: pad(hoy) };
            case 'mes':
                return { inicio: pad(new Date(hoy.getFullYear(), hoy.getMonth(), 1)), fin: pad(hoy) };
            default:
                return {
                    inicio: document.getElementById('fechaInicio').value,
                    fin: document.getElementById('fechaFin').value
                };
        }
    }

    function cargarProductos() {
        if (isLoading) return;
        isLoading = true;

        const tbody = document.querySelector('#productosTable tbody');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2 text-muted">Cargando productos...</p>
                </td>
            </tr>`;

        const params = new URLSearchParams({
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            limite: limiteFilter
        });

        fetch(`{{ route('reportes.productos-mas-vendidos') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(response => {
            const productos = response.productos ?? [];
            renderTabla(productos);
        })
        .catch(err => {
            console.error('Error:', err);
            tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">Error al cargar datos</td></tr>`;
        })
        .finally(() => { isLoading = false; });
    }

    function renderTabla(productos) {
        const tbody = document.querySelector('#productosTable tbody');

        if (!productos || productos.length === 0) {
            tbody.innerHTML = `
                <tr id="no-productos-row">
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3 d-block"></i>
                        <h5>No hay datos de ventas en el período seleccionado</h5>
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = productos.map((item, index) => {
            const producto = item.producto ?? {};
            const stock = producto.stock ?? 0;
            const stockMinimo = producto.stock_minimo ?? 1;
            const stockClass = stock <= 0 ? 'secondary' : (stock <= stockMinimo ? 'danger' : 'success');

            const categorias = (producto.categorias ?? [])
                .map(c => `<span class="badge bg-secondary">${c.nombre ?? ''}</span>`)
                .join(' ') || 'N/A';

            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <strong>${producto.nombre ?? 'N/A'}</strong>
                        <br><small class="text-muted">${producto.marca ?? ''}</small>
                    </td>
                    <td>${producto.sku ?? 'N/A'}</td>
                    <td>${categorias}</td>
                    <td><span class="badge bg-info">${item.veces_vendido ?? 0}</span></td>
                    <td>${item.total_unidades ?? 0}</td>
                    <td><strong>Q${parseFloat(item.total_vendido ?? 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</strong></td>
                    <td><span class="badge bg-${stockClass}">${stock}</span></td>
                </tr>`;
        }).join('');

        // Aplicar búsqueda si hay texto
        if (currentSearch) aplicarBusquedaLocal();
    }

    function aplicarBusquedaLocal() {
        const rows = document.querySelectorAll('#productosTable tbody tr');
        rows.forEach(row => {
            const texto = row.textContent.toLowerCase();
            row.style.display = (!currentSearch || texto.includes(currentSearch)) ? '' : 'none';
        });
    }

    // ── Eventos ──────────────────────────────────────────

    document.querySelectorAll('.filter-periodo-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-periodo-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentPeriodo = this.dataset.periodo;

            if (currentPeriodo !== 'personalizado') {
                const fechas = getFechasPorPeriodo(currentPeriodo);
                fechaInicio = fechas.inicio;
                fechaFin    = fechas.fin;
                document.getElementById('fechaInicio').value = fechaInicio;
                document.getElementById('fechaFin').value    = fechaFin;
                cargarProductos();
            }
        });
    });

    document.getElementById('btnAplicarFiltros').addEventListener('click', function() {
        fechaInicio  = document.getElementById('fechaInicio').value;
        fechaFin     = document.getElementById('fechaFin').value;
        limiteFilter = document.getElementById('limiteFilter').value;
        cargarProductos();
    });

    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = this.value.toLowerCase().trim();
            aplicarBusquedaLocal();
        }, 300);
    });

    document.getElementById('clearSearch').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        currentSearch = '';
        aplicarBusquedaLocal();
    });

    document.getElementById('btnLimpiarFiltros').addEventListener('click', limpiarFiltros);

    window.limpiarFiltros = function() {
        document.getElementById('limiteFilter').value = '20';
        document.getElementById('searchInput').value  = '';
        limiteFilter  = '20';
        currentSearch = '';
        document.querySelector('.filter-periodo-btn[data-periodo="mes"]').click();
    };

    // Carga inicial
    document.querySelector('.filter-periodo-btn[data-periodo="mes"]').click();
});

function exportarReporte() {
    alert('Funcionalidad de exportación próximamente');
}
</script>
@endpush

@push('styles')
<style>
.filter-periodo-btn.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}
</style>
@endpush