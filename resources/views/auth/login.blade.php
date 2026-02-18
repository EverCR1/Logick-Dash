@extends('layouts.auth')

@section('title', 'Iniciar Sesión - Logickem')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <!-- Header con gradiente (verde pastel) -->
                    <div class="bg-gradient-primary p-5 text-center">
                        <div class="logo-container mb-4">
                            <img src="{{ asset('images/logo.png') }}" alt="LOGICKEM" class="img-fluid logo-img">
                        </div>
                        <h2 class="fw-bold mb-1" style="font-size: 2rem; color: #1e3a2e;">¡Bienvenido!</h2>
                        <p class="mb-0" style="font-size: 1.1rem; color: #2c4a3a;">Sistema de Gestión Empresarial</p>
                    </div>
                    
                    <!-- Formulario -->
                    <div class="p-4 p-lg-5">
                        <form method="POST" action="{{ route('login') }}" id="loginForm">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold text-secondary">
                                    <i class="fas fa-envelope me-2" style="color: #A8E6A0;"></i>Email o Usuario
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('login') is-invalid @enderror" 
                                       id="email" 
                                       name="email"
                                       value="{{ old('email') }}"
                                       placeholder="ejemplo@correo.com"
                                       required
                                       autofocus>
                                @error('login')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold text-secondary">
                                    <i class="fas fa-lock me-2" style="color: #A8E6A0;"></i>Contraseña
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control form-control-lg @error('login') is-invalid @enderror" 
                                           id="password" 
                                           name="password"
                                           placeholder="••••••••"
                                           required>
                                    <button class="btn btn-outline-secondary border" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('login')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label text-secondary" for="remember">
                                        Recordar sesión
                                    </label>
                                </div>
                                
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small" style="color: #7BCF7A;">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>
                            
                            <div class="d-grid gap-3">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill" id="submitBtn">
                                    <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-4">
                <p class="text-muted small mb-0">
                    &copy; {{ date('Y') }} Logickem. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #A8E6A0;      /* Verde pastel principal */
        --primary-dark: #7BCF7A;       /* Verde pastel más oscuro */
        --primary-light: #D4F5D2;       /* Verde pastel más claro */
        --secondary-color: #0288D1;
    }
    
    .min-vh-100 {
        min-height: 100vh;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #A8E6A0, #7BCF7A);
    }
    
    .logo-container {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        width: 140px;
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        backdrop-filter: blur(8px);
        border: 3px solid rgba(255, 255, 255, 0.25);
        padding: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .logo-img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }
    
    .form-control-lg {
        padding: 0.9rem 1.2rem;
        font-size: 1rem;
        border-radius: 12px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
    }
    
    .form-control-lg:focus {
        border-color: #A8E6A0;
        box-shadow: 0 0 0 0.2rem rgba(168, 230, 160, 0.25);
    }
    
    .input-group .btn {
        border: 2px solid #e9ecef;
        border-radius: 0 12px 12px 0;
        padding: 0 1.2rem;
    }
    
    .input-group .btn:hover {
        background-color: #f8f9fa;
        border-color: #A8E6A0;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #A8E6A0, #7BCF7A);
        border: none;
        padding: 0.9rem 1.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s;
        color: #2c3e50;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(168, 230, 160, 0.4);
        background: linear-gradient(135deg, #B8F0B0, #8CDF8A);
    }
    
    .btn-primary:disabled {
        background: linear-gradient(135deg, #c0c0c0, #a0a0a0);
        transform: none;
        color: white;
    }
    
    .form-check-input:checked {
        background-color: #A8E6A0;
        border-color: #A8E6A0;
    }
    
    .form-check-input:focus {
        border-color: #A8E6A0;
        box-shadow: 0 0 0 0.2rem rgba(168, 230, 160, 0.25);
    }
    
    .invalid-feedback {
        font-size: 0.85rem;
        margin-top: 0.4rem;
        color: #dc3545;
    }
    
    .text-white-50 {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    
    /* Animación de carga */
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .fa-spinner {
        animation: spin 1s linear infinite;
    }
    
    /* Responsive */
    @media (max-width: 576px) {
        .card-body {
            padding: 0;
        }
        
        .p-4.p-lg-5 {
            padding: 1.5rem !important;
        }
        
        .logo-container {
            width: 120px;
            height: 120px;
        }
        
        .bg-gradient-primary {
            padding: 2rem !important;
        }
        
        h2.fw-bold {
            font-size: 1.6rem !important;
        }
    }
    
    /* Estilos adicionales para el verde pastel */
    a.text-decoration-none:hover {
        color: #A8E6A0 !important;
        text-decoration: underline !important;
    }
    
    .text-secondary {
        color: #495057 !important;
    }
    
    .card {
        border: none;
        transition: transform 0.3s;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
</style>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword')?.addEventListener('click', function() {
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
    document.getElementById('email')?.focus();
    
    // Interceptar envío del formulario para mostrar loading
    document.querySelector('form')?.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Iniciando sesión...';
        submitBtn.disabled = true;
        
        // Si hay error, restaurar botón después de 5 segundos
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        }, 5000);
    });
    
    // Auto-ocultar alertas después de 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    });
</script>
@endsection