@extends('layouts.app')

@section('title', 'Nueva Venta - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
    <li class="breadcrumb-item active">Nueva Venta</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-cash-register me-2"></i> Registrar Nueva Venta
            </h5>
        </div>
        
        <form id="formVenta" method="POST" action="{{ route('ventas.store') }}">
            @csrf
            <input type="hidden" id="items_json" name="items" value="[]">
            
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Información General -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-info-circle me-2"></i> Información General
                        </h6>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="cliente_id" class="form-label">Cliente <span class="text-muted">(Opcional)</span></label>
                        <div class="input-group">
                            <select id="cliente_id" name="cliente_id" class="form-control select2-busqueda" 
                                    data-url="{{ route('ventas.buscar.clientes.ajax') }}">
                                <option value="">-- Seleccionar cliente --</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente['id'] }}">{{ $cliente['nombre'] }} 
                                        @if(!empty($cliente['nit'])) | NIT: {{ $cliente['nit'] }} @endif
                                        @if(!empty($cliente['telefono'])) | Tel: {{ $cliente['telefono'] }} @endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-secondary" onclick="abrirModalCliente()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <small class="text-muted">Deje vacío para venta a cliente ocasional</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="metodo_pago" class="form-label">Método de Pago *</label>
                        <select id="metodo_pago" name="metodo_pago" class="form-control" required>
                            <option value="">-- Seleccionar método --</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="mixto">Mixto</option>
                        </select>
                    </div>
                </div>

                <!-- Agregar Items a la Venta -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-plus-circle me-2"></i> Agregar Productos/Servicios
                        </h6>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="item_tipo" class="form-label">Tipo *</label>
                        <select id="item_tipo" class="form-control" onchange="cambiarTipoItem()">
                            <option value="producto" selected>Producto</option>
                            <option value="servicio">Servicio</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <!-- Campo para Producto -->
                    <div class="col-md-3 mb-3" id="item_campo_producto">
                        <label for="item_producto_id" class="form-label">Producto *</label>
                        <select id="item_producto_id" class="form-control select2-item" 
                                data-url="{{ route('ventas.buscar.productos.ajax') }}">
                            <option value="">-- Buscar producto --</option>
                            @foreach($productos as $producto)
                                <option value="{{ $producto['id'] }}" 
                                        data-precio="{{ $producto['precio'] }}"
                                        data-nombre="{{ $producto['nombre'] }}"
                                        data-stock="{{ $producto['stock'] }}">
                                    {{ $producto['nombre'] }} - Q{{ number_format($producto['precio'], 2) }}
                                    @if($producto['stock'] > 0)
                                        (Stock: {{ $producto['stock'] }})
                                    @else
                                        <span class="text-danger">(Sin stock)</span>
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Campo para Servicio -->
                    <div class="col-md-3 mb-3" id="item_campo_servicio" style="display: none;">
                        <label for="item_servicio_id" class="form-label">Servicio *</label>
                        <select id="item_servicio_id" class="form-control select2-item" 
                                data-url="{{ route('ventas.buscar.servicios.ajax') }}">
                            <option value="">-- Buscar servicio --</option>
                            @foreach($servicios as $servicio)
                                <option value="{{ $servicio['id'] }}" 
                                        data-precio="{{ $servicio['precio'] }}"
                                        data-nombre="{{ $servicio['nombre'] }}">
                                    {{ $servicio['nombre'] }} - Q{{ number_format($servicio['precio'], 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Campo para Otro -->
                    <div class="col-md-3 mb-3" id="item_campo_otro" style="display: none;">
                        <label for="item_descripcion" class="form-label">Descripción *</label>
                        <input type="text" id="item_descripcion" class="form-control" placeholder="Ej: Cable HDMI">
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="item_cantidad" class="form-label">Cantidad *</label>
                        <input type="number" id="item_cantidad" class="form-control" min="1" value="1">
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="item_precio" class="form-label">Precio Unit. *</label>
                        <input type="number" id="item_precio" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    
                    <div class="col-md-2 mb-3">
                        <label for="item_descuento" class="form-label">Descuento (Q)</label>
                        <input type="number" id="item_descuento" class="form-control" step="0.01" min="0" value="0">
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <button type="button" class="btn btn-primary" onclick="agregarItem()">
                            <i class="fas fa-plus-circle me-2"></i> Agregar Item
                        </button>
                        <small id="item_stock_info" class="text-muted ms-3"></small>
                    </div>
                </div>

                <!-- Tabla de Items Agregados -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-list me-2"></i> Items de la Venta
                        </h6>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unit.</th>
                                        <th>Descuento</th>
                                        <th>Subtotal</th>
                                        <th>Total</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tabla-items">
                                    <tr id="fila-vacia">
                                        <td colspan="8" class="text-center py-4">
                                            <i class="fas fa-shopping-cart fa-2x text-muted mb-2"></i>
                                            <p class="text-muted">No hay items agregados. Agregue productos o servicios a la venta.</p>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot id="tabla-totales" style="display: none;">
                                    <tr class="table-secondary">
                                        <td colspan="5" class="text-end"><strong>Totales:</strong></td>
                                        <td><strong id="total_subtotal">Q0.00</strong></td>
                                        <td><strong id="total_descuento">Q0.00</strong></td>
                                        <td><strong id="total_general" class="text-primary">Q0.00</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control" rows="2" 
                                  placeholder="Notas adicionales sobre esta venta..."></textarea>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light h-100">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <div class="form-check mb-2">
                                    <input type="checkbox" id="es_credito" name="es_credito" class="form-check-input">
                                    <label for="es_credito" class="form-check-label">¿Es venta a crédito?</label>
                                </div>
                                <small class="text-muted">Si marca esta opción, el cliente deberá pagar posteriormente.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success" id="btnRegistrar" disabled>
                        <i class="fas fa-check-circle me-2"></i> Registrar Venta
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal para nuevo cliente rápido -->
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Cliente Rápido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formClienteRapido">
                    <div class="mb-3">
                        <label for="cliente_nombre" class="form-label">Nombre *</label>
                        <input type="text" id="cliente_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="cliente_nit" class="form-label">NIT</label>
                        <input type="text" id="cliente_nit" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="cliente_telefono" class="form-label">Teléfono</label>
                        <input type="text" id="cliente_telefono" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarClienteRapido()">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    border: 1px solid #ced4da;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 36px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}

.card.bg-light {
    border: 1px solid #dee2e6;
}

.table tfoot td {
    font-weight: 600;
}

.stock-bajo {
    color: #dc3545;
    font-weight: 600;
}

.item-eliminado {
    animation: fadeOut 0.3s ease;
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
<script>
// =================== VARIABLES GLOBALES ===================
let items = [];

// =================== INICIALIZACIÓN ===================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando Select2...');
    
    // Inicializar Select2 para clientes
    $('#cliente_id').select2({
        language: "es",
        width: '100%',
        placeholder: "Buscar cliente...",
        allowClear: true,
        ajax: {
            url: $('#cliente_id').data('url'),
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { 
                    query: params.term, 
                    limit: 10 
                };
            },
            processResults: function(data) {
                console.log('Respuesta clientes:', data);
                if (data.success && data.clientes) {
                    return {
                        results: data.clientes.map(item => ({
                            id: item.id,
                            text: `${item.nombre} ${item.nit ? '| NIT: ' + item.nit : ''} ${item.telefono ? '| Tel: ' + item.telefono : ''}`,
                            nombre: item.nombre,
                            nit: item.nit,
                            telefono: item.telefono
                        }))
                    };
                }
                return { results: [] };
            },
            cache: true
        },
        minimumInputLength: 2
    });
    
    // Inicializar Select2 para productos
    initProductoSelect2();
    
    // Inicializar Select2 para servicios
    initServicioSelect2();
    
    // Eventos
    $('#item_producto_id').on('change', function() {
        console.log('Producto seleccionado');
        actualizarDatosItem();
    });
    
    $('#item_servicio_id').on('change', function() {
        console.log('Servicio seleccionado');
        actualizarDatosItem();
    });
    
    $('#item_cantidad, #item_precio, #item_descuento').on('input', function() {
        validarStockItem();
    });
});

// Inicializar Select2 para productos - VERSIÓN CORREGIDA
function initProductoSelect2() {
    const $select = $('#item_producto_id');
    const url = $select.data('url');
    
    if (url) {
        $select.select2({
            language: "es",
            width: '100%',
            placeholder: "Buscar producto por nombre, SKU o marca...",
            allowClear: true,
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { 
                        query: params.term, 
                        limit: 10 
                    };
                },
                processResults: function(data) {
                    console.log('Respuesta productos API:', data);
                    
                    if (!data.success || !data.productos) {
                        return { results: [] };
                    }
                    
                    // Mapear los productos con datos personalizados
                    const results = data.productos.map(producto => ({
                        id: producto.id,
                        text: `${producto.nombre} - Q${parseFloat(producto.precio).toFixed(2)} ${parseInt(producto.stock) > 0 ? '(Stock: ' + producto.stock + ')' : '(Sin stock)'}`,
                        // Guardar todos los datos como atributos personalizados
                        nombre: producto.nombre,
                        precio: producto.precio, // Guardar como string, convertiremos después
                        stock: producto.stock, // Guardar como string, convertiremos después
                        sku: producto.sku || '',
                        marca: producto.marca || ''
                    }));
                    
                    return { results: results };
                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: function(item) {
                if (item.loading) return item.text;
                if (!item.id) return item.text;
                
                // Usar los datos guardados para mostrar bonito
                const precio = parseFloat(item.precio) || 0;
                const stock = parseInt(item.stock) || 0;
                
                return $(`<div class="d-flex justify-content-between align-items-center py-1">
                    <div>
                        <strong>${item.nombre}</strong>
                        ${item.sku ? `<br><small class="text-muted">SKU: ${item.sku}</small>` : ''}
                        ${item.marca ? `<small class="text-muted">Marca: ${item.marca}</small>` : ''}
                        ${stock > 0 ? `<br><small class="text-muted">Stock: ${stock}</small>` : '<br><small class="text-danger">Sin stock</small>'}
                    </div>
                    <div class="text-primary fw-bold">Q${precio.toFixed(2)}</div>
                </div>`);
            },
            templateSelection: function(item) {
                if (!item.id) return item.text;
                const precio = parseFloat(item.precio) || 0;
                return `${item.nombre} - Q${precio.toFixed(2)}`;
            }
        });
    }
}

// Inicializar Select2 para servicios - VERSIÓN CORREGIDA
function initServicioSelect2() {
    const $select = $('#item_servicio_id');
    const url = $select.data('url');
    
    if (url) {
        $select.select2({
            language: "es",
            width: '100%',
            placeholder: "Buscar servicio por nombre o código...",
            allowClear: true,
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { 
                        query: params.term, 
                        limit: 10 
                    };
                },
                processResults: function(data) {
                    console.log('Respuesta servicios API:', data);
                    
                    if (!data.success || !data.servicios) {
                        return { results: [] };
                    }
                    
                    const results = data.servicios.map(servicio => ({
                        id: servicio.id,
                        text: `${servicio.nombre} - Q${parseFloat(servicio.precio).toFixed(2)}`,
                        nombre: servicio.nombre,
                        precio: servicio.precio,
                        codigo: servicio.codigo || ''
                    }));
                    
                    return { results: results };
                },
                cache: true
            },
            minimumInputLength: 2,
            templateResult: function(item) {
                if (item.loading) return item.text;
                if (!item.id) return item.text;
                
                const precio = parseFloat(item.precio) || 0;
                
                return $(`<div class="d-flex justify-content-between align-items-center py-1">
                    <div>
                        <strong>${item.nombre}</strong>
                        ${item.codigo ? `<br><small class="text-muted">Código: ${item.codigo}</small>` : ''}
                    </div>
                    <div class="text-primary fw-bold">Q${precio.toFixed(2)}</div>
                </div>`);
            },
            templateSelection: function(item) {
                if (!item.id) return item.text;
                const precio = parseFloat(item.precio) || 0;
                return `${item.nombre} - Q${precio.toFixed(2)}`;
            }
        });
    }
}

// =================== FUNCIONES PARA ITEMS ===================

// Cambiar tipo de item
function cambiarTipoItem() {
    const tipo = document.getElementById('item_tipo').value;
    console.log('Cambiando tipo a:', tipo);
    
    // Ocultar todos los campos
    document.getElementById('item_campo_producto').style.display = 'none';
    document.getElementById('item_campo_servicio').style.display = 'none';
    document.getElementById('item_campo_otro').style.display = 'none';
    
    // Mostrar el campo correspondiente
    if (tipo === 'producto') {
        document.getElementById('item_campo_producto').style.display = 'block';
        limpiarCamposItem();
        reiniciarSelect2('#item_producto_id');
    } else if (tipo === 'servicio') {
        document.getElementById('item_campo_servicio').style.display = 'block';
        limpiarCamposItem();
        reiniciarSelect2('#item_servicio_id');
    } else {
        document.getElementById('item_campo_otro').style.display = 'block';
        document.getElementById('item_descripcion').value = '';
        document.getElementById('item_precio').value = '0';
        document.getElementById('item_stock_info').textContent = '';
    }
}

// Reiniciar Select2
function reiniciarSelect2(selector) {
    $(selector).val(null).trigger('change');
}

// Limpiar campos de item
function limpiarCamposItem() {
    document.getElementById('item_cantidad').value = '1';
    document.getElementById('item_precio').value = '0';
    document.getElementById('item_descuento').value = '0';
    document.getElementById('item_stock_info').textContent = '';
}

// Actualizar datos del item seleccionado - VERSIÓN CORREGIDA
function actualizarDatosItem() {
    const tipo = document.getElementById('item_tipo').value;
    console.log('Actualizando datos para tipo:', tipo);
    
    if (tipo === 'producto') {
        const select = document.getElementById('item_producto_id');
        // Obtener el elemento seleccionado directamente del DOM
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            // Obtener los datos desde los atributos data-*
            const precio = parseFloat(selectedOption.dataset.precio) || 0;
            const stock = parseInt(selectedOption.dataset.stock) || 0;
            const nombre = selectedOption.dataset.nombre || '';
            
            console.log('Datos del producto seleccionado:', {
                precio: precio,
                stock: stock,
                nombre: nombre
            });
            
            document.getElementById('item_precio').value = precio.toFixed(2);
            
            const stockInfo = document.getElementById('item_stock_info');
            if (stock <= 0) {
                stockInfo.innerHTML = '<span class="stock-bajo">⚠️ ¡Sin stock disponible!</span>';
                document.getElementById('item_cantidad').max = 0;
                document.getElementById('item_cantidad').value = 0;
            } else {
                stockInfo.innerHTML = `<span class="text-success">✓ Stock disponible: ${stock} unidades</span>`;
                document.getElementById('item_cantidad').max = stock;
                
                // Ajustar cantidad si es mayor al stock
                const cantidad = parseInt(document.getElementById('item_cantidad').value) || 1;
                if (cantidad > stock) {
                    document.getElementById('item_cantidad').value = stock;
                }
            }
        } else {
            console.log('No hay producto seleccionado');
            document.getElementById('item_precio').value = '0';
            document.getElementById('item_stock_info').textContent = '';
        }
    } else if (tipo === 'servicio') {
        const select = document.getElementById('item_servicio_id');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const precio = parseFloat(selectedOption.dataset.precio) || 0;
            
            console.log('Datos del servicio seleccionado:', {
                precio: precio
            });
            
            document.getElementById('item_precio').value = precio.toFixed(2);
            document.getElementById('item_stock_info').innerHTML = '<span class="text-info">✓ Servicio disponible</span>';
        } else {
            document.getElementById('item_precio').value = '0';
            document.getElementById('item_stock_info').textContent = '';
        }
    }
    
    validarStockItem();
}

// Validar stock del item - VERSIÓN CORREGIDA
function validarStockItem() {
    const tipo = document.getElementById('item_tipo').value;
    
    if (tipo === 'producto') {
        const select = document.getElementById('item_producto_id');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const stock = parseInt(selectedOption.dataset.stock) || 0;
            const cantidad = parseInt(document.getElementById('item_cantidad').value) || 0;
            
            if (cantidad > stock) {
                document.getElementById('item_stock_info').innerHTML = '<span class="stock-bajo">❌ La cantidad excede el stock disponible</span>';
                return false;
            } else if (cantidad > 0) {
                document.getElementById('item_stock_info').innerHTML = `<span class="text-success">✓ Cantidad válida (Stock: ${stock})</span>`;
                return true;
            }
        }
    } else if (tipo === 'servicio') {
        return true;
    }
    
    return true;
}

// Agregar item a la venta - VERSIÓN CORREGIDA
function agregarItem() {
    console.log('Agregando item...');
    
    const tipo = document.getElementById('item_tipo').value;
    let descripcion = '';
    let producto_id = null;
    let servicio_id = null;
    let referencia = null;
    
    // Validar según tipo
    if (tipo === 'producto') {
        const select = document.getElementById('item_producto_id');
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            mostrarAlerta('Debe seleccionar un producto', 'warning');
            return;
        }
        
        producto_id = selectedOption.value;
        descripcion = selectedOption.dataset.nombre || '';
    } 
    else if (tipo === 'servicio') {
        const select = document.getElementById('item_servicio_id');
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            mostrarAlerta('Debe seleccionar un servicio', 'warning');
            return;
        }
        
        servicio_id = selectedOption.value;
        descripcion = selectedOption.dataset.nombre || '';
    } 
    else {
        descripcion = document.getElementById('item_descripcion').value.trim();
        if (!descripcion) {
            mostrarAlerta('Debe ingresar una descripción', 'warning');
            return;
        }
        referencia = 'EXT-' + Date.now().toString().slice(-6);
    }
    
    const cantidad = parseInt(document.getElementById('item_cantidad').value);
    const precioUnitario = parseFloat(document.getElementById('item_precio').value);
    const descuento = parseFloat(document.getElementById('item_descuento').value) || 0;
    
    // Validaciones
    if (!cantidad || cantidad < 1) {
        mostrarAlerta('La cantidad debe ser mayor a 0', 'warning');
        return;
    }
    
    if (!precioUnitario || precioUnitario <= 0) {
        mostrarAlerta('El precio debe ser mayor a 0', 'warning');
        return;
    }
    
    if (descuento < 0) {
        mostrarAlerta('El descuento no puede ser negativo', 'warning');
        return;
    }
    
    if (descuento > (cantidad * precioUnitario)) {
        mostrarAlerta('El descuento no puede ser mayor al subtotal', 'warning');
        return;
    }
    
    // Validar stock
    if (!validarStockItem()) {
        mostrarAlerta('La cantidad excede el stock disponible', 'error');
        return;
    }
    
    // Calcular subtotales
    const subtotal = cantidad * precioUnitario;
    const total = subtotal - descuento;
    
    // Crear item
    const item = {
        id: Date.now() + Math.random(),
        tipo: tipo,
        cantidad: cantidad,
        descripcion: descripcion,
        precio_unitario: precioUnitario,
        descuento: descuento,
        subtotal: subtotal,
        total: total,
        producto_id: producto_id,
        servicio_id: servicio_id,
        referencia: referencia
    };
    
    console.log('Item creado:', item);
    
    // Agregar a la lista
    items.push(item);
    
    // Actualizar tabla
    actualizarTablaItems();
    
    // Limpiar campos
    limpiarCamposItem();
    reiniciarSelect2('#item_producto_id');
    reiniciarSelect2('#item_servicio_id');
    document.getElementById('item_descripcion').value = '';
    
    // Mostrar mensaje
    mostrarAlerta('Item agregado correctamente', 'success');
}

// Actualizar tabla de items - VERSIÓN CORREGIDA
function actualizarTablaItems() {
    const tbody = document.getElementById('tabla-items');
    const filaVacia = document.getElementById('fila-vacia');
    const tablaTotales = document.getElementById('tabla-totales');
    
    // Verificar que los elementos existan
    if (!tbody) {
        console.error('No se encontró el elemento tabla-items');
        return;
    }
    
    if (items.length === 0) {
        // Mostrar fila vacía si existe
        if (filaVacia) {
            filaVacia.style.display = '';
        }
        if (tablaTotales) {
            tablaTotales.style.display = 'none';
        }
        if (document.getElementById('btnRegistrar')) {
            document.getElementById('btnRegistrar').disabled = true;
        }
        
        // Limpiar el tbody y mostrar la fila vacía
        tbody.innerHTML = '';
        if (filaVacia) {
            tbody.appendChild(filaVacia);
        }
        return;
    }
    
    // Ocultar fila vacía si existe
    if (filaVacia) {
        filaVacia.style.display = 'none';
    }
    if (tablaTotales) {
        tablaTotales.style.display = '';
    }
    if (document.getElementById('btnRegistrar')) {
        document.getElementById('btnRegistrar').disabled = false;
    }
    
    // Generar filas
    let html = '';
    items.forEach((item, index) => {
        const tipoClass = item.tipo === 'producto' ? 'primary' : (item.tipo === 'servicio' ? 'info' : 'secondary');
        
        html += `<tr>
            <td><span class="badge bg-${tipoClass}">${ucfirst(item.tipo)}</span></td>
            <td>
                ${item.descripcion}
                ${item.referencia ? `<br><small class="text-muted">Ref: ${item.referencia}</small>` : ''}
            </td>
            <td class="text-center">${item.cantidad}</td>
            <td class="text-end">Q${item.precio_unitario.toFixed(2)}</td>
            <td class="text-end ${item.descuento > 0 ? 'text-danger' : ''}">Q${item.descuento.toFixed(2)}</td>
            <td class="text-end">Q${item.subtotal.toFixed(2)}</td>
            <td class="text-end"><strong>Q${item.total.toFixed(2)}</strong></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarItem(${index})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    });
    
    tbody.innerHTML = html;
    
    // Actualizar totales
    const totalSubtotal = items.reduce((sum, item) => sum + item.subtotal, 0);
    const totalDescuento = items.reduce((sum, item) => sum + item.descuento, 0);
    const totalGeneral = items.reduce((sum, item) => sum + item.total, 0);
    
    const totalSubtotalEl = document.getElementById('total_subtotal');
    const totalDescuentoEl = document.getElementById('total_descuento');
    const totalGeneralEl = document.getElementById('total_general');
    
    if (totalSubtotalEl) totalSubtotalEl.textContent = `Q${totalSubtotal.toFixed(2)}`;
    if (totalDescuentoEl) totalDescuentoEl.textContent = `Q${totalDescuento.toFixed(2)}`;
    if (totalGeneralEl) totalGeneralEl.textContent = `Q${totalGeneral.toFixed(2)}`;
    
    // Actualizar campo hidden con JSON
    const itemsJsonEl = document.getElementById('items_json');
    if (itemsJsonEl) {
        itemsJsonEl.value = JSON.stringify(items.map(item => ({
            tipo: item.tipo,
            cantidad: item.cantidad,
            descripcion: item.descripcion,
            precio_unitario: item.precio_unitario,
            descuento: item.descuento,
            producto_id: item.producto_id,
            servicio_id: item.servicio_id,
            referencia: item.referencia
        })));
    }
}

// Eliminar item - VERSIÓN CORREGIDA
function eliminarItem(index) {
    if (confirm('¿Está seguro de eliminar este item?')) {
        items.splice(index, 1);
        actualizarTablaItems();
        mostrarAlerta('Item eliminado', 'info');
        
        // Si no quedan items, asegurar que se muestre la fila vacía
        if (items.length === 0) {
            const tbody = document.getElementById('tabla-items');
            const filaVacia = document.getElementById('fila-vacia');
            if (tbody && filaVacia) {
                tbody.innerHTML = '';
                tbody.appendChild(filaVacia);
            }
        }
    }
}

// =================== FUNCIONES AUXILIARES ===================

// Primera letra mayúscula
function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Mostrar alerta
function mostrarAlerta(mensaje, tipo = 'info') {
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo === 'error' ? 'danger' : tipo} alert-dismissible fade show`;
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.card-body').insertBefore(alerta, document.querySelector('.card-body').firstChild);
    setTimeout(() => alerta.remove(), 3000);
}

// =================== FUNCIONES DE CLIENTE ===================

function abrirModalCliente() {
    document.getElementById('cliente_nombre').value = '';
    document.getElementById('cliente_nit').value = '';
    document.getElementById('cliente_telefono').value = '';
    new bootstrap.Modal(document.getElementById('modalCliente')).show();
}

function guardarClienteRapido() {
    const nombre = document.getElementById('cliente_nombre').value.trim();
    if (!nombre) {
        mostrarAlerta('El nombre es requerido', 'error');
        return;
    }
    
    fetch('{{ route("clientes.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nombre: nombre,
            nit: document.getElementById('cliente_nit').value.trim(),
            telefono: document.getElementById('cliente_telefono').value.trim(),
            tipo: 'natural',
            estado: 'activo'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('cliente_id');
            const option = new Option(nombre, data.cliente.id);
            select.appendChild(option);
            select.value = data.cliente.id;
            $(select).trigger('change');
            bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
            mostrarAlerta('Cliente creado exitosamente', 'success');
        } else {
            mostrarAlerta('Error al crear cliente', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error al crear cliente', 'error');
    });
}

// =================== VALIDACIÓN DEL FORMULARIO ===================

document.getElementById('formVenta').addEventListener('submit', function(e) {
    if (items.length === 0) {
        e.preventDefault();
        mostrarAlerta('Debe agregar al menos un producto o servicio a la venta', 'error');
        return;
    }
    
    if (!document.getElementById('metodo_pago').value) {
        e.preventDefault();
        mostrarAlerta('Debe seleccionar un método de pago', 'error');
        return;
    }
});
</script>
@endpush