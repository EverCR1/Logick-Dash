@extends('layouts.auth')

@section('title', 'Login - LOGICK')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <div class="logo-login mb-3">
                            <i class="fas fa-store fa-3x text-primary"></i>
                        </div>
                        <h2 class="fw-bold text-primary">LOGICK</h2>
                        <p class="text-muted">Sistema de Gestión de Productos y Ventas</p>
                    </div>
                    
                    <!-- Formulario -->
<form method="POST" action="{{ route('login') }}" id="loginForm">
    @csrf
    
    <div class="mb-3">
        <label for="email" class="form-label">Email o Username</label>
        <input type="text" 
               class="form-control @error('login') is-invalid @enderror" 
               id="email" 
               name="email"
               value="{{ old('email') }}"
               placeholder="Ingresa tu email o username"
               required>
        @error('login')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <div class="input-group">
            <input type="password" 
                   class="form-control @error('login') is-invalid @enderror" 
                   id="password" 
                   name="password"
                   placeholder="Ingresa tu contraseña"
                   required
                   autocomplete="current-password">
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="mb-3 form-check">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">Recordarme</label>
    </div>
    
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
        </button>
    </div>
</form>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted mb-0">Credenciales de prueba:</p>
                        <small class="text-muted">
                            Admin: admin@logick.com / password123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-vh-100 {
        min-height: 100vh;
    }
    
    .logo-login {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
    }
</style>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Auto-focus on email field
    document.getElementById('email').focus();
    
    // Debug: Mostrar errores de Laravel en consola
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar si hay errores de validación
        const errorElements = document.querySelectorAll('.is-invalid');
        if (errorElements.length > 0) {
            console.log('Errores de validación encontrados:');
            errorElements.forEach(element => {
                console.log(`Campo: ${element.name}, Valor: ${element.value}`);
            });
        }
        
        // Verificar mensajes flash de sesión
        const alertElements = document.querySelectorAll('.alert');
        alertElements.forEach(alert => {
            console.log('Alerta:', alert.textContent.trim());
        });
    });
    
    // Interceptar envío del formulario para debug
    document.querySelector('form').addEventListener('submit', function(e) {
        console.log('=== DEBUG FORMULARIO LOGIN ===');
        console.log('Form action:', this.action);
        console.log('Form method:', this.method);
        
        // Mostrar datos del formulario
        const formData = new FormData(this);
        for (let [key, value] of formData.entries()) {
            if (key !== 'password') {
                console.log(`${key}: ${value}`);
            } else {
                console.log(`${key}: [PROTEGIDO]`);
            }
        }
        
        // Mostrar CSRF token
        const csrfToken = document.querySelector('input[name="_token"]');
        if (csrfToken) {
            console.log('CSRF Token:', csrfToken.value.substring(0, 20) + '...');
        }
        
        console.log('=== FIN DEBUG ===');
        
        // Agregar indicador de carga
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Procesando...';
        submitBtn.disabled = true;
        
        // Restaurar después de 5 segundos si la página no recarga
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 5000);
    });
    
    // También mostrar cualquier error que aparezca dinámicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.classList && node.classList.contains('invalid-feedback')) {
                            console.log('Nuevo error detectado:', node.textContent);
                        }
                        if (node.classList && node.classList.contains('alert')) {
                            console.log('Nueva alerta:', node.textContent.trim());
                        }
                    }
                });
            }
        });
    });
    
    // Observar el body para detectar errores dinámicos
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
</script>
@endsection