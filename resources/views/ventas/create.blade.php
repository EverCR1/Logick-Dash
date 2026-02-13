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
            <input type="hidden" id="usuario_id" name="usuario_id" value="{{ auth()->id() }}">
            
            <input type="hidden" id="total_venta" name="total" value="0">
            @csrf
            <input type="hidden" id="descripcion" name="descripcion" value="">
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

                <!-- Selección del Producto/Servicio -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-shopping-cart me-2"></i> Producto/Servicio a Vender
                        </h6>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="tipo" class="form-label">Tipo *</label>
                        <select id="tipo" name="tipo" class="form-control" required onchange="cambiarTipo()">
                            <option value="producto" selected>Producto</option>
                            <option value="servicio">Servicio</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <!-- Campo para Producto -->
                    <div class="col-md-8 mb-3" id="campo_producto">
                        <label for="producto_id" class="form-label">Producto *</label>
                        <select id="producto_id" name="producto_id" class="form-control select2-busqueda" 
                                data-url="{{ route('ventas.buscar.productos.ajax') }}" 
                                onchange="seleccionarProducto()">
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
                        <small class="text-muted">Escriba para buscar productos por nombre, SKU o marca</small>
                    </div>
                    
                    <!-- Campo para Servicio -->
                    <div class="col-md-8 mb-3" id="campo_servicio" style="display: none;">
                        <label for="servicio_id" class="form-label">Servicio *</label>
                        <select id="servicio_id" name="servicio_id" class="form-control select2-busqueda" 
                                data-url="{{ route('ventas.buscar.servicios.ajax') }}"
                                onchange="seleccionarServicio()">
                            <option value="">-- Buscar servicio --</option>
                            @foreach($servicios as $servicio)
                                <option value="{{ $servicio['id'] }}" 
                                        data-precio="{{ $servicio['precio'] }}"
                                        data-nombre="{{ $servicio['nombre'] }}">
                                    {{ $servicio['nombre'] }} - Q{{ number_format($servicio['precio'], 2) }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Escriba para buscar servicios por nombre o código</small>
                    </div>
                    
                    <!-- Campo para Otro -->
                    <div class="col-md-8 mb-3" id="campo_otro" style="display: none;">
                        <label for="descripcion_otro" class="form-label">Descripción *</label>
                        <input type="text" id="descripcion_otro" name="descripcion" class="form-control" placeholder="Descripción del item a vender">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="cantidad" class="form-label">Cantidad *</label>
                        <input type="number" id="cantidad" name="cantidad" class="form-control" min="1" value="1" 
                               onchange="calcularTotal()" oninput="calcularTotal()" required>
                        <small id="stock_info" class="text-muted"></small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="precio_unitario" class="form-label">Precio Unitario (Q) *</label>
                        <input type="number" id="precio_unitario" name="precio_unitario" class="form-control" 
                               step="0.01" min="0" value="0" onchange="calcularTotal()" oninput="calcularTotal()" required>
                        <small class="text-muted" id="precio_info"></small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="descuento" class="form-label">Descuento (Q) <span class="text-muted">(Opcional)</span></label>
                        <input type="number" id="descuento" name="descuento" class="form-control" 
                               step="0.01" min="0" value="0" onchange="calcularTotal()" oninput="calcularTotal()">
                    </div>
                </div>

                <!-- Resumen de la Venta -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-calculator me-2"></i> Resumen de la Venta
                        </h6>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <strong id="subtotal">Q0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Descuento:</span>
                                    <strong id="descuento_total" class="text-danger">Q0.00</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="h5">TOTAL:</span>
                                    <strong id="total" class="h4 text-primary">Q0.00</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea id="observaciones" name="observaciones" class="form-control" rows="3" 
                                              placeholder="Notas adicionales sobre esta venta..."></textarea>
                                </div>
                                <div class="form-check">
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
                    <div>
                        <!-- Botón para probar datos -->
                        <button type="button" class="btn btn-info me-2" id="btnProbarDatos">
                            <i class="fas fa-bug me-2"></i> Probar Datos
                        </button>
                        
                        <!-- Botón original para registrar -->
                        <button type="submit" class="btn btn-success" id="btnRegistrar">
                            <i class="fas fa-check-circle me-2"></i> Registrar Venta
                        </button>
                    </div>
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

#total {
    font-size: 1.8rem;
    font-weight: 700;
}

.stock-bajo {
    color: #dc3545;
    font-weight: 600;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
<script>
// Variables globales
let productoSeleccionado = null;
let servicioSeleccionado = null;

// Inicializar Select2 para búsquedas
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 VERIFICANDO CAMPOS DEL FORMULARIO:');
    
    // Verificar que el campo descripcion existe
    const descripcionInput = document.getElementById('descripcion');
    if (descripcionInput) {
        console.log('✅ Campo descripcion encontrado:', descripcionInput);
    } else {
        console.error('❌ CAMPO DESCRIPCION NO ENCONTRADO');
    }
    
    // Inicializar Select2
    $('.select2-busqueda').select2({
        language: "es",
        width: '100%',
        placeholder: "Escriba para buscar...",
        allowClear: true
    });
    
    // Configurar búsqueda AJAX para cada select
    $('.select2-busqueda').each(function() {
        const $select = $(this);
        const url = $select.data('url');
        
        if (url) {
            $select.select2({
                language: "es",
                width: '100%',
                placeholder: "Escriba para buscar...",
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
                        console.log('Datos recibidos de API:', data);
                        if (data.success) {
                            const results = data.productos || data.servicios || data.clientes || [];
                            return {
                                results: results.map(item => ({
                                    id: item.id,
                                    text: item.nombre || item.text || 'Sin nombre',
                                    nombre: item.nombre,
                                    precio: item.precio || item.precio_venta || 0,
                                    stock: item.stock || 0,
                                    ...item
                                }))
                            };
                        }
                        return { results: [] };
                    },
                    cache: true
                },
                minimumInputLength: 2
            });
        }
    });
    
    // Calcular total inicial
    calcularTotal();
});

// =================== FUNCIONES PRINCIPALES ===================

// Cambiar tipo de venta
function cambiarTipo() {
    const tipo = document.getElementById('tipo').value;
    console.log('Tipo cambiado a:', tipo);
    
    // Ocultar todos los campos
    document.getElementById('campo_producto').style.display = 'none';
    document.getElementById('campo_servicio').style.display = 'none';
    document.getElementById('campo_otro').style.display = 'none';
    
    // Mostrar el campo correspondiente
    if (tipo === 'producto') {
        document.getElementById('campo_producto').style.display = 'block';
        document.getElementById('producto_id').required = true;
        document.getElementById('servicio_id').required = false;
        document.getElementById('descripcion_otro').required = false;
        
        // Limpiar otros campos
        document.getElementById('servicio_id').value = '';
        document.getElementById('descripcion_otro').value = '';
        document.getElementById('descripcion').value = ''; // CORREGIDO
    } else if (tipo === 'servicio') {
        document.getElementById('campo_servicio').style.display = 'block';
        document.getElementById('producto_id').required = false;
        document.getElementById('servicio_id').required = true;
        document.getElementById('descripcion_otro').required = false;
        
        // Limpiar otros campos
        document.getElementById('producto_id').value = '';
        document.getElementById('descripcion_otro').value = '';
        document.getElementById('descripcion').value = ''; // CORREGIDO
    } else {
        document.getElementById('campo_otro').style.display = 'block';
        document.getElementById('producto_id').required = false;
        document.getElementById('servicio_id').required = false;
        document.getElementById('descripcion_otro').required = true;
        
        // Limpiar otros campos
        document.getElementById('producto_id').value = '';
        document.getElementById('servicio_id').value = '';
        // La descripción se llenará con descripcion_otro
    }
    
    // Resetear información
    productoSeleccionado = null;
    servicioSeleccionado = null;
    document.getElementById('stock_info').textContent = '';
    document.getElementById('precio_info').textContent = '';
    document.getElementById('precio_unitario').value = '0';
    
    // Recalcular total
    calcularTotal();
}

// Seleccionar producto
function seleccionarProducto() {
    const select = document.getElementById('producto_id');
    const option = select.selectedOptions[0];
    
    console.log('Producto seleccionado:', option);
    
    // Verificar que el campo descripcion existe
    const descripcionInput = document.getElementById('descripcion');
    if (!descripcionInput) {
        console.error('❌ ERROR: No se encontró el campo con id="descripcion"');
        return;
    }
    
    if (option && option.value) {
        const nombre = option.dataset.nombre || option.text.split(' - ')[0];
        const precio = parseFloat(option.dataset.precio || 0);
        const stock = parseInt(option.dataset.stock || 0);
        
        productoSeleccionado = {
            id: option.value,
            nombre: nombre,
            precio: precio,
            stock: stock
        };
        
        // Actualizar precio unitario
        document.getElementById('precio_unitario').value = precio.toFixed(2);
        document.getElementById('precio_info').textContent = 'Precio obtenido del producto';
        
        // ACTUALIZAR DESCRIPCIÓN - CORREGIDO
        descripcionInput.value = nombre;
        console.log('✅ Descripción actualizada a:', nombre);
        console.log('✅ Valor actual del campo descripcion:', descripcionInput.value);
        
        // Actualizar información de stock
        const stockInfo = document.getElementById('stock_info');
        if (stock <= 0) {
            stockInfo.innerHTML = '<span class="stock-bajo">¡Producto sin stock disponible!</span>';
            stockInfo.className = 'stock-bajo';
            document.getElementById('cantidad').max = 0;
            document.getElementById('cantidad').value = 0;
        } else {
            stockInfo.textContent = `Stock disponible: ${stock} unidades`;
            stockInfo.className = 'text-muted';
            document.getElementById('cantidad').max = stock;
            
            // Si la cantidad actual es mayor al stock, ajustarla
            const cantidad = parseInt(document.getElementById('cantidad').value);
            if (cantidad > stock) {
                document.getElementById('cantidad').value = stock;
                mostrarAlerta('La cantidad ha sido ajustada al stock disponible', 'warning');
            }
        }
        
        // Recalcular total
        calcularTotal();
    } else {
        productoSeleccionado = null;
        document.getElementById('precio_unitario').value = '0';
        descripcionInput.value = ''; // CORREGIDO
        document.getElementById('stock_info').textContent = '';
        document.getElementById('precio_info').textContent = '';
    }
}

// Seleccionar servicio
function seleccionarServicio() {
    const select = document.getElementById('servicio_id');
    const option = select.selectedOptions[0];
    
    console.log('Servicio seleccionado:', option);
    
    // Verificar que el campo descripcion existe
    const descripcionInput = document.getElementById('descripcion');
    if (!descripcionInput) {
        console.error('❌ ERROR: No se encontró el campo con id="descripcion"');
        return;
    }
    
    if (option && option.value) {
        const nombre = option.dataset.nombre || option.text.split(' - ')[0];
        const precio = parseFloat(option.dataset.precio || 0);
        
        servicioSeleccionado = {
            id: option.value,
            nombre: nombre,
            precio: precio
        };
        
        // Actualizar precio unitario
        document.getElementById('precio_unitario').value = precio.toFixed(2);
        document.getElementById('precio_info').textContent = 'Precio obtenido del servicio';
        document.getElementById('stock_info').textContent = '';
        
        // ACTUALIZAR DESCRIPCIÓN - CORREGIDO
        descripcionInput.value = nombre;
        console.log('✅ Descripción actualizada a:', nombre);
        
        // Recalcular total
        calcularTotal();
    } else {
        servicioSeleccionado = null;
        document.getElementById('precio_unitario').value = '0';
        descripcionInput.value = ''; // CORREGIDO
        document.getElementById('precio_info').textContent = '';
    }
}

// Calcular total de la venta
function calcularTotal() {
    const cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
    const precioUnitario = parseFloat(document.getElementById('precio_unitario').value) || 0;
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    
    // Calcular subtotal
    const subtotal = cantidad * precioUnitario;
    const total = Math.max(0, subtotal - descuento);
    
    // Actualizar pantalla
    document.getElementById('subtotal').textContent = `Q${subtotal.toFixed(2)}`;
    document.getElementById('descuento_total').textContent = `Q${descuento.toFixed(2)}`;
    document.getElementById('total').textContent = `Q${total.toFixed(2)}`;
    
    // Actualizar campo hidden del total
    document.getElementById('total_venta').value = total.toFixed(2);
    
    // Validar stock si es producto
    if (productoSeleccionado && cantidad > productoSeleccionado.stock) {
        document.getElementById('stock_info').innerHTML = '<span class="stock-bajo">Cantidad excede el stock disponible</span>';
        document.getElementById('btnRegistrar').disabled = true;
    } else {
        document.getElementById('btnRegistrar').disabled = false;
    }
}

// =================== FUNCIÓN DE PRUEBA CORREGIDA ===================

function probarDatosFormulario() {
    const form = document.getElementById('formVenta');
    const tipo = document.getElementById('tipo').value;
    
    console.clear();
    console.log('🔄 ===== PRUEBA DE DATOS DEL FORMULARIO ===== 🔄');
    
    // VERIFICAR EL CAMPO DESCRIPCIÓN
    const descripcionInput = document.getElementById('descripcion');
    console.log('🔍 VERIFICACIÓN DEL CAMPO DESCRIPCIÓN:');
    console.log('   • Elemento encontrado:', descripcionInput ? 'Sí' : 'No');
    if (descripcionInput) {
        console.log('   • ID:', descripcionInput.id);
        console.log('   • Name:', descripcionInput.name);
        console.log('   • Valor ACTUAL:', descripcionInput.value || '(vacío)');
    }
    
    // Obtener FormData
    const formData = new FormData(form);
    const datos = {};
    
    for (let [key, value] of formData.entries()) {
        datos[key] = value;
    }
    
    console.log('\n📦 DATOS EN FORMDATA:');
    console.log('   • descripcion en FormData:', datos.descripcion || '(vacío)');
    
    // Mostrar resumen
    console.log('\n📤 DATOS PARA ENVIAR:');
    console.log(JSON.stringify(datos, null, 2));
    
    // Validar
    if (!datos.descripcion) {
        console.error('❌ LA DESCRIPCIÓN ESTÁ VACÍA EN FORMDATA');
        alert('⚠️ La descripción está vacía. Verifica que el campo hidden exista y se esté actualizando.');
    } else {
        console.log('✅ TODO CORRECTO - Descripción enviada:', datos.descripcion);
    }
}

// Función para enviar el formulario
function enviarFormularioVenta() {
    const tipo = document.getElementById('tipo').value;
    
    // Para "otro", copiar la descripción
    if (tipo === 'otro') {
        document.getElementById('descripcion').value = document.getElementById('descripcion_otro').value;
    }
    
    console.log('📤 Enviando formulario con descripción:', document.getElementById('descripcion').value);
    document.getElementById('formVenta').submit();
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
    setTimeout(() => alerta.remove(), 5000);
}

// Funciones de cliente
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
            bootstrap.Modal.getInstance(document.getElementById('modalCliente')).hide();
            mostrarAlerta('Cliente creado', 'success');
        }
    });
}

// =================== EVENT LISTENERS ===================

document.getElementById('btnProbarDatos').addEventListener('click', probarDatosFormulario);

document.getElementById('formVenta').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const tipo = document.getElementById('tipo').value;
    if (tipo === 'otro') {
        document.getElementById('descripcion').value = document.getElementById('descripcion_otro').value;
    }
    
    if (!document.getElementById('descripcion').value && tipo !== 'otro') {
        alert('Error: La descripción no se ha generado. Selecciona un producto o servicio.');
        return;
    }
    
    enviarFormularioVenta();
});
</script>
@endpush