@extends('layouts.app')

@section('title', 'Nueva Categoría')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
    <li class="breadcrumb-item active">Nueva Categoría</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fas fa-plus-circle text-primary"></i>
                    <h5 class="card-title mb-0">Nueva Categoría</h5>
                </div>

                {{-- enctype necesario para subir imagen --}}
                <form action="{{ route('categorias.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Corrige los siguientes errores:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- Nombre --}}
                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-semibold">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input type="text"
                                       class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre" name="nombre"
                                       value="{{ old('nombre') }}"
                                       placeholder="Ej: Electrónica, Ropa, Hogar..."
                                       required autofocus>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                      id="descripcion" name="descripcion"
                                      rows="3"
                                      placeholder="Describe el propósito de esta categoría...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Opcional.</small>
                        </div>

                        {{-- Categoría padre --}}
                        <div class="mb-3">
                            <label for="parent_id" class="form-label fw-semibold">Categoría padre</label>
                            <select class="form-select @error('parent_id') is-invalid @enderror"
                                    id="parent_id" name="parent_id">
                                <option value="">— Ninguna (categoría raíz) —</option>
                                @foreach($categoriasPadre as $pid => $pnombre)
                                    <option value="{{ $pid }}" {{ old('parent_id') == $pid ? 'selected' : '' }}>
                                        {{ $pnombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Selecciona una categoría padre si es una subcategoría.</small>
                        </div>

                        {{-- Imagen --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Imagen</label>

                            <div class="img-upload-area" id="uploadArea">
                                <div class="img-upload-placeholder" id="uploadPlaceholder">
                                    <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                    <p class="mb-1 text-muted">Arrastra una imagen o haz clic para seleccionar</p>
                                    <small class="text-muted">JPEG, PNG, WEBP — máx. 5MB</small>
                                </div>
                                <div class="img-preview-wrap" id="previewWrap" style="display:none;">
                                    <img id="imgPreview" src="#" alt="Vista previa" class="img-preview">
                                    <button type="button" class="img-remove-btn" id="removeImg" title="Quitar imagen">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <input type="file" id="imagen" name="imagen"
                                       accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                       class="img-input @error('imagen') is-invalid @enderror">
                            </div>

                            @error('imagen')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>{{-- card-body --}}

                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancelar
                        </a>
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-warning" id="btnReset">
                                <i class="fas fa-undo me-2"></i>Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Categoría
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.img-upload-area {
    position: relative;
    border: 2px dashed var(--border);
    border-radius: var(--radius-md);
    background: var(--surface-2);
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    overflow: hidden;
    min-height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.img-upload-area:hover,
.img-upload-area.dragover {
    border-color: var(--accent);
    background: #f0fdf4;
}
.img-upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px;
    pointer-events: none;
    text-align: center;
}
.img-input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
}
.img-preview-wrap {
    position: relative;
    width: 100%;
    padding: 12px;
    display: flex;
    justify-content: center;
}
.img-preview {
    max-height: 200px;
    max-width: 100%;
    border-radius: var(--radius-md);
    object-fit: contain;
    box-shadow: var(--shadow-sm);
}
.img-remove-btn {
    position: absolute;
    top: 8px; right: 8px;
    width: 28px; height: 28px;
    border-radius: 50%;
    background: rgba(239,68,68,0.9);
    border: none;
    color: white;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem;
    transition: background 0.15s;
}
.img-remove-btn:hover { background: #dc2626; }
</style>
@endpush

@push('scripts')
<script>
(function() {
    const area       = document.getElementById('uploadArea');
    const input      = document.getElementById('imagen');
    const placeholder= document.getElementById('uploadPlaceholder');
    const previewWrap= document.getElementById('previewWrap');
    const preview    = document.getElementById('imgPreview');
    const removeBtn  = document.getElementById('removeImg');
    const resetBtn   = document.getElementById('btnReset');

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            placeholder.style.display = 'none';
            previewWrap.style.display = '';
        };
        reader.readAsDataURL(file);
    }

    function clearPreview() {
        preview.src = '#';
        input.value = '';
        placeholder.style.display = '';
        previewWrap.style.display = 'none';
    }

    input.addEventListener('change', () => {
        if (input.files[0]) showPreview(input.files[0]);
    });

    removeBtn.addEventListener('click', e => {
        e.stopPropagation();
        clearPreview();
    });

    resetBtn.addEventListener('click', () => clearPreview());

    // Drag & drop
    area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
    area.addEventListener('dragleave', () => area.classList.remove('dragover'));
    area.addEventListener('drop', e => {
        e.preventDefault();
        area.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file) {
            // Asignar al input nativo
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showPreview(file);
        }
    });
})();
</script>
@endpush