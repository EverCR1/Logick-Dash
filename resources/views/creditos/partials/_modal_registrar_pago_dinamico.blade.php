{{-- Modal dinámico para registrar pago/abono - Reutilizable --}}
<div class="modal fade" id="modalRegistrarPago" tabindex="-1" aria-labelledby="modalRegistrarPagoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalRegistrarPagoLabel">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    Registrar Pago/Abono
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRegistrarPago" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3" id="infoCliente" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>Cliente:</strong></span>
                            <span class="fw-bold" id="clienteNombre"></span>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-3" id="infoCapital">
                        <div class="d-flex justify-content-between">
                            <span><strong>Capital restante:</strong></span>
                            <span class="text-danger fw-bold" id="capitalRestanteTexto">Q0.00</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="montoPago" class="form-label">
                            Monto (Q) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Q</span>
                            <input type="number" step="0.01" min="0.01" 
                                   class="form-control" id="montoPago" name="monto" 
                                   placeholder="0.00" required>
                        </div>
                        <small class="form-text text-muted" id="montoMaximoTexto">
                            Máximo: Q0.00
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Pago <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" 
                                       id="tipoAbono" value="abono" checked>
                                <label class="form-check-label" for="tipoAbono">
                                    <i class="fas fa-money-bill-wave text-warning me-1"></i>
                                    Abono (pago parcial)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" 
                                       id="tipoPagoTotal" value="pago_total">
                                <label class="form-check-label" for="tipoPagoTotal">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Pago Total (liquidar crédito)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observacionesPago" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observacionesPago" name="observaciones" 
                                  rows="2" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                    
                    <div class="bg-light p-3 rounded">
                        <h6 class="mb-2">Opciones rápidas:</h6>
                        <div class="d-flex gap-2 flex-wrap" id="opcionesRapidas">
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnRegistrarPago">
                        <i class="fas fa-save me-2"></i>Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables del modal
    const modalRegistrarPago = document.getElementById('modalRegistrarPago');
    const formRegistrarPago = document.getElementById('formRegistrarPago');
    const montoInput = document.getElementById('montoPago');
    const tipoAbono = document.getElementById('tipoAbono');
    const tipoPagoTotal = document.getElementById('tipoPagoTotal');
    const capitalRestanteTexto = document.getElementById('capitalRestanteTexto');
    const montoMaximoTexto = document.getElementById('montoMaximoTexto');
    const opcionesRapidas = document.getElementById('opcionesRapidas');
    const infoCliente = document.getElementById('infoCliente');
    const clienteNombre = document.getElementById('clienteNombre');
    
    let capitalRestanteActual = 0;
    let creditoIdActual = null;
    
    // Función para abrir el modal con los datos del crédito
    window.abrirModalPago = function(creditoId, nombreCliente, capitalRestante) {
        creditoIdActual = creditoId;
        capitalRestanteActual = capitalRestante;
        
        // Actualizar action del formulario
        formRegistrarPago.action = `{{ url('creditos') }}/${creditoId}/registrar-pago`;
        
        // Mostrar información del cliente si está disponible
        if (nombreCliente && nombreCliente !== 'N/A') {
            clienteNombre.textContent = nombreCliente;
            infoCliente.style.display = 'block';
        } else {
            infoCliente.style.display = 'none';
        }
        
        // Actualizar textos de capital
        capitalRestanteTexto.textContent = `Q${capitalRestante.toFixed(2)}`;
        montoMaximoTexto.textContent = `Máximo: Q${capitalRestante.toFixed(2)}`;
        
        // Configurar input de monto
        montoInput.max = capitalRestante;
        montoInput.value = '';
        montoInput.placeholder = `Q${capitalRestante.toFixed(2)}`;
        
        // Resetear tipo de pago
        tipoAbono.checked = true;
        
        // Generar opciones rápidas
        generarOpcionesRapidas(capitalRestante);
        
        // Abrir modal
        const modal = new bootstrap.Modal(modalRegistrarPago);
        modal.show();
    };
    
    // Función para generar botones de opciones rápidas
    function generarOpcionesRapidas(capitalRestante) {
        opcionesRapidas.innerHTML = '';
        
        if (capitalRestante <= 0) return;
        
        const porcentajes = [25, 50, 75, 100];
        
        porcentajes.forEach(porcentaje => {
            const monto = (capitalRestante * porcentaje / 100).toFixed(2);
            
            // Solo mostrar si el monto es mayor a 0
            if (monto > 0) {
                const boton = document.createElement('button');
                boton.type = 'button';
                boton.className = 'btn btn-sm btn-outline-primary';
                boton.setAttribute('data-monto', monto);
                boton.textContent = `Q${Number(monto).toFixed(0)} (${porcentaje}%)`;
                
                boton.addEventListener('click', function() {
                    montoInput.value = this.dataset.monto;
                    // Disparar evento input para actualizar tipo de pago
                    montoInput.dispatchEvent(new Event('input'));
                });
                
                opcionesRapidas.appendChild(boton);
            }
        });
    }
    
    // Auto-seleccionar pago total si el monto es igual al capital restante
    montoInput.addEventListener('input', function() {
        const monto = parseFloat(this.value) || 0;
        
        if (Math.abs(monto - capitalRestanteActual) < 0.01) {
            tipoPagoTotal.checked = true;
        } else {
            tipoAbono.checked = true;
        }
    });
    
    // Al seleccionar pago total, llenar con el monto completo
    tipoPagoTotal.addEventListener('change', function() {
        if (this.checked && capitalRestanteActual > 0) {
            montoInput.value = capitalRestanteActual.toFixed(2);
        }
    });
    
    // Validar antes de enviar
    formRegistrarPago.addEventListener('submit', function(e) {
        const monto = parseFloat(montoInput.value) || 0;
        
        if (monto <= 0) {
            e.preventDefault();
            alert('Por favor, ingresa un monto válido mayor a 0');
            return;
        }
        
        if (monto > capitalRestanteActual) {
            e.preventDefault();
            alert(`El monto no puede ser mayor a Q${capitalRestanteActual.toFixed(2)}`);
            return;
        }
        
        // Confirmar según el tipo de pago
        let mensaje = '';
        if (tipoPagoTotal.checked) {
            mensaje = '¿Estás seguro de registrar el PAGO TOTAL de este crédito?';
        } else {
            mensaje = `¿Confirmas el registro del ABONO de Q${monto.toFixed(2)}?`;
        }
        
        if (!confirm(mensaje)) {
            e.preventDefault();
        }
    });
    
    // Limpiar formulario cuando se cierra el modal
    modalRegistrarPago.addEventListener('hidden.bs.modal', function() {
        formRegistrarPago.reset();
        montoInput.value = '';
        tipoAbono.checked = true;
    });
});
</script>
@endpush