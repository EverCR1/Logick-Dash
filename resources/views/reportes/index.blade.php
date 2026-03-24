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
.reporte-card {
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    overflow: hidden;
    position: relative;
}
.reporte-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    border-color: #bbf7d0 !important;
    background: #f0fdf4 !important;
}
 
.icon-wrapper {
    width: 72px; height: 72px;
    display: flex; align-items: center; justify-content: center;
    transition: transform 0.2s;
    border-radius: 50%;
}
.reporte-card:hover .icon-wrapper { transform: scale(1.1); }
 
.bg-primary-soft   { background: rgba(22,163,74,0.1); }
.bg-success-soft   { background: rgba(34,197,94,0.1); }
.bg-danger-soft    { background: rgba(239,68,68,0.1); }
.bg-warning-soft   { background: rgba(245,158,11,0.1); }
.bg-info-soft      { background: rgba(59,130,246,0.1); }
.bg-secondary-soft { background: rgba(100,116,139,0.1); }
.bg-cyan-soft      { background: rgba(6,182,212,0.1); }
 
.text-cyan { color: #0891b2 !important; }
.btn-cyan  { background: #0891b2; border-color: #0891b2; color: white; }
.btn-cyan:hover { background: #0e7490; border-color: #0e7490; color: white; }
 
/* Animación escalonada */
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}
.reporte-card { animation: fadeInUp 0.4s ease-out forwards; opacity: 0; }
.col-md-4:nth-child(1) .reporte-card { animation-delay: 0.05s; }
.col-md-4:nth-child(2) .reporte-card { animation-delay: 0.1s; }
.col-md-4:nth-child(3) .reporte-card { animation-delay: 0.15s; }
.col-md-4:nth-child(4) .reporte-card { animation-delay: 0.2s; }
.col-md-4:nth-child(5) .reporte-card { animation-delay: 0.25s; }
.col-md-4:nth-child(6) .reporte-card { animation-delay: 0.3s; }
.col-md-4:nth-child(7) .reporte-card { animation-delay: 0.35s; }
</style>
@endpush