{{-- resources/views/ventas/partials/_modal_cliente_rapido.blade.php --}}
<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Agregar Cliente Rápido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formClienteRapido">
                    <div class="mb-3">
                        <label for="cliente_nombre" class="form-label">Nombre *</label>
                        <input type="text" id="cliente_nombre" class="form-control" 
                               placeholder="Ingrese el nombre completo" required>
                        <div class="invalid-feedback" id="nombre-error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cliente_nit" class="form-label">NIT</label>
                        <input type="text" id="cliente_nit" class="form-control" 
                               placeholder="Ingrese el NIT">
                        <div class="invalid-feedback" id="nit-error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cliente_telefono" class="form-label">Teléfono</label>
                        <input type="text" id="cliente_telefono" class="form-control" 
                               placeholder="Ingrese el teléfono">
                    </div>
                    
                    <div class="mb-3">
                        <label for="cliente_email" class="form-label">Email</label>
                        <input type="email" id="cliente_email" class="form-control" 
                               placeholder="Ingrese el email">
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cliente_direccion" class="form-label">Dirección</label>
                        <textarea id="cliente_direccion" class="form-control" rows="2" 
                                  placeholder="Ingrese la dirección"></textarea>
                    </div>
                    
                    <input type="hidden" id="cliente_tipo" value="natural">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarClienteRapido">
                    <i class="fas fa-save me-2"></i>Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>