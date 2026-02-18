@extends('layouts.app')

@section('title', 'Editar Proveedor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('proveedores.index') }}">Proveedores</a></li>
    <li class="breadcrumb-item active">Editar Proveedor</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-warning card-outline">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="fas fa-edit me-2"></i>Editando Proveedor: <strong>{{ $proveedor['nombre'] }}</strong>
                    </h5>
                    <div class="card-tools">
                        <span class="badge bg-dark text-white">ID: {{ $proveedor['id'] }}</span>
                    </div>
                </div>
                <form action="{{ route('proveedores.update', $proveedor['id']) }}" method="POST" class="form-horizontal">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                                    <div>
                                        <strong>¡Por favor corrige los siguientes errores!</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3 border-warning">
                                    <div class="card-header bg-warning bg-opacity-25">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-building me-2"></i>Información Básica
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <label for="nombre" class="form-label fw-bold">
                                                Nombre del Proveedor <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                                       id="nombre" name="nombre" value="{{ old('nombre', $proveedor['nombre']) }}" 
                                                       placeholder="Ej: Distribuidora XYZ" required>
                                            </div>
                                            @error('nombre')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="descripcion" class="form-label fw-bold">Descripción</label>
                                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                                      id="descripcion" name="descripcion" rows="4" 
                                                      placeholder="Breve descripción del proveedor...">{{ old('descripcion', $proveedor['descripcion']) }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Máximo 500 caracteres</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-3 border-warning">
                                    <div class="card-header bg-warning bg-opacity-25">
                                        <h6 class="card-title mb-0">
                                            <i class="fas fa-address-card me-2"></i>Información de Contacto
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                       id="email" name="email" value="{{ old('email', $proveedor['email']) }}" 
                                                       placeholder="ejemplo@correo.com">
                                            </div>
                                            @error('email')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="telefono" class="form-label fw-bold">Teléfono</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="text" class="form-control @error('telefono') is-invalid @enderror" 
                                                       id="telefono" name="telefono" value="{{ old('telefono', $proveedor['telefono']) }}" 
                                                       placeholder="Ej: 1234-5678">
                                            </div>
                                            @error('telefono')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="direccion" class="form-label fw-bold">Dirección</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                <input type="text" class="form-control @error('direccion') is-invalid @enderror" 
                                                       id="direccion" name="direccion" value="{{ old('direccion', $proveedor['direccion']) }}" 
                                                       placeholder="Dirección completa">
                                            </div>
                                            @error('direccion')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('proveedores.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-2"></i>Actualizar Proveedor
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection