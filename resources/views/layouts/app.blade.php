<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@hasSection('title')@yield('title') - @endif{{ config('app.name') }}</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <style>
        :root {
            --primary-color: #2E7D32;
            --primary-light: #4CAF50;
            --primary-dark: #1B5E20;
            --secondary-color: #0288D1;
            --secondary-light: #03A9F4;
            --secondary-dark: #01579B;
            --light-color: #f8f9fa;
            --dark-color: #212121;
            --gray-medium: #6c757d;
            --sidebar-width: 250px;
            --sidebar-collapsed: 70px;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            overflow-x: hidden;
        }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            color: white;
            position: fixed;
            transition: all var(--transition-speed) ease;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        #sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        .sidebar-header {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
            overflow: hidden;
        }

        .logo i {
            font-size: 1.8rem;
            width: 30px;
            text-align: center;
            flex-shrink: 0;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            transition: opacity var(--transition-speed);
            white-space: nowrap;
        }

        #sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 4px;
            transition: background-color 0.2s;
            flex-shrink: 0;
        }

        .toggle-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 20px 0;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .menu-item {
            list-style: none;
        }

        .menu-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            white-space: nowrap;
        }

        .menu-link:hover, .menu-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--secondary-light);
        }

        .menu-icon {
            width: 24px;
            text-align: center;
            margin-right: 15px;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .menu-text {
            transition: opacity var(--transition-speed);
        }

        #sidebar.collapsed .menu-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        /* Sidebar Footer - CORREGIDO */
        .sidebar-footer {
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .copyright-text {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            text-align: center;
            padding: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: all var(--transition-speed);
            line-height: 1.4;
        }

        #sidebar.collapsed .copyright-text {
            font-size: 0.6rem;
            padding: 5px 2px;
        }

        /* Main content */
        #content {
            margin-left: var(--sidebar-width);
            transition: margin-left var(--transition-speed);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #content.expanded {
            margin-left: var(--sidebar-collapsed);
        }

        /* Navbar superior */
        .top-navbar {
            background-color: white;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        /* Menú de usuario mejorado */
        .user-menu {
            position: relative;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .user-avatar:hover {
            transform: scale(1.05);
            border-color: var(--secondary-light);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .user-dropdown {
            position: absolute;
            top: 55px;
            right: 0;
            width: 280px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            display: none;
            z-index: 1000;
            overflow: hidden;
            animation: slideDown 0.3s ease;
        }

        .user-dropdown.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 20px;
            text-align: center;
        }

        .dropdown-avatar-large {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0 auto 10px;
            border: 3px solid white;
        }

        .dropdown-user-name {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .dropdown-user-email {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .dropdown-body {
            padding: 15px;
        }

        .user-info-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .user-info-item i {
            width: 30px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .user-info-item .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.75rem;
            color: var(--gray-medium);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--dark-color);
        }

        .dropdown-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            background-color: #f8f9fa;
        }

        .change-password-btn {
            background: none;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 10px;
            border-radius: 8px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .change-password-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Botón de logout en dropdown */
        .logout-btn-dropdown {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 8px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .logout-btn-dropdown:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }
        
        .logout-btn-dropdown i {
            font-size: 1.1rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        .breadcrumb-item a {
            color: var(--secondary-color);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 20px;
            font-weight: 600;
        }

        .card-header:first-child {
            border-radius: 10px 10px 0 0;
        }

        /* Botones personalizados */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-secondary:hover {
            background-color: var(--secondary-dark);
            border-color: var(--secondary-dark);
        }

        .btn-outline-light:hover {
            color: var(--primary-dark);
        }

        /* Badges */
        .badge-activo {
            background-color: #d4edda;
            color: #155724;
            padding: 0.5em 0.75em;
            border-radius: 20px;
        }

        .badge-inactivo {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.5em 0.75em;
            border-radius: 20px;
        }

        /* Alertas */
        .alert {
            border-radius: 8px;
            border: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Tablas */
        .table th {
            font-weight: 600;
            color: var(--dark-color);
            border-top: none;
        }

        .table td {
            vertical-align: middle;
        }

        /* Scrollbar personalizada */
        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            #sidebar.mobile-show {
                margin-left: 0;
                width: var(--sidebar-width);
            }
            
            #sidebar.mobile-show .logo-text,
            #sidebar.mobile-show .menu-text,
            #sidebar.mobile-show .copyright-text {
                display: block !important;
                opacity: 1 !important;
            }
            
            #content {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: block !important;
            }
        }
    </style>
    
    @stack('styles')
</head>
@if(session('api_token'))
<meta name="api-token" content="{{ session('api_token') }}">
@endif
<body>
    <!-- Sidebar -->
    <div id="sidebar" class="{{ session('sidebar_collapsed', false) ? 'collapsed' : '' }}">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" style="height: 40px; width: auto;">
                <span class="logo-text">{{ config('app.name') }}</span>
            </a>
            <button class="toggle-btn" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <ul class="sidebar-menu">
            <!-- Dashboard -->
            <li class="menu-item">
                <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt menu-icon"></i>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            
            <!-- Usuarios (solo admin) -->
            @if($userRole == 'administrador')
            <li class="menu-item">
                <a href="{{ route('usuarios.index') }}" class="menu-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="fas fa-users menu-icon"></i>
                    <span class="menu-text">Usuarios</span>
                </a>
            </li>
            @endif

            <!-- Clientes (admin y vendedor) -->
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <li class="menu-item">
                <a href="{{ route('clientes.index') }}" class="menu-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tie menu-icon"></i>
                    <span class="menu-text">Clientes</span>
                </a>
            </li>
            @endif
            
            <!-- Proveedores (admin y vendedor) -->
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <li class="menu-item">
                <a href="{{ route('proveedores.index') }}" class="menu-link {{ request()->routeIs('proveedores.*') ? 'active' : '' }}">
                    <i class="fas fa-truck menu-icon"></i>
                    <span class="menu-text">Proveedores</span>
                </a>
            </li>
            @endif
            
            <!-- Categorías (admin y vendedor) -->
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <li class="menu-item">
                <a href="{{ route('categorias.index') }}" class="menu-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                    <i class="fas fa-tags menu-icon"></i>
                    <span class="menu-text">Categorías</span>
                </a>
            </li>
            @endif
            
            <!-- Productos (admin y vendedor) -->
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <li class="menu-item">
                <a href="{{ route('productos.index') }}" class="menu-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                    <i class="fas fa-box menu-icon"></i>
                    <span class="menu-text">Productos</span>
                </a>
            </li>
            @endif
            
            <!-- Servicios (admin y vendedor) -->
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <li class="menu-item">
                <a href="{{ route('servicios.index') }}" class="menu-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}">
                    <i class="fas fa-concierge-bell menu-icon"></i>
                    <span class="menu-text">Servicios</span>
                </a>
            </li>
            @endif

            <!-- Créditos (admin y vendedor) -->
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <li class="menu-item">
                <a href="{{ route('creditos.index') }}" class="menu-link {{ request()->routeIs('creditos.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card menu-icon"></i>
                    <span class="menu-text">Créditos</span>
                </a>
            </li>
            @endif
            
            <!-- Ventas (admin y vendedor) -->
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <li class="menu-item">
                <a href="{{ route('ventas.index') }}" class="menu-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart menu-icon"></i>
                    <span class="menu-text">Ventas</span>
                </a>
            </li>
            @endif
            
            <!-- Reportes (admin y analista) -->
            @if(in_array($userRole, ['administrador', 'analista']))
            <li class="menu-item">
                <a href="{{ route('reportes.index') }}" class="menu-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar menu-icon"></i>
                    <span class="menu-text">Reportes</span>
                </a>
            </li>
            @endif
            
            <!-- Auditoría (solo admin) -->
            @if($userRole == 'administrador')
            <li class="menu-item">
                <a href="{{ route('auditoria.index') }}" class="menu-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list menu-icon"></i>
                    <span class="menu-text">Auditoría</span>
                </a>
            </li>
            @endif
        </ul>
        
        <!-- Sidebar Footer con COPYRIGHT -->
        <div class="sidebar-footer">
            <div class="copyright-text">
                {{ config('app.name') }} © {{ date('Y') }}. 
                <!-- Todos los derechos reservados. -->
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div id="content" class="{{ session('sidebar_collapsed', false) ? 'expanded' : '' }}">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary btn-sm me-3 mobile-toggle d-md-none">
                    <i class="fas fa-bars"></i>
                </button>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            
            <!-- Menú de usuario mejorado -->
            <div class="user-menu">
                <div class="user-avatar" id="userAvatar">
                    {{ substr($user['nombres'] ?? 'U', 0, 1) }}{{ substr($user['apellidos'] ?? 'S', 0, 1) }}
                </div>
                
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-avatar-large">
                            {{ substr($user['nombres'] ?? 'U', 0, 1) }}{{ substr($user['apellidos'] ?? 'S', 0, 1) }}
                        </div>
                        <div class="dropdown-user-name">{{ $user['nombre_completo'] ?? 'Usuario' }}</div>
                        <div class="dropdown-user-email">{{ $user['email'] ?? '' }}</div>
                    </div>
                    
                    <div class="dropdown-body">
                        <div class="user-info-item">
                            <i class="fas fa-user"></i>
                            <div class="info-content">
                                <div class="info-label">Rol</div>
                                <div class="info-value">{{ ucfirst($userRole ?? 'Sin rol') }}</div>
                            </div>
                        </div>
                        
                        <div class="user-info-item">
                            <i class="fas fa-envelope"></i>
                            <div class="info-content">
                                <div class="info-label">Email</div>
                                <div class="info-value">{{ $user['email'] ?? 'No disponible' }}</div>
                            </div>
                        </div>
                        
                        <div class="user-info-item">
                            <i class="fas fa-phone"></i>
                            <div class="info-content">
                                <div class="info-label">Teléfono</div>
                                <div class="info-value">{{ $user['telefono'] ?? 'No disponible' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dropdown-footer">
                        <!-- Botón Cambiar Contraseña -->
                        <button class="change-password-btn" data-bs-toggle="modal" data-bs-target="#cambiarPasswordModal">
                            <i class="fas fa-key"></i>
                            Cambiar Contraseña
                        </button>
                        
                        <!-- Botón Cerrar Sesión -->
                        <form action="{{ route('logout') }}" method="POST" class="w-100">
                            @csrf
                            <button type="submit" class="logout-btn-dropdown">
                                <i class="fas fa-sign-out-alt"></i>
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Alertas -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            <!-- Contenido de la página -->
            @yield('content')
        </main>
    </div>
    
    <!-- Modal Cambiar Contraseña -->
    @include('layouts.partials.modal-cambiar-password')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (opcional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Obtener elementos
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.querySelector('.mobile-toggle');
    const userAvatar = document.getElementById('userAvatar');
    const userDropdown = document.getElementById('userDropdown');
    
    // Cargar estado del sidebar desde localStorage
    document.addEventListener('DOMContentLoaded', function() {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            content.classList.add('expanded');
        }
    });
    
    // Toggle sidebar
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
            
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
            
            // Guardar en localStorage
            localStorage.setItem('sidebarCollapsed', !isCurrentlyCollapsed);
        });
    }
    
    // Mobile toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.toggle('mobile-show');
        });
    }
    
    // Toggle menú de usuario
    if (userAvatar) {
        userAvatar.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
    }
    
    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function(event) {
        if (userDropdown && !userAvatar.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.remove('show');
        }
    });
    
    // Cerrar sidebar en móvil al hacer clic fuera
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768 && 
            sidebar && 
            !sidebar.contains(event.target) && 
            mobileToggle && 
            !mobileToggle.contains(event.target) &&
            sidebar.classList.contains('mobile-show')) {
            sidebar.classList.remove('mobile-show');
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            const bsAlert = bootstrap.Alert.getInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            } else {
                // Si no hay instancia, crear una y cerrar
                const newAlert = new bootstrap.Alert(alert);
                newAlert.close();
            }
        });
    }, 5000);
    
    // Prevenir que el formulario de logout tenga problemas
    document.querySelectorAll('form[action="{{ route('logout') }}"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            console.log('Cerrando sesión...');
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert para mensajes de éxito
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: '#28a745',
                color: '#fff',
                iconColor: '#fff',
                showCloseButton: true,
                customClass: {
                    popup: 'rounded-3 shadow-lg'
                }
            });
        @endif

        // SweetAlert para mensajes de error
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: '#dc3545',
                color: '#fff',
                iconColor: '#fff',
                showCloseButton: true,
                customClass: {
                    popup: 'rounded-3 shadow-lg'
                }
            });
        @endif

        // SweetAlert para mensajes de warning
        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: '{{ session('warning') }}',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: '#ffc107',
                color: '#000',
                iconColor: '#000',
                showCloseButton: true,
                customClass: {
                    popup: 'rounded-3 shadow-lg'
                }
            });
        @endif

        // SweetAlert para mensajes de info
        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'Información',
                text: '{{ session('info') }}',
                timer: 3000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true,
                background: '#17a2b8',
                color: '#fff',
                iconColor: '#fff',
                showCloseButton: true,
                customClass: {
                    popup: 'rounded-3 shadow-lg'
                }
            });
        @endif

        // SweetAlert para errores de validación múltiples
        @if($errors->any())
            let errorMessages = '';
            @foreach($errors->all() as $error)
                errorMessages += '• {{ $error }}\n';
            @endforeach
            
            Swal.fire({
                icon: 'error',
                title: 'Errores de Validación',
                html: errorMessages.replace(/\n/g, '<br>'),
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Entendido',
                background: '#fff',
                color: '#000',
                customClass: {
                    popup: 'rounded-3 shadow-lg'
                }
            });
        @endif
    });
    </script>
    
    @stack('scripts')
</body>
</html>