@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid px-0">

    {{-- ── HEADER ────────────────────────────────────────────────── --}}
    <div class="dash-header mb-5">
        <div class="dash-header-inner">
            <div>
                @php \Carbon\Carbon::setLocale('es'); @endphp
                <p class="dash-date">
                    <i class="fas fa-calendar-alt me-2"></i>
                    {{ \Carbon\Carbon::now()->timezone('America/Guatemala')->isoFormat('dddd, D [de] MMMM YYYY') }}
                    &nbsp;·&nbsp;
                    <i class="fas fa-clock me-1"></i>
                    {{ \Carbon\Carbon::now()->timezone('America/Guatemala')->format('h:i A') }}
                </p>
                <h1 class="dash-title">¡Hola, {{ $user['nombres'] ?? 'Usuario' }}! 👋</h1>
                <p class="dash-subtitle">Aquí tienes el resumen de hoy</p>
            </div>
            <div class="dash-actions">
                <button class="btn btn-outline-secondary btn-sm" id="refreshDashboard">
                    <i class="fas fa-rotate-right me-1"></i> Actualizar
                </button>
                <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Nueva venta
                </a>
            </div>
        </div>
    </div>

    {{-- ── STOCK ALERT ───────────────────────────────────────────── --}}
    @if($stats['total_alertas'] > 0)
    <div class="alert alert-warning alert-dismissible fade show mb-4" style="border-radius:10px;">
        <i class="fas fa-triangle-exclamation"></i>
        <div>
            <strong>{{ $stats['total_alertas'] }} producto(s) con stock bajo</strong>
            @if(count($stats['alertas_stock_bajo']) > 0)
            <div style="margin-top:6px; font-size:0.82rem; opacity:0.85;">
                @foreach(array_slice($stats['alertas_stock_bajo'], 0, 3) as $p)
                    <span style="margin-right:12px;">
                        <strong>{{ $p['nombre'] ?? '' }}</strong> — stock: {{ $p['stock'] ?? 0 }} / mín: {{ $p['stock_minimo'] ?? 0 }}
                    </span>
                @endforeach
                @if($stats['total_alertas'] > 3)
                    <span style="opacity:0.6;">+{{ $stats['total_alertas'] - 3 }} más</span>
                @endif
            </div>
            @endif
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── VENTAS KPIs ───────────────────────────────────────────── --}}
    <div class="kpi-grid mb-5">
        <div class="kpi-card kpi-primary">
            <div class="kpi-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="kpi-body">
                <span class="kpi-label">Ventas Hoy</span>
                <span class="kpi-value">Q {{ number_format($stats['ventas_hoy'], 2) }}</span>
                <span class="kpi-sub">{{ $stats['total_ventas_hoy'] }} transacciones</span>
            </div>
        </div>
        <div class="kpi-card kpi-blue">
            <div class="kpi-icon"><i class="fas fa-calendar-week"></i></div>
            <div class="kpi-body">
                <span class="kpi-label">Esta Semana</span>
                <span class="kpi-value">Q {{ number_format($stats['ventas_semana'], 2) }}</span>
                <span class="kpi-sub">{{ $stats['total_ventas_semana'] }} transacciones</span>
            </div>
        </div>
        <div class="kpi-card kpi-violet">
            <div class="kpi-icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="kpi-body">
                <span class="kpi-label">Este Mes</span>
                <span class="kpi-value">Q {{ number_format($stats['ventas_mes'], 2) }}</span>
                <span class="kpi-sub">{{ $stats['total_ventas_mes'] }} transacciones</span>
            </div>
        </div>
        <div class="kpi-card kpi-amber">
            <div class="kpi-icon"><i class="fas fa-chart-bar"></i></div>
            <div class="kpi-body">
                <span class="kpi-label">Promedio / Venta</span>
                <span class="kpi-value">Q {{ number_format($stats['promedio_venta'], 2) }}</span>
                <span class="kpi-sub">Máx: Q {{ number_format($stats['venta_maxima'], 2) }}</span>
            </div>
        </div>
    </div>

    {{-- ── ADMIN STATS ───────────────────────────────────────────── --}}
    @if($userRole == 'administrador')
    <div class="stat-grid mb-5">
        <a href="{{ route('clientes.index') }}" class="stat-card">
            <div class="stat-icon" style="background:#fef3c7; color:#d97706;"><i class="fas fa-users"></i></div>
            <div class="stat-body">
                <span class="stat-value">{{ number_format($stats['total_clientes']) }}</span>
                <span class="stat-label">Clientes</span>
                <span class="stat-sub">{{ $stats['clientes_activos'] }} activos · {{ $stats['clientes_nuevos_mes'] }} nuevos</span>
            </div>
            <i class="fas fa-arrow-right stat-arrow"></i>
        </a>
        <a href="{{ route('productos.index') }}" class="stat-card">
            <div class="stat-icon" style="background:#ede9fe; color:#7c3aed;"><i class="fas fa-box"></i></div>
            <div class="stat-body">
                <span class="stat-value">{{ number_format($stats['total_productos']) }}</span>
                <span class="stat-label">Productos</span>
                <span class="stat-sub">{{ $stats['productos_stock_bajo'] }} bajo stock · {{ $stats['productos_agotados'] }} agotados</span>
            </div>
            <i class="fas fa-arrow-right stat-arrow"></i>
        </a>
        <a href="{{ route('servicios.index') }}" class="stat-card">
            <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;"><i class="fas fa-concierge-bell"></i></div>
            <div class="stat-body">
                <span class="stat-value">{{ number_format($stats['total_servicios']) }}</span>
                <span class="stat-label">Servicios</span>
                <span class="stat-sub">{{ $stats['servicios_activos'] }} activos</span>
            </div>
            <i class="fas fa-arrow-right stat-arrow"></i>
        </a>
        <a href="{{ route('creditos.index') }}" class="stat-card">
            <div class="stat-icon" style="background:#fce7f3; color:#be185d;"><i class="fas fa-credit-card"></i></div>
            <div class="stat-body">
                <span class="stat-value">{{ number_format($stats['creditos_activos']) }}</span>
                <span class="stat-label">Créditos activos</span>
                <span class="stat-sub">Q {{ number_format($stats['capital_pendiente'], 2) }} pendiente</span>
            </div>
            <i class="fas fa-arrow-right stat-arrow"></i>
        </a>
        <a href="{{ route('proveedores.index') }}" class="stat-card">
            <div class="stat-icon" style="background:#d1fae5; color:#059669;"><i class="fas fa-truck"></i></div>
            <div class="stat-body">
                <span class="stat-value">{{ number_format($stats['total_proveedores']) }}</span>
                <span class="stat-label">Proveedores</span>
                <span class="stat-sub">{{ $stats['proveedores_activos'] }} activos</span>
            </div>
            <i class="fas fa-arrow-right stat-arrow"></i>
        </a>
        <a href="{{ route('categorias.index') }}" class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-tags"></i></div>
            <div class="stat-body">
                <span class="stat-value">{{ number_format($stats['total_categorias']) }}</span>
                <span class="stat-label">Categorías</span>
                <span class="stat-sub">Nivel 0: {{ $stats['categorias_por_nivel']['nivel_0'] ?? 0 }}</span>
            </div>
            <i class="fas fa-arrow-right stat-arrow"></i>
        </a>
        <a href="{{ route('usuarios.index') }}" class="stat-card">
            <div class="stat-icon" style="background:#fff7ed; color:#ea580c;"><i class="fas fa-user-shield"></i></div>
            <div class="stat-body">
                <span class="stat-value">{{ number_format($stats['total_usuarios']) }}</span>
                <span class="stat-label">Usuarios</span>
                <span class="stat-sub">Admin: {{ $stats['usuarios_por_rol']['administrador'] }} · Vendedor: {{ $stats['usuarios_por_rol']['vendedor'] }}</span>
            </div>
            <i class="fas fa-arrow-right stat-arrow"></i>
        </a>
        <div class="stat-card stat-card--highlight">
            <div class="stat-icon" style="background:rgba(34,197,94,0.15); color:#22c55e;"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-body">
                <span class="stat-value" style="color:#22c55e;">Q {{ number_format($stats['valor_inventario'], 2) }}</span>
                <span class="stat-label">Valor Inventario</span>
                <span class="stat-sub">Recuperado: Q {{ number_format($stats['total_recuperado'], 2) }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- ── BOTTOM ROW ────────────────────────────────────────────── --}}
    <div class="row g-4">

        {{-- Productos más vendidos --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="section-dot" style="background:#22c55e;"></span>
                        Productos más vendidos
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(!empty($stats['top_productos']))
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['top_productos'] as $i => $producto)
                                <tr>
                                    <td>
                                        <span class="rank-badge rank-{{ $i+1 <= 3 ? $i+1 : 'rest' }}">{{ $i+1 }}</span>
                                    </td>
                                    <td style="font-weight:500; font-size:0.83rem;">{{ $producto['producto']['nombre'] ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $producto['total_vendido'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-end" style="font-family:'DM Mono', monospace; font-size:0.82rem; font-weight:500;">
                                        Q {{ number_format($producto['total_ingreso'] ?? 0, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-box-open"></i>
                        <p>Sin ventas registradas</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Top clientes --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="section-dot" style="background:#3b82f6;"></span>
                        Top clientes
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(!empty($stats['top_clientes']))
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th class="text-center">Compras</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['top_clientes'] as $i => $cliente)
                                <tr>
                                    <td>
                                        <span class="rank-badge rank-{{ $i+1 <= 3 ? $i+1 : 'rest' }}">{{ $i+1 }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight:500; font-size:0.83rem;">{{ $cliente['nombre'] ?? 'N/A' }}</div>
                                        @if($cliente['nit'] ?? false)
                                        <div style="font-size:0.73rem; color:var(--text-tertiary);">NIT: {{ $cliente['nit'] }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $cliente['ventas_count'] ?? 0 }}</span>
                                    </td>
                                    <td class="text-end" style="font-family:'DM Mono', monospace; font-size:0.82rem; font-weight:500;">
                                        Q {{ number_format($cliente['total_comprado'] ?? 0, 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                        <p>Sin datos de clientes</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Métodos de pago + Estado créditos --}}
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="section-dot" style="background:#f59e0b;"></span>
                        Métodos de pago
                    </div>
                </div>
                <div class="card-body" style="padding:14px 20px;">
                    @if(!empty($stats['metodos_pago']))
                        @foreach($stats['metodos_pago'] as $metodo)
                        <div class="metodo-row">
                            <div class="metodo-icon metodo-{{ $metodo['metodo_pago'] }}">
                                @switch($metodo['metodo_pago'])
                                    @case('efectivo') <i class="fas fa-money-bill-wave"></i> @break
                                    @case('tarjeta')  <i class="fas fa-credit-card"></i> @break
                                    @case('transferencia') <i class="fas fa-building-columns"></i> @break
                                    @case('mixto')    <i class="fas fa-layer-group"></i> @break
                                @endswitch
                            </div>
                            <div class="metodo-info">
                                <span class="metodo-name">{{ ucfirst($metodo['metodo_pago']) }}</span>
                                <span class="metodo-count">{{ $metodo['cantidad'] }} transacciones</span>
                            </div>
                            <span class="metodo-total">Q {{ number_format($metodo['total'], 2) }}</span>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state" style="padding:20px 0;">
                            <i class="fas fa-credit-card"></i>
                            <p>Sin pagos registrados</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="section-dot" style="background:#a855f7;"></span>
                        Estado de créditos
                    </div>
                </div>
                <div class="card-body" style="padding:14px 20px;">
                    <div class="credito-row">
                        <span class="credito-dot" style="background:#ef4444;"></span>
                        <span class="credito-label">Activos</span>
                        <span class="credito-value">{{ number_format($stats['creditos_activos']) }}</span>
                    </div>
                    <div class="credito-row">
                        <span class="credito-dot" style="background:#f59e0b;"></span>
                        <span class="credito-label">Abonados</span>
                        <span class="credito-value">{{ number_format($stats['creditos_abonados']) }}</span>
                    </div>
                    <div class="credito-row">
                        <span class="credito-dot" style="background:#22c55e;"></span>
                        <span class="credito-label">Pagados</span>
                        <span class="credito-value">{{ number_format($stats['creditos_pagados']) }}</span>
                    </div>
                    <div style="height:1px; background:var(--border-subtle); margin:12px 0;"></div>
                    <div class="credito-row">
                        <span class="credito-label" style="color:var(--text-secondary);">Capital pendiente</span>
                        <span class="credito-value" style="color:#ef4444; font-family:'DM Mono',monospace;">Q {{ number_format($stats['capital_pendiente'], 2) }}</span>
                    </div>
                    <div class="credito-row">
                        <span class="credito-label" style="color:var(--text-secondary);">Recuperado</span>
                        <span class="credito-value" style="color:#22c55e; font-family:'DM Mono',monospace;">Q {{ number_format($stats['total_recuperado'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
/* ── HEADER ─────────────────────────────────────────────── */
.dash-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
    border-radius: 14px;
    padding: 28px 32px;
    position: relative;
    overflow: hidden;
}
.dash-header::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(34,197,94,0.15) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.dash-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.dash-date {
    font-size: 0.78rem;
    color: rgba(255,255,255,0.4);
    margin-bottom: 8px;
    text-transform: capitalize;
    letter-spacing: 0.02em;
}
.dash-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: white;
    letter-spacing: -0.03em;
    line-height: 1.2;
    margin-bottom: 4px;
}
.dash-subtitle {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.4);
    margin: 0;
}
.dash-actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

/* ── KPI GRID ───────────────────────────────────────────── */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}
@media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { .kpi-grid { grid-template-columns: 1fr; } }

.kpi-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    border-left: 3px solid transparent;
}
.kpi-card:hover { 
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
    background: #f0fdf4;
    border-color: #bbf7d0;
}
.kpi-primary { border-left-color: #22c55e; }
.kpi-blue    { border-left-color: #3b82f6; }
.kpi-violet  { border-left-color: #8b5cf6; }
.kpi-amber   { border-left-color: #f59e0b; }

.kpi-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}
.kpi-primary .kpi-icon { background: rgba(34,197,94,0.1);  color: #22c55e; }
.kpi-blue    .kpi-icon { background: rgba(59,130,246,0.1);  color: #3b82f6; }
.kpi-violet  .kpi-icon { background: rgba(139,92,246,0.1);  color: #8b5cf6; }
.kpi-amber   .kpi-icon { background: rgba(245,158,11,0.1);  color: #f59e0b; }

.kpi-body { display: flex; flex-direction: column; gap: 2px; }
.kpi-label { font-size: 0.75rem; font-weight: 600; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.05em; }
.kpi-value { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); letter-spacing: -0.02em; font-family: 'DM Mono', monospace; }
.kpi-sub   { font-size: 0.75rem; color: var(--text-tertiary); }

/* ── STAT GRID ──────────────────────────────────────────── */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}
@media (max-width: 1200px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 640px)  { .stat-grid { grid-template-columns: 1fr; } }

.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: var(--transition);
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}
.stat-card:hover {
    border-color: var(--accent);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
    color: inherit;
}
.stat-card--highlight {
    background: rgba(34,197,94,0.04);
    border-color: rgba(34,197,94,0.2);
}

.stat-icon {
    width: 38px; height: 38px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.stat-body { flex: 1; display: flex; flex-direction: column; min-width: 0; }
.stat-value { font-size: 1.15rem; font-weight: 700; letter-spacing: -0.02em; font-family: 'DM Mono', monospace; line-height: 1.2; }
.stat-label { font-size: 0.78rem; font-weight: 600; color: var(--text-secondary); }
.stat-sub   { font-size: 0.72rem; color: var(--text-tertiary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.stat-arrow { color: var(--text-tertiary); font-size: 0.7rem; flex-shrink: 0; opacity: 0; transition: opacity 0.15s; }
.stat-card:hover .stat-arrow { opacity: 1; color: var(--accent); }

/* ── RANK BADGES ────────────────────────────────────────── */
.rank-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 22px; height: 22px;
    border-radius: 6px;
    font-size: 0.72rem; font-weight: 700;
}
.rank-1 { background: #fef3c7; color: #d97706; }
.rank-2 { background: #f1f5f9; color: #475569; }
.rank-3 { background: #fef3c7; color: #92400e; }
.rank-rest { background: var(--surface-3); color: var(--text-tertiary); }

/* ── SECTION DOT ────────────────────────────────────────── */
.section-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

/* ── METODO ROW ─────────────────────────────────────────── */
.metodo-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-subtle);
}
.metodo-row:last-child { border-bottom: none; }

.metodo-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; flex-shrink: 0;
}
.metodo-efectivo     { background: #d1fae5; color: #059669; }
.metodo-tarjeta      { background: #dbeafe; color: #2563eb; }
.metodo-transferencia{ background: #e0e7ff; color: #4338ca; }
.metodo-mixto        { background: #fef3c7; color: #d97706; }

.metodo-info { flex: 1; display: flex; flex-direction: column; }
.metodo-name  { font-size: 0.83rem; font-weight: 600; color: var(--text-primary); }
.metodo-count { font-size: 0.72rem; color: var(--text-tertiary); }
.metodo-total { font-size: 0.83rem; font-weight: 700; font-family: 'DM Mono', monospace; color: var(--text-primary); }

/* ── CREDITO ROW ────────────────────────────────────────── */
.credito-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 0;
}
.credito-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}
.credito-label { flex: 1; font-size: 0.82rem; color: var(--text-secondary); }
.credito-value { font-size: 0.83rem; font-weight: 700; }

/* ── EMPTY STATE ────────────────────────────────────────── */
.empty-state {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 40px 20px;
    color: var(--text-tertiary);
    text-align: center;
}
.empty-state i { font-size: 2rem; margin-bottom: 10px; opacity: 0.4; }
.empty-state p { font-size: 0.85rem; margin: 0; }

/* ── TAMAÑO DE FUENTE DASHBOARD ─────────────────────────── */
.kpi-label  { font-size: 0.9375rem; }  /* 15px */
.kpi-value  { font-size: 1.375rem; }   /* 22px */
.kpi-sub    { font-size: 0.9375rem; }  /* 15px */

.stat-value { font-size: 1.25rem; }    /* 20px */
.stat-label { font-size: 0.9375rem; }  /* 15px */
.stat-sub   { font-size: 0.9375rem; }  /* 15px */

.table th   { font-size: 0.9375rem; }  /* 15px */
.table td   { font-size: 0.9375rem; }  /* 15px */

.rank-badge { font-size: 0.9375rem; width: 26px; height: 26px; } /* 15px */

.metodo-name  { font-size: 0.9375rem; } /* 15px */
.metodo-count { font-size: 0.9375rem; } /* 15px */
.metodo-total { font-size: 0.9375rem; } /* 15px */

.credito-label { font-size: 0.9375rem; } /* 15px */
.credito-value { font-size: 0.9375rem; } /* 15px */

.card-header   { font-size: 1rem; }     /* 16px */
</style>
@endsection

@push('scripts')
<script>
document.getElementById('refreshDashboard')?.addEventListener('click', function() {
    const btn = this;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Actualizando...';
    btn.disabled = true;
    fetch('{{ route("dashboard.refresh") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(d => { if (d.success) location.reload(); })
    .catch(() => {})
    .finally(() => {
        btn.innerHTML = '<i class="fas fa-rotate-right me-1"></i> Actualizar';
        btn.disabled = false;
    });
});
</script>
@endpush