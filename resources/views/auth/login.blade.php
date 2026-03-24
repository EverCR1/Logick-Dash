@extends('layouts.auth')

@section('title', config('app.name') . ' — Iniciar sesión')

@push('styles')
<style>
    body {
        background: #0f172a;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    /* ── FONDO ───────────────────────────────────────────────── */
    .bg-layer {
        position: fixed;
        inset: 0;
        z-index: 0;
        overflow: hidden;
        pointer-events: none;
    }
    .bg-layer::before {
        content: '';
        position: absolute;
        top: -25%; left: -15%;
        width: 650px; height: 650px;
        background: radial-gradient(circle, rgba(34,197,94,0.11) 0%, transparent 65%);
        border-radius: 50%;
    }
    .bg-layer::after {
        content: '';
        position: absolute;
        bottom: -20%; right: -10%;
        width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(59,130,246,0.07) 0%, transparent 65%);
        border-radius: 50%;
    }
    .bg-grid {
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
        background-size: 48px 48px;
    }

    /* ── WRAPPER ─────────────────────────────────────────────── */
    .login-wrap {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 440px;
        padding: 20px;
        animation: fadeUp 0.45s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── CARD ────────────────────────────────────────────────── */
    .login-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 22px;
        padding: 44px 40px 40px;
        backdrop-filter: blur(24px);
        box-shadow: 0 28px 56px rgba(0,0,0,0.45);
    }

    /* ── LOGO ────────────────────────────────────────────────── */
    .login-logo {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 36px;
    }
    .login-logo-ring {
        width: 180px;
        height: 180px;
        border-radius: 24px;
        background: linear-gradient(135deg, rgba(34,197,94,0.18), rgba(34,197,94,0.06));
        border: 1px solid rgba(34,197,94,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
        box-shadow: 0 8px 24px rgba(34,197,94,0.12);
    }
    .login-logo-ring img {
        width: 160px;
        height: 160px;
        object-fit: contain;
        filter: brightness(1.1) drop-shadow(0 2px 8px rgba(34,197,94,0.3));
    }
    .login-brand {
        font-size: 1.45rem;
        font-weight: 700;
        color: white;
        letter-spacing: -0.03em;
    }
    .login-tagline {
        font-size: 0.8rem;
        color: #94cbff;
        margin-top: 4px;
        letter-spacing: 0.02em;
    }

    /* ── DIVIDER ─────────────────────────────────────────────── */
    .login-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }
    .login-divider::before,
    .login-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(255,255,255,0.07);
    }
    .login-divider span {
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #f5eeee;
        white-space: nowrap;
    }

    /* ── CAMPOS ──────────────────────────────────────────────── */
    .field-wrap { margin-bottom: 15px; }

    .field-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        color: #fcf4f4;
        margin-bottom: 7px;
        letter-spacing: 0.03em;
    }

    .field-input-wrap { position: relative; }

    .field-icon {
        position: absolute;
        left: 13px; top: 50%;
        transform: translateY(-50%);
        color: rgba(255,255,255,0.18);
        font-size: 0.82rem;
        pointer-events: none;
        transition: color 0.15s;
    }
    .field-input-wrap:focus-within .field-icon { color: rgba(34,197,94,0.55); }

    .field-input {
        width: 100%;
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.09);
        border-radius: 10px;
        padding: 11px 14px 11px 37px;
        font-size: 0.9rem;
        font-family: 'DM Sans', sans-serif;
        color: white;
        outline: none;
        transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
    }
    .field-input::placeholder { color: rgba(255,255,255,0.18); }
    .field-input:focus {
        border-color: rgba(34,197,94,0.45);
        background: rgba(255,255,255,0.07);
        box-shadow: 0 0 0 3px rgba(34,197,94,0.07);
    }
    .field-input.is-invalid { border-color: rgba(239,68,68,0.5); }
    .field-input.has-eye    { padding-right: 38px; }

    .field-eye {
        position: absolute;
        right: 11px; top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: rgba(255,255,255,0.18);
        cursor: pointer;
        padding: 4px;
        font-size: 0.82rem;
        transition: color 0.15s;
        line-height: 1;
    }
    .field-eye:hover { color: rgba(255,255,255,0.45); }

    .field-invalid {
        font-size: 0.76rem;
        color: #f87171;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* ── OPCIONES ────────────────────────────────────────────── */
    .login-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 18px 0 22px;
    }

    .check-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        user-select: none;
    }
    .check-input {
        width: 15px; height: 15px;
        border-radius: 4px;
        border: 1px solid rgba(255,255,255,0.18);
        background: rgba(255,255,255,0.05);
        appearance: none;
        -webkit-appearance: none;
        cursor: pointer;
        transition: all 0.15s;
        flex-shrink: 0;
        position: relative;
    }
    .check-input:checked {
        background: #22c55e;
        border-color: #22c55e;
    }
    .check-input:checked::after {
        content: '';
        position: absolute;
        left: 4px; top: 1px;
        width: 5px; height: 8px;
        border: 2px solid white;
        border-top: none;
        border-left: none;
        transform: rotate(45deg);
    }
    .check-label {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.35);
    }

    .forgot-link {
        font-size: 0.8rem;
        color: rgba(34,197,94,0.65);
        text-decoration: none;
        transition: color 0.15s;
    }
    .forgot-link:hover { color: #22c55e; }

    /* ── BOTÓN SUBMIT ────────────────────────────────────────── */
    .btn-submit {
        width: 100%;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        letter-spacing: 0.02em;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(34,197,94,0.2);
    }
    .btn-submit:hover:not(:disabled) {
        background: linear-gradient(135deg, #16a34a, #15803d);
        box-shadow: 0 8px 24px rgba(34,197,94,0.3);
        transform: translateY(-1px);
    }
    .btn-submit:active:not(:disabled) { transform: translateY(0); }
    .btn-submit:disabled {
        background: rgba(255,255,255,0.08);
        color: rgba(255,255,255,0.25);
        cursor: not-allowed;
        box-shadow: none;
    }

    /* ── ALERTA ERROR ────────────────────────────────────────── */
    .login-alert {
        background: rgba(239,68,68,0.1);
        border: 1px solid rgba(239,68,68,0.22);
        border-radius: 10px;
        padding: 10px 14px;
        margin-bottom: 20px;
        font-size: 0.82rem;
        color: #f87171;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ── FOOTER ──────────────────────────────────────────────── */
    .login-footer {
        text-align: center;
        margin-top: 22px;
        font-size: 0.73rem;
        color: #ffffff;
        letter-spacing: 0.01em;
    }

    /* ── SPINNER ─────────────────────────────────────────────── */
    @keyframes spin { to { transform: rotate(360deg); } }
    .icon-spin { animation: spin 0.75s linear infinite; display: inline-block; }

    /* ── RESPONSIVE ──────────────────────────────────────────── */
    @media (max-width: 480px) {
        .login-card { padding: 32px 24px; }
        .login-logo-ring { width: 88px; height: 88px; }
        .login-logo-ring img { width: 60px; height: 60px; }
    }
</style>
@endpush

@section('content')

<div class="bg-layer">
    <div class="bg-grid"></div>
</div>

<div class="login-wrap">
    <div class="login-card">

        {{-- Logo --}}
        <div class="login-logo">
            <div class="login-logo-ring">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
            </div>
            <div class="login-brand">{{ config('app.name') }}</div>
            <div class="login-tagline">Sistema de Gestión Empresarial</div>
        </div>

        {{-- Divider --}}
        <div class="login-divider"><span>Acceso al sistema</span></div>

        {{-- Error de sesión --}}
        @if(session('error'))
        <div class="login-alert">
            <i class="fas fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="field-wrap">
                <label class="field-label" for="email">Email o usuario</label>
                <div class="field-input-wrap">
                    <input
                        type="text"
                        id="email"
                        name="email"
                        class="field-input {{ $errors->has('login') ? 'is-invalid' : '' }}"
                        value="{{ old('email') }}"
                        placeholder="usuario@empresa.com"
                        autocomplete="username"
                        autofocus
                        required>
                    <i class="fas fa-envelope field-icon"></i>
                </div>
                @error('login')
                    <div class="field-invalid">
                        <i class="fas fa-circle-exclamation"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Contraseña --}}
            <div class="field-wrap">
                <label class="field-label" for="password">Contraseña</label>
                <div class="field-input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="field-input has-eye {{ $errors->has('login') ? 'is-invalid' : '' }}"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required>
                    <i class="fas fa-lock field-icon"></i>
                    <button type="button" class="field-eye" id="togglePassword" tabindex="-1">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            {{-- Recordar / Olvidé --}}
            <div class="login-options">
                <label class="check-wrap">
                    <input type="checkbox" class="check-input" id="remember" name="remember">
                    <span class="check-label">Recordar sesión</span>
                </label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>
                @endif
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-arrow-right-to-bracket"></i>
                Iniciar sesión
            </button>

        </form>
    </div>

    <div class="login-footer">
        &copy; {{ date('Y') }} {{ config('app.name') }} · Todos los derechos reservados
    </div>
</div>

@endsection

@push('scripts')
<script>
// Toggle visibilidad contraseña
document.getElementById('togglePassword')?.addEventListener('click', function() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eyeIcon');
    const show  = input.type === 'password';
    input.type     = show ? 'text' : 'password';
    icon.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
});

// Estado de carga al enviar
document.getElementById('loginForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = '<i class="fas fa-circle-notch icon-spin"></i> Ingresando...';
    btn.disabled  = true;
    // Restaurar si tarda más de 6s (error de red, etc.)
    setTimeout(() => {
        btn.innerHTML = '<i class="fas fa-arrow-right-to-bracket"></i> Iniciar sesión';
        btn.disabled  = false;
    }, 6000);
});
</script>
@endpush