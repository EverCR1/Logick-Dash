<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LOGICK Dashboard')</title>
    
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
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
        }

        .logo img {
            width: 40px;
            height: 40px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            transition: opacity var(--transition-speed);
        }

        #sidebar.collapsed .logo-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
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
        }

        .toggle-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            padding: 20px 0;
            height: calc(100vh - 120px);
            overflow-y: auto;
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
        }

        .menu-text {
            transition: opacity var(--transition-speed);
        }

        #sidebar.collapsed .menu-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.8rem;
            color: var(--gray-medium);
            text-transform: capitalize;
        }

        /* Contenido principal */
        .main-content {
            flex: 1;
            padding: 25px;
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

        /* Badges */
        .badge-activo {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-inactivo {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Alertas */
        .alert {
            border-radius: 8px;
            border: none;
        }

        /* Tablas */
        .table th {
            font-weight: 600;
            color: var(--dark-color);
            border-top: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            #sidebar.mobile-show {
                margin-left: 0;
            }
            
            #content {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: block;
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
                <i class="fas fa-store menu-icon"></i>
                <span class="logo-text">LOGICK</span>
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

            @if(in_array($userRole, ['administrador', 'vendedor']))
            <li class="menu-item">
                <a href="{{ route('clientes.index') }}" class="menu-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                    <i class="fas fa-users menu-icon"></i>
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
            
            <!-- Categorías -->
            <li class="menu-item">
                <a href="{{ route('categorias.index') }}" class="menu-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                    <i class="fas fa-tags menu-icon"></i>
                    <span class="menu-text">Categorías</span>
                </a>
            </li>
            
            <!-- Productos -->
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <li class="menu-item">
                <a href="{{ route('productos.index') }}" class="menu-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                    <i class="fas fa-box menu-icon"></i>
                    <span class="menu-text">Productos</span>
                </a>
            </li>
            @endif
            
            <!-- Servicios -->
            <li class="menu-item">
                <a href="{{ route('servicios.index') }}" class="menu-link {{ request()->routeIs('servicios.*') ? 'active' : '' }}">
                    <i class="fas fa-concierge-bell menu-icon"></i>
                    <span class="menu-text">Servicios</span>
                </a>
            </li>

            <!-- Créditos -->
            <li class="menu-item">
                <a href="{{ route('creditos.index') }}" class="menu-link {{ request()->routeIs('creditos.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card menu-icon"></i>
                    <span class="menu-text">Créditos</span>
                </a>
            </li>
            
            <!-- Ventas (próximamente) -->
            <li class="menu-item">
                <a href="{{ route('ventas.index') }}" class="menu-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart menu-icon"></i>
                    <span class="menu-text">Ventas</span>
                </a>
            </li>
            
            <!-- Reportes (próximamente) -->
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-chart-bar menu-icon"></i>
                    <span class="menu-text">Reportes</span>
                </a>
            </li>
            
            <!-- Auditoría (próximamente) -->
            <li class="menu-item">
                <a href="#" class="menu-link">
                    <i class="fas fa-clipboard-list menu-icon"></i>
                    <span class="menu-text">Auditoría</span>
                </a>
            </li>
        </ul>
        
        <div class="sidebar-footer" style="padding: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
            <form action="{{ route('logout') }}" method="POST" class="d-grid">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    <span class="menu-text">Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div id="content" class="{{ session('sidebar_collapsed', false) ? 'expanded' : '' }}">
        <!-- Top Navbar -->
        <nav class="top-navbar">
            <div>
                <button class="btn btn-outline-secondary btn-sm mobile-toggle d-md-none">
                    <i class="fas fa-bars"></i>
                </button>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a></li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            
            <div class="user-info">
                <div class="user-details">
                    <span class="user-name">{{ $user['nombre_completo'] ?? 'Usuario' }}</span>
                    <span class="user-role">{{ $userRole ?? 'Sin rol' }}</span>
                </div>
                <div class="user-avatar">
                    {{ substr($user['nombres'] ?? 'U', 0, 1) }}{{ substr($user['apellidos'] ?? 'S', 0, 1) }}
                </div>
            </div>
        </nav>
        
        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Alertas -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
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
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (opcional) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
    // Obtener elementos
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileToggle = document.querySelector('.mobile-toggle');
    
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
        sidebarToggle.addEventListener('click', function() {
            const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
            
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
            
            // Guardar en localStorage (sin necesidad de llamada al servidor)
            localStorage.setItem('sidebarCollapsed', !isCurrentlyCollapsed);
        });
    }
    
    // Mobile toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-show');
        });
    }
    
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
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
    
    @stack('scripts')
</body>
</html>