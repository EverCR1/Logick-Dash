@extends('layouts.app')

@section('title', 'Editar Categoría - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
    <li class="breadcrumb-item active">Editar Categoría</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-warning card-outline">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0 text-dark">
                        <i class="fas fa-edit me-2"></i>Editando: <strong>{{ $categoria['nombre'] }}</strong>
                    </h5>
                </div>
                
                <form action="{{ route('categorias.update', $categoria['id']) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Por favor corrige los siguientes errores:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="mb-4">
                            <label for="nombre" class="form-label fw-bold">
                                Nombre de la categoría <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre', $categoria['nombre']) }}" 
                                       required>
                            </div>
                            @error('nombre')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="descripcion" class="form-label fw-bold">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" 
                                      name="descripcion" 
                                      rows="4">{{ old('descripcion', $categoria['descripcion']) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="parent_id" class="form-label fw-bold">Categoría padre</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror" 
                                    id="parent_id" 
                                    name="parent_id">
                                <option value="">-- Ninguna (categoría raíz) --</option>
                                @foreach($categoriasPadre as $id => $nombre)
                                    @if($id != $categoria['id']) <!-- Evitar seleccionarse a sí misma -->
                                        <option value="{{ $id }}" {{ old('parent_id', $categoria['parent_id']) == $id ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Selecciona una categoría padre si esta es una subcategoría.</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="estado" class="form-label fw-bold">Estado</label>
                            <select class="form-select @error('estado') is-invalid @enderror" 
                                    id="estado" 
                                    name="estado">
                                <option value="activo" {{ old('estado', $categoria['estado']) == 'activo' ? 'selected' : '' }}>
                                    <i class="fas fa-check-circle text-success"></i> Activo
                                </option>
                                <option value="inactivo" {{ old('estado', $categoria['estado']) == 'inactivo' ? 'selected' : '' }}>
                                    <i class="fas fa-times-circle text-danger"></i> Inactivo
                                </option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                            <div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-2"></i>Actualizar Categoría
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Información adicional -->
            <div class="card mt-3">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información adicional
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-calendar-plus text-primary me-2"></i>
                                <strong>Creada:</strong> 
                                {{ isset($categoria['created_at']) ? \Carbon\Carbon::parse($categoria['created_at'])->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <i class="fas fa-rotate text-info me-2"></i>
                                <strong>Última actualización:</strong> 
                                {{ isset($categoria['updated_at']) ? \Carbon\Carbon::parse($categoria['updated_at'])->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection