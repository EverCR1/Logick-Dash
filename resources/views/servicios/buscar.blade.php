@extends('layouts.app')

@section('title', 'Buscar Servicios - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('servicios.index') }}">Servicios</a></li>
    <li class="breadcrumb-item active">Buscar</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-search me-2"></i>Buscar Servicios
            </h5>
        </div>
        <div class="card-body">
            <!-- Formulario de búsqueda -->
            <form action="{{ route('servicios.buscar') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" name="q" 
                                   value="{{ $query ?? '' }}" 
                                   placeholder="Buscar por código, nombre o descripción...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="estado">
                            <option value="todos" {{ ($estado ?? 'todos') == 'todos' ? 'selected' : '' }}>Todos los estados</option>
                            <option value="activo" {{ ($estado ?? '') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ ($estado ?? '') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                    </div>
                </div>
            </form>

            @php
                // Extraer datos de manera segura
                $serviciosData = [];
                $serviciosLinks = [];
                $serviciosMeta = [];
                
                if (isset($servicios['data'])) {
                    $serviciosData = $servicios['data'];
                } elseif (isset($servicios) && is_array($servicios)) {
                    $serviciosData = $servicios;
                }
                
                if (isset($servicios['links']) && is_array($servicios['links'])) {
                    $serviciosLinks = $servicios['links'];
                }
                
                if (isset($servicios['meta']) && is_array($servicios['meta'])) {
                    $serviciosMeta = $servicios['meta'];
                }
            @endphp

            @if(!empty($query) || ($estado ?? 'todos') != 'todos')
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                @if(!empty($query) && ($estado ?? 'todos') != 'todos')
                    Buscando "{{ $query }}" y estado "{{ $estado == 'activo' ? 'activos' : 'inactivos' }}"
                @elseif(!empty($query))
                    Buscando "{{ $query }}"
                @elseif(($estado ?? 'todos') != 'todos')
                    Mostrando servicios {{ $estado == 'activo' ? 'activos' : 'inactivos' }}
                @endif
            </div>
            @endif

            @if(empty($serviciosData))
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No se encontraron servicios</h5>
                <p class="text-muted">Intenta con otros términos de búsqueda</p>
                @if(!empty($query) || ($estado ?? 'todos') != 'todos')
                <a href="{{ route('servicios.buscar') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Limpiar búsqueda
                </a>
                @endif
            </div>
            @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Servicio</th>
                            <th>Inversión</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($serviciosData as $servicio)
                        @php
                            $precio_final = $servicio['precio_oferta'] ?? $servicio['precio_venta'];
                            $inversion = $servicio['inversion_estimada'];
                            $margen = $inversion > 0 ? (($precio_final - $inversion) / $inversion) * 100 : 0;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $servicio['codigo'] ?? '' }}</strong>
                            </td>
                            <td>
                                <strong>{{ $servicio['nombre'] ?? '' }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($servicio['descripcion'] ?? '', 70) }}</small>
                            </td>
                            <td>
                                <small>Q{{ number_format($inversion, 2) }}</small>
                            </td>
                            <td>
                                @if(!empty($servicio['precio_oferta']))
                                    <div>
                                        <small class="text-decoration-line-through text-muted">
                                            Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}
                                        </small>
                                    </div>
                                    <div class="text-danger">
                                        <small><strong>Q{{ number_format($servicio['precio_oferta'], 2) }}</strong></small>
                                    </div>
                                @else
                                    <small><strong>Q{{ number_format($servicio['precio_venta'] ?? 0, 2) }}</strong></small>
                                @endif
                                <br>
                                <small class="text-muted">{{ number_format($margen, 1) }}% margen</small>
                            </td>
                            <td>
                                <span class="badge {{ ($servicio['estado'] ?? '') == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $servicio['estado'] ?? 'desconocido' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('servicios.show', $servicio['id'] ?? '#') }}" class="btn btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('servicios.edit', $servicio['id'] ?? '#') }}" class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if(!empty($serviciosLinks) && count($serviciosLinks) > 0)
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    @if(!empty($serviciosMeta))
                        Mostrando 
                        {{ $serviciosMeta['from'] ?? 1 }} - 
                        {{ $serviciosMeta['to'] ?? count($serviciosData) }} de 
                        {{ $serviciosMeta['total'] ?? count($serviciosData) }} resultados
                    @else
                        Mostrando {{ count($serviciosData) }} resultados
                    @endif
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0">
                        @foreach($serviciosLinks as $link)
                            @if(is_array($link))
                                <li class="page-item {{ $link['active'] ?? false ? 'active' : '' }} {{ empty($link['url']) ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ $link['url'] ?? '#' }}{{ !empty($query) ? '&q=' . urlencode($query) : '' }}{{ ($estado ?? 'todos') != 'todos' ? '&estado=' . $estado : '' }}">
                                        {!! $link['label'] ?? '' !!}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </nav>
            </div>
            @endif
            @endif

            <!-- Enlace para volver -->
            <div class="mt-4">
                <a href="{{ route('servicios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver a la lista completa
                </a>
            </div>
        </div>
    </div>
</div>
@endsection