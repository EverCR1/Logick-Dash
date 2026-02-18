{{-- resources/views/layouts/partials/modal-cambiar-password.blade.php --}}
<div class="modal fade" id="cambiarPasswordModal" tabindex="-1" aria-labelledby="cambiarPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="cambiarPasswordModalLabel">
                    <i class="fas fa-key me-2"></i>Cambiar Contraseña
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCambiarPassword" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas, números y símbolos.
                    </div>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Contraseña Actual</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength mt-2">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" id="passwordStrengthBar" style="width: 0%;"></div>
                            </div>
                            <small class="text-muted" id="passwordStrengthText">Ingresa una contraseña</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="passwordMatchFeedback"></div>
                    </div>

                    <div class="password-requirements mt-3">
                        <small class="text-muted d-block mb-2">Requisitos:</small>
                        <ul class="list-unstyled mb-0" id="passwordRequirements">
                            <li id="req-length" class="text-muted">
                                <i class="fas fa-circle me-2 fa-xs"></i> Mínimo 8 caracteres
                            </li>
                            <li id="req-uppercase" class="text-muted">
                                <i class="fas fa-circle me-2 fa-xs"></i> Al menos una mayúscula
                            </li>
                            <li id="req-lowercase" class="text-muted">
                                <i class="fas fa-circle me-2 fa-xs"></i> Al menos una minúscula
                            </li>
                            <li id="req-number" class="text-muted">
                                <i class="fas fa-circle me-2 fa-xs"></i> Al menos un número
                            </li>
                            <li id="req-symbol" class="text-muted">
                                <i class="fas fa-circle me-2 fa-xs"></i> Al menos un símbolo (!@#$%^&*)
                            </li>
                        </ul>
                    </div>

                    <div class="alert alert-danger d-none" id="errorAlert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <span id="errorMessage"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnCambiarPassword">
                        <i class="fas fa-save me-2"></i>Cambiar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos - Verificar que existan antes de usarlos
    const modal = document.getElementById('cambiarPasswordModal');
    const form = document.getElementById('formCambiarPassword');
    
    // Si el modal no existe en esta página, no continuar
    if (!modal || !form) {
        return;
    }

    // Elementos del formulario
    const newPassword = document.getElementById('new_password');
    const passwordConfirmation = document.getElementById('new_password_confirmation');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const submitBtn = document.getElementById('btnCambiarPassword');
    
    // Verificar que los elementos críticos existan
    if (!newPassword || !passwordConfirmation || !strengthBar || !strengthText) {
        console.warn('Algunos elementos del modal de cambio de contraseña no existen');
        return;
    }

    // Configuración de requisitos (solo cuando el modal está abierto)
    const requirements = {
        length: { 
            regex: /.{8,}/, 
            element: document.getElementById('req-length'),
            icon: null
        },
        uppercase: { 
            regex: /[A-Z]/, 
            element: document.getElementById('req-uppercase'),
            icon: null
        },
        lowercase: { 
            regex: /[a-z]/, 
            element: document.getElementById('req-lowercase'),
            icon: null
        },
        number: { 
            regex: /[0-9]/, 
            element: document.getElementById('req-number'),
            icon: null
        },
        symbol: { 
            regex: /[!@#$%^&*(),.?":{}|<>]/, 
            element: document.getElementById('req-symbol'),
            icon: null
        }
    };

    // Inicializar iconos de requisitos
    function initRequirementIcons() {
        for (const key in requirements) {
            const req = requirements[key];
            if (req.element) {
                req.icon = req.element.querySelector('i');
                // Resetear estado
                if (req.icon) {
                    req.icon.className = 'fas fa-circle me-2 text-muted fa-xs';
                }
                req.element.classList.add('text-muted');
                req.element.classList.remove('text-success');
            }
        }
    }

    // Toggle mostrar/ocultar contraseña
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;
            
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Validación de fortaleza de contraseña
    newPassword.addEventListener('input', function() {
        // Verificar que los elementos de requisitos existan
        let allElementsExist = true;
        for (const key in requirements) {
            if (!requirements[key].element) {
                allElementsExist = false;
                break;
            }
        }
        
        if (!allElementsExist) return;

        const password = this.value;
        let metCount = 0;

        // Verificar cada requisito
        for (const [key, req] of Object.entries(requirements)) {
            if (!req.element || !req.icon) continue;
            
            const isMet = req.regex.test(password);
            
            if (isMet) {
                req.icon.className = 'fas fa-check-circle me-2 text-success fa-xs';
                req.element.classList.remove('text-muted');
                req.element.classList.add('text-success');
                metCount++;
            } else {
                req.icon.className = 'fas fa-circle me-2 text-muted fa-xs';
                req.element.classList.add('text-muted');
                req.element.classList.remove('text-success');
            }
        }

        // Calcular fortaleza
        const strength = (metCount / 5) * 100;
        strengthBar.style.width = strength + '%';

        if (strength === 0) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Contraseña muy débil';
        } else if (strength <= 40) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Contraseña débil';
        } else if (strength <= 60) {
            strengthBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Contraseña moderada';
        } else if (strength <= 80) {
            strengthBar.className = 'progress-bar bg-info';
            strengthText.textContent = 'Contraseña fuerte';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Contraseña muy fuerte';
        }
    });

    // Validar que las contraseñas coincidan
    const passwordMatchFeedback = document.getElementById('passwordMatchFeedback');

    function checkPasswordMatch() {
        if (!passwordMatchFeedback) return true;
        
        if (newPassword.value !== passwordConfirmation.value) {
            passwordConfirmation.classList.add('is-invalid');
            passwordMatchFeedback.textContent = 'Las contraseñas no coinciden';
            return false;
        } else {
            passwordConfirmation.classList.remove('is-invalid');
            passwordMatchFeedback.textContent = '';
            return true;
        }
    }

    newPassword.addEventListener('keyup', checkPasswordMatch);
    passwordConfirmation.addEventListener('keyup', checkPasswordMatch);

    // Envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!checkPasswordMatch()) {
            return;
        }

        // Verificar que los elementos existan
        if (!errorAlert || !errorMessage || !submitBtn) return;
        
        // Ocultar errores previos
        errorAlert.classList.add('d-none');
        
        // Deshabilitar botón
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cambiando...';

        const formData = new FormData(this);

        fetch('{{ route("cambiar-password") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar modal
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
                
                if (data.redirect) {
                    // Si hay redirect, mostrar mensaje y redirigir
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Contraseña cambiada!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        alert(data.message);
                        window.location.href = data.redirect;
                    }
                } else {
                    // Mostrar mensaje de éxito
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    const mainContent = document.querySelector('.main-content');
                    if (mainContent) {
                        mainContent.insertAdjacentHTML('afterbegin', alertHtml);
                    }
                    
                    // Limpiar formulario
                    form.reset();
                    if (strengthBar) strengthBar.style.width = '0%';
                    if (strengthText) strengthText.textContent = 'Ingresa una contraseña';
                    
                    // Resetear requisitos
                    for (const key in requirements) {
                        const req = requirements[key];
                        if (req.element && req.icon) {
                            req.icon.className = 'fas fa-circle me-2 text-muted fa-xs';
                            req.element.classList.add('text-muted');
                            req.element.classList.remove('text-success');
                        }
                    }
                }
            } else {
                // Mostrar error
                if (errorMessage) {
                    errorMessage.textContent = data.message || 'Error al cambiar la contraseña';
                }
                if (errorAlert) {
                    errorAlert.classList.remove('d-none');
                }
                
                if (data.errors && errorMessage) {
                    // Si hay errores de validación específicos
                    const errorList = Object.values(data.errors).flat().join('<br>');
                    errorMessage.innerHTML = errorList;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (errorMessage) {
                errorMessage.textContent = 'Error de conexión. Intenta de nuevo.';
            }
            if (errorAlert) {
                errorAlert.classList.remove('d-none');
            }
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    });

    // Limpiar errores al abrir el modal
    modal.addEventListener('show.bs.modal', function() {
        // Inicializar iconos de requisitos
        initRequirementIcons();
        
        if (errorAlert) errorAlert.classList.add('d-none');
        if (form) form.reset();
        
        // Resetear barra de fortaleza
        if (strengthBar) strengthBar.style.width = '0%';
        if (strengthText) strengthText.textContent = 'Ingresa una contraseña';
        
        // Resetear validación de confirmación
        if (passwordConfirmation) {
            passwordConfirmation.classList.remove('is-invalid');
        }
        if (passwordMatchFeedback) {
            passwordMatchFeedback.textContent = '';
        }
    });
});
</script>
@endpush