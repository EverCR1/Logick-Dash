@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Crear Usuario</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-user-plus me-2"></i>
                Crear Nuevo Usuario
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('usuarios.store') }}" method="POST" id="formUsuario">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nombres" class="form-label">
                            <i class="fas fa-user me-1 text-primary"></i>
                            Nombres <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('nombres') is-invalid @enderror" 
                               id="nombres" name="nombres" value="{{ old('nombres') }}" 
                               placeholder="Ingrese los nombres" required autofocus>
                        @error('nombres')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="apellidos" class="form-label">
                            <i class="fas fa-user me-1 text-primary"></i>
                            Apellidos <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('apellidos') is-invalid @enderror" 
                               id="apellidos" name="apellidos" value="{{ old('apellidos') }}" 
                               placeholder="Ingrese los apellidos" required>
                        @error('apellidos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1 text-primary"></i>
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" 
                               placeholder="correo@ejemplo.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">El email debe ser único en el sistema</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-at me-1 text-primary"></i>
                            Username <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username') }}" 
                               placeholder="nombre_usuario" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">El username debe ser único en el sistema</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1 text-primary"></i>
                            Contraseña <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" placeholder="••••••••" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Mínimo 8 caracteres, debe contener mayúsculas, minúsculas, números y símbolos
                        </small>
                        <div class="password-strength mt-2">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <small class="text-muted" id="passwordMessage"></small>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock me-1 text-primary"></i>
                            Confirmar Contraseña <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" 
                               placeholder="••••••••" required>
                        <small class="text-muted password-match-feedback"></small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="rol" class="form-label">
                            <i class="fas fa-user-tag me-1 text-primary"></i>
                            Rol <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('rol') is-invalid @enderror" id="rol" name="rol" required>
                            <option value="">Seleccionar rol</option>
                            <option value="administrador" {{ old('rol') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                            <option value="vendedor" {{ old('rol') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                            <option value="analista" {{ old('rol') == 'analista' ? 'selected' : '' }}>Analista</option>
                        </select>
                        @error('rol')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">El rol determinará los permisos del usuario</small>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="estado" class="form-label">
                            <i class="fas fa-toggle-on me-1 text-primary"></i>
                            Estado <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                            <option value="activo" {{ old('estado', 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="telefono" class="form-label">
                            <i class="fas fa-phone me-1 text-primary"></i>
                            Teléfono
                        </label>
                        <input type="text" class="form-control @error('telefono') is-invalid @enderror" 
                               id="telefono" name="telefono" value="{{ old('telefono') }}" 
                               placeholder="+502 1234 5678">
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label for="direccion" class="form-label">
                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                            Dirección
                        </label>
                        <textarea class="form-control @error('direccion') is-invalid @enderror" 
                                  id="direccion" name="direccion" rows="3" 
                                  placeholder="Ingrese la dirección completa">{{ old('direccion') }}</textarea>
                        @error('direccion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="fas fa-save me-2"></i>Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Mostrar/Ocultar contraseña
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Medidor de fortaleza de contraseña
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('passwordStrength');
    const message = document.getElementById('passwordMessage');
    
    let strength = 0;
    
    if (password.length >= 8) strength += 25;
    if (password.match(/[a-z]+/)) strength += 25;
    if (password.match(/[A-Z]+/)) strength += 25;
    if (password.match(/[0-9]+/)) strength += 15;
    if (password.match(/[$@#&!]+/)) strength += 10;
    
    strengthBar.style.width = strength + '%';
    
    if (strength <= 25) {
        strengthBar.className = 'progress-bar bg-danger';
        message.textContent = 'Contraseña débil';
    } else if (strength <= 50) {
        strengthBar.className = 'progress-bar bg-warning';
        message.textContent = 'Contraseña regular';
    } else if (strength <= 75) {
        strengthBar.className = 'progress-bar bg-info';
        message.textContent = 'Contraseña buena';
    } else {
        strengthBar.className = 'progress-bar bg-success';
        message.textContent = 'Contraseña fuerte';
    }
});

// Validar coincidencia de contraseñas
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirm = this.value;
    const feedback = document.querySelector('.password-match-feedback');
    
    if (confirm.length > 0) {
        if (password === confirm) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            feedback.innerHTML = '<span class="text-success"><i class="fas fa-check"></i> Las contraseñas coinciden</span>';
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
            feedback.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i> Las contraseñas no coinciden</span>';
        }
    } else {
        this.classList.remove('is-valid', 'is-invalid');
        feedback.innerHTML = '';
    }
});

// Validación antes de enviar el formulario
document.getElementById('formUsuario').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    
    if (password !== confirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
    }
});
</script>
@endpush