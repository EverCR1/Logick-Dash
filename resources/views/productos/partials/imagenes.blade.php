<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Imágenes del Producto</h6>
        <small class="text-muted">Máximo 10 imágenes por producto</small>
    </div>
    <div class="card-body">
        <!-- Vista previa de imágenes existentes -->
        <div class="row mb-4" id="imagenes-preview">
            @if(isset($producto['imagenes']) && count($producto['imagenes']) > 0)
                @foreach($producto['imagenes'] as $imagen)
                <div class="col-md-3 col-6 mb-3 imagen-item" data-id="{{ $imagen['id'] }}">
                    <div class="card position-relative border {{ $imagen['es_principal'] ? 'border-success' : '' }}">
                        @php
                            // Función temporal si ImageHelper no existe
                            function getImageUrl($image, $size = 'medium') {
                                if (!$image) return 'https://via.placeholder.com/300x300?text=Sin+Imagen';
                                
                                if (is_array($image)) {
                                    if ($size === 'thumb' && isset($image['url_thumb'])) {
                                        return $image['url_thumb'];
                                    }
                                    if ($size === 'medium' && isset($image['url_medium'])) {
                                        return $image['url_medium'];
                                    }
                                    return $image['url'] ?? 'https://via.placeholder.com/300x300?text=Sin+Imagen';
                                }
                                
                                return $image;
                            }
                        @endphp
                        
                        <img src="{{ getImageUrl($imagen, 'thumb') }}" 
                             class="card-img-top" 
                             alt="Imagen {{ $loop->iteration }}"
                             style="height: 150px; object-fit: cover; cursor: pointer;"
                             onclick="abrirModalImagen('{{ getImageUrl($imagen, 'original') }}', '{{ $imagen['nombre_original'] }}')">
                        
                        @if($imagen['es_principal'])
                            <div class="position-absolute top-0 start-0 m-2">
                                <span class="badge bg-success">
                                    <i class="fas fa-star me-1"></i> Principal
                                </span>
                            </div>
                        @endif
                        
                        <div class="card-body p-2 text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                @if(!$imagen['es_principal'])
                                <button type="button" class="btn btn-outline-success btn-set-main" 
                                        data-id="{{ $imagen['id'] }}"
                                        title="Establecer como principal">
                                    <i class="fas fa-star"></i>
                                </button>
                                @endif
                                <button type="button" class="btn btn-outline-danger btn-delete-image" 
                                        data-id="{{ $imagen['id'] }}"
                                        data-nombre="{{ $imagen['nombre_original'] }}"
                                        title="Eliminar imagen">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <small class="d-block text-muted mt-1">{{ $imagen['nombre_original'] }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-12 text-center">
                    <div class="bg-light rounded p-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No hay imágenes para este producto</p>
                        <p class="text-muted small">Sube imágenes usando el formulario de abajo</p>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Formulario de subida -->
        <div class="border-top pt-4">
            <h6 class="mb-3">Subir nuevas imágenes</h6>
            
            <form id="formUploadImages" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="producto_id" value="{{ $producto['id'] ?? '' }}">
                
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="imagenes" class="form-label">Seleccionar imágenes</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="imagenes" 
                                   name="imagenes[]" 
                                   multiple 
                                   accept="image/*"
                                   required>
                            <div class="form-text">
                                Formatos aceptados: JPEG, PNG, GIF, WebP. Máximo 5MB por imagen.
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="establecer_principal" name="establecer_principal" value="1" checked>
                                <label class="form-check-label" for="establecer_principal">
                                    Establecer primera imagen como principal
                                </label>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-primary w-100" id="btn-upload-images">
                            <i class="fas fa-upload me-2"></i> Subir Imágenes
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Vista previa de nuevas imágenes -->
            <div class="row mt-3 d-none" id="nuevas-imagenes-preview"></div>
            
            <!-- Progreso de carga -->
            <div class="progress mt-3 d-none" style="height: 20px;" id="upload-progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 0%" 
                     aria-valuenow="0" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    0%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver imagen en grande -->
<div class="modal fade" id="modalImagen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImagenTitulo"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImagenSrc" class="img-fluid" alt="">
            </div>
        </div>
    </div>
</div>

<style>
.imagen-item {
    transition: transform 0.2s;
}

.imagen-item:hover {
    transform: translateY(-2px);
}

.card-img-top {
    cursor: pointer;
    transition: opacity 0.2s;
}

.card-img-top:hover {
    opacity: 0.9;
}

.badge.bg-success {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.border-success {
    border-width: 2px !important;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
</style>

<script>
// Variable global para el token
let apiToken = '{{ session("api_token") }}';

document.addEventListener('DOMContentLoaded', function() {
    const productoId = document.querySelector('input[name="producto_id"]').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Vista previa de imágenes seleccionadas
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('nuevas-imagenes-preview');
        previewContainer.innerHTML = '';
        previewContainer.classList.remove('d-none');
        
        const files = e.target.files;
        
        if (files.length > 0) {
            previewContainer.innerHTML = `
                <div class="col-12">
                    <h6 class="mb-3 text-muted">
                        <i class="fas fa-eye me-2"></i>
                        Vista previa (${files.length} imagen${files.length > 1 ? 'es' : ''})
                    </h6>
                </div>
                <div class="row">
            `;
            
            for (let i = 0; i < files.length && i < 4; i++) { // Mostrar máximo 4
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 col-6 mb-3';
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" 
                                 class="card-img-top" 
                                 style="height: 120px; object-fit: cover;"
                                 alt="Vista previa ${i+1}">
                            <div class="card-body p-2 text-center">
                                <small class="text-muted text-truncate d-block">${file.name}</small>
                                <small class="text-muted">${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                            </div>
                        </div>
                    `;
                    previewContainer.querySelector('.row').appendChild(col);
                };
                
                reader.readAsDataURL(file);
            }
            
            if (files.length > 4) {
                const col = document.createElement('div');
                col.className = 'col-12 text-center';
                col.innerHTML = `
                    <div class="alert alert-info py-2">
                        <i class="fas fa-info-circle me-2"></i>
                        +${files.length - 4} imágenes más no mostradas
                    </div>
                `;
                previewContainer.appendChild(col);
            }
            
            previewContainer.innerHTML += '</div>';
        }
    });
    
    // Subir imágenes - VERSIÓN CORREGIDA
    document.getElementById('btn-upload-images').addEventListener('click', function() {
        const filesInput = document.getElementById('imagenes');
        const btn = this;
        const progressBar = document.getElementById('upload-progress');
        const progressFill = progressBar.querySelector('.progress-bar');
        
        // Validar que haya archivos
        if (filesInput.files.length === 0) {
            showAlert('warning', 'Por favor selecciona al menos una imagen');
            return;
        }
        
        // Validar tamaño máximo
        let totalSize = 0;
        for (let file of filesInput.files) {
            totalSize += file.size;
            if (file.size > 5 * 1024 * 1024) { // 5MB
                showAlert('danger', `La imagen "${file.name}" excede el tamaño máximo de 5MB`);
                return;
            }
        }
        
        if (totalSize > 50 * 1024 * 1024) { // 50MB total
            showAlert('danger', 'El tamaño total de las imágenes excede el límite de 50MB');
            return;
        }
        
        // Crear FormData MANUALMENTE - SOLUCIÓN AL PROBLEMA
        const formData = new FormData();
        
        // Agregar el token CSRF
        formData.append('_token', csrfToken);
        
        // Agregar producto_id
        formData.append('producto_id', productoId);
        
        // Agregar el checkbox establecer_principal
        const establecerPrincipal = document.getElementById('establecer_principal').checked ? '1' : '0';
        formData.append('establecer_principal', establecerPrincipal);
        
        // Agregar los archivos
        for (let i = 0; i < filesInput.files.length; i++) {
            formData.append('imagenes[]', filesInput.files[i]);
        }
        
        // Deshabilitar botón y mostrar progreso
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Subiendo...';
        btn.disabled = true;
        progressBar.classList.remove('d-none');
        progressFill.style.width = '0%';
        progressFill.textContent = '0%';
        
        // Hacer la petición
        fetch(`/productos/${productoId}/subir-imagenes`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success || data.message) {
                progressFill.style.width = '100%';
                progressFill.textContent = '100%';
                
                setTimeout(() => {
                    showAlert('success', '¡Imágenes subidas exitosamente!');
                    // Recargar la página para ver las nuevas imágenes
                    setTimeout(() => location.reload(), 1000);
                }, 500);
            } else {
                throw new Error(data.message || 'Error al subir imágenes');
            }
        })
        .catch(error => {
            showAlert('danger', `Error: ${error.message}`);
            btn.innerHTML = '<i class="fas fa-upload me-2"></i> Subir Imágenes';
            btn.disabled = false;
            progressBar.classList.add('d-none');
        });
    });
    
    // Establecer imagen como principal
    document.querySelectorAll('.btn-set-main').forEach(btn => {
        btn.addEventListener('click', function() {
            const imagenId = this.getAttribute('data-id');
            
            if (confirm('¿Establecer esta imagen como principal? La imagen principal es la que se muestra en listados y catálogos.')) {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                fetch(`/productos/${productoId}/imagenes/${imagenId}/establecer-principal`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success || data.message) {
                        showAlert('success', 'Imagen principal actualizada correctamente');
                        // Recargar para actualizar la interfaz
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        throw new Error(data.message || 'Error al actualizar');
                    }
                })
                .catch(error => {
                    showAlert('danger', error.message);
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                });
            }
        });
    });
    
    // Eliminar imagen
    document.querySelectorAll('.btn-delete-image').forEach(btn => {
        btn.addEventListener('click', function() {
            const imagenId = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            
            if (confirm(`¿Estás seguro de eliminar la imagen "${nombre}"?\n\nEsta acción no se puede deshacer y la imagen se eliminará permanentemente.`)) {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;
                
                fetch(`/productos/${productoId}/imagenes/${imagenId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success || data.message) {
                        showAlert('success', 'Imagen eliminada correctamente');
                        // Remover el elemento de la vista
                        const imagenElement = document.querySelector(`.imagen-item[data-id="${imagenId}"]`);
                        if (imagenElement) {
                            imagenElement.style.opacity = '0.5';
                            setTimeout(() => {
                                imagenElement.remove();
                                
                                // Si no quedan imágenes, mostrar mensaje
                                if (document.querySelectorAll('.imagen-item').length === 0) {
                                    document.getElementById('imagenes-preview').innerHTML = `
                                        <div class="col-12 text-center">
                                            <div class="bg-light rounded p-5">
                                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                                <p class="text-muted mb-0">No hay imágenes para este producto</p>
                                                <p class="text-muted small">Sube imágenes usando el formulario de abajo</p>
                                            </div>
                                        </div>
                                    `;
                                }
                            }, 500);
                        }
                    } else {
                        throw new Error(data.message || 'Error al eliminar');
                    }
                })
                .catch(error => {
                    showAlert('danger', error.message);
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                });
            }
        });
    });
});

// Función para abrir imagen en modal
function abrirModalImagen(src, titulo) {
    document.getElementById('modalImagenSrc').src = src;
    document.getElementById('modalImagenTitulo').textContent = titulo;
    const modal = new bootstrap.Modal(document.getElementById('modalImagen'));
    modal.show();
}

// Función para mostrar alertas
function showAlert(type, message) {
    // Eliminar alertas anteriores
    const existingAlerts = document.querySelectorAll('.alert-dynamic');
    existingAlerts.forEach(alert => alert.remove());
    
    // Crear nueva alerta
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show alert-dynamic mb-3`;
    alert.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'} me-2"></i>
            <div>${message}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insertar después del header del card
    const cardBody = document.querySelector('.card-body');
    if (cardBody) {
        cardBody.insertBefore(alert, cardBody.firstChild);
    }
    
    // Auto-eliminar después de 5 segundos
    setTimeout(() => {
        if (alert.parentNode) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}
</script>