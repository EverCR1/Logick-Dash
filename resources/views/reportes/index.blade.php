@extends('layouts.app')

@section('title', 'Reportes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Reportes</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Módulo de Reportes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Resumen General --}}
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 reporte-card">
                                <div class="card-body text-center">
                                    <div class="icon-wrapper bg-primary-soft rounded-circle mx-auto mb-3">
                                        <i class="fas fa-chart-pie fa-3x text-primary"></i>
                                    </div>
                                    <h5>Resumen General</h5>
                                    <p class="text-muted">Estadísticas generales del negocio</p>
                                    <a href="{{ route('reportes.resumen') }}" class="btn btn-primary btn-hover">
                                        <i class="fas fa-eye me-2"></i>Ver resumen
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Reporte de Ventas --}}
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 reporte-card">
                                <div class="card-body text-center">
                                    <div class="icon-wrapper bg-success-soft rounded-circle mx-auto mb-3">
                                        <i class="fas fa-shopping-cart fa-3x text-success"></i>
                                    </div>
                                    <h5>Reporte de Ventas</h5>
                                    <p class="text-muted">Ventas por período, cliente, vendedor</p>
                                    <a href="{{ route('reportes.ventas') }}" class="btn btn-success btn-hover">
                                        <i class="fas fa-eye me-2"></i>Ver ventas
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Productos Más Vendidos --}}
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 reporte-card">
                                <div class="card-body text-center">
                                    <div class="icon-wrapper bg-danger-soft rounded-circle mx-auto mb-3">
                                        <i class="fas fa-chart-bar fa-3x text-danger"></i>
                                    </div>
                                    <h5>Productos Más Vendidos</h5>
                                    <p class="text-muted">Ranking de productos con mayor rotación</p>
                                    <a href="{{ route('reportes.productos-mas-vendidos') }}" class="btn btn-danger btn-hover">
                                        <i class="fas fa-eye me-2"></i>Ver ranking
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Reporte de Inventario --}}
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 reporte-card">
                                <div class="card-body text-center">
                                    <div class="icon-wrapper bg-warning-soft rounded-circle mx-auto mb-3">
                                        <i class="fas fa-boxes fa-3x text-warning"></i>
                                    </div>
                                    <h5>Reporte de Inventario</h5>
                                    <p class="text-muted">Productos, stock bajo, valor inventario</p>
                                    <a href="{{ route('reportes.inventario') }}" class="btn btn-warning btn-hover">
                                        <i class="fas fa-eye me-2"></i>Ver inventario
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Top Clientes --}}
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 reporte-card">
                                <div class="card-body text-center">
                                    <div class="icon-wrapper bg-info-soft rounded-circle mx-auto mb-3">
                                        <i class="fas fa-trophy fa-3x text-info"></i>
                                    </div>
                                    <h5>Top Clientes</h5>
                                    <p class="text-muted">Clientes que más compran</p>
                                    <a href="{{ route('reportes.top-clientes') }}" class="btn btn-info btn-hover">
                                        <i class="fas fa-eye me-2"></i>Ver top clientes
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Rendimiento Vendedores --}}
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 reporte-card">
                                <div class="card-body text-center">
                                    <div class="icon-wrapper bg-secondary-soft rounded-circle mx-auto mb-3">
                                        <i class="fas fa-user-tie fa-3x text-secondary"></i>
                                    </div>
                                    <h5>Rendimiento Vendedores</h5>
                                    <p class="text-muted">Ventas por vendedor</p>
                                    <a href="{{ route('reportes.rendimiento-vendedores') }}" class="btn btn-secondary btn-hover">
                                        <i class="fas fa-eye me-2"></i>Ver rendimiento
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Ventas Últimos 30 Días --}}
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 reporte-card">
                                <div class="card-body text-center">
                                    <div class="icon-wrapper bg-cyan-soft rounded-circle mx-auto mb-3">
                                        <i class="fas fa-calendar-alt fa-3x text-cyan"></i>
                                    </div>
                                    <h5>Ventas 30 Días</h5>
                                    <p class="text-muted">Gráfico de ventas de los últimos 30 días</p>
                                    <a href="{{ route('reportes.ventas-30-dias') }}" class="btn btn-cyan btn-hover">
                                        <i class="fas fa-eye me-2"></i>Ver gráfico
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Estilos para las cards de reportes */
.reporte-card {
    transition: all 0.3s ease-in-out;
    border: 1px solid rgba(0,0,0,0.08);
    overflow: hidden;
}

.reporte-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: transparent;
}

/* Wrapper para íconos */
.icon-wrapper {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.reporte-card:hover .icon-wrapper {
    transform: scale(1.1);
}

/* Fondos suaves para íconos */
.bg-primary-soft {
    background-color: rgba(13, 110, 253, 0.1);
}

.bg-success-soft {
    background-color: rgba(25, 135, 84, 0.1);
}

.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.1);
}

.bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.1);
}

.bg-info-soft {
    background-color: rgba(13, 202, 240, 0.1);
}

.bg-secondary-soft {
    background-color: rgba(108, 117, 125, 0.1);
}

/* Efecto para botones */
.btn-hover {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-hover:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.btn-hover:active {
    transform: scale(0.95);
}

/* Animación de entrada para las cards */
.reporte-card {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Retrasos en la animación para cada card */
.reporte-card:nth-child(1) { animation-delay: 0.1s; }
.reporte-card:nth-child(2) { animation-delay: 0.2s; }
.reporte-card:nth-child(3) { animation-delay: 0.3s; }
.reporte-card:nth-child(4) { animation-delay: 0.4s; }
.reporte-card:nth-child(5) { animation-delay: 0.5s; }
.reporte-card:nth-child(6) { animation-delay: 0.6s; }

/* Efecto de brillo al hacer hover */
.reporte-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.bg-cyan-soft {
    background-color: rgba(13, 202, 240, 0.1);
}
.text-cyan {
    color: #0dcaf0 !important;
}
.btn-cyan {
    background-color: #0dcaf0;
    border-color: #0dcaf0;
    color: white;
}
.btn-cyan:hover {
    background-color: #0bb7d9;
    border-color: #0bb7d9;
    color: white;
}

.reporte-card:hover::before {
    left: 100%;
}

/* Estilos responsive */
@media (max-width: 768px) {
    .icon-wrapper {
        width: 60px;
        height: 60px;
    }
    
    .icon-wrapper i {
        font-size: 2rem !important;
    }
    
    .reporte-card {
        margin-bottom: 1rem;
    }
}
</style>
@endpush