{{--
    resources/views/categorias/partials/tree.blade.php

    CORRECCIÓN: la API puede serializar la relación como 'children_recursive'
    (snake_case) o 'childrenRecursive' (camelCase) según la versión de Laravel.
    Usamos el operador ?? para manejar ambos casos en cada nivel.
--}}

@foreach($categorias as $categoria)

@php
    $nivel     = $nivel ?? 0;
    $colorIdx  = $nivel % 6;

    // ── Hijos: manejar snake_case y camelCase ────────────────────────
    $hijos = $categoria['children_recursive']
          ?? $categoria['childrenRecursive']
          ?? $categoria['children']
          ?? [];

    $tieneHijos = !empty($hijos);
    $collapseId = 'cat-' . $categoria['id'];

    // ── Imagen: la API devuelve el objeto imagen o null ──────────────
    $imagen  = $categoria['imagen'] ?? null;
    $imgUrl  = $imagen['url_thumb'] ?? ($imagen['url_medium'] ?? ($imagen['url'] ?? null));
    $imgFull = $imagen['url_medium'] ?? ($imagen['url'] ?? $imgUrl);
@endphp

<li class="level-{{ $nivel }}">
    <div class="category-item level-color-{{ $colorIdx }}"
         data-estado="{{ $categoria['estado'] ?? 'activo' }}"
         data-id="{{ $categoria['id'] }}">

        {{-- ── Lado izquierdo ───────────────────────────────────── --}}
        <div class="d-flex align-items-center gap-2 flex-1 min-w-0">

            {{-- Toggle colapsar --}}
            @if($tieneHijos)
                <button type="button"
                        class="toggle-btn"
                        data-bs-toggle="collapse"
                        data-target="#{{ $collapseId }}"
                        title="Expandir/Colapsar">
                    <i class="fas fa-chevron-right"></i>
                </button>
            @else
                <div style="width:26px; flex-shrink:0;"></div>
            @endif

            {{-- Imagen miniatura con click para modal --}}
            @if($imgUrl)
                <img src="{{ $imgUrl }}"
                     alt="{{ $categoria['nombre'] }}"
                     class="cat-thumb"
                     data-full="{{ $imgFull }}"
                     data-nombre="{{ $categoria['nombre'] }}"
                     onclick="abrirModalImagen(this)"
                     title="Ver imagen de {{ $categoria['nombre'] }}">
            @else
                <div class="cat-thumb-placeholder" title="Sin imagen">
                    <i class="fas fa-tag text-muted"></i>
                </div>
            @endif

            {{-- Info --}}
            <div class="min-w-0">
                <span class="category-name">{{ $categoria['nombre'] }}</span>

                @if(!empty($categoria['descripcion']))
                    <span class="category-desc d-block text-truncate" style="max-width:320px;">
                        {{ Str::limit($categoria['descripcion'], 60) }}
                    </span>
                @endif

                <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                    <span class="badge {{ ($categoria['estado'] ?? 'activo') === 'activo' ? 'bg-success' : 'bg-secondary' }} badge-estado">
                        {{ ucfirst($categoria['estado'] ?? 'activo') }}
                    </span>

                    @if($tieneHijos)
                        <span class="badge bg-light text-dark badge-estado">
                            {{ count($hijos) }} {{ count($hijos) === 1 ? 'sub' : 'subs' }}
                        </span>
                    @endif

                    @if($imgUrl)
                        <span class="badge bg-light text-muted badge-estado" title="Tiene imagen">
                            <i class="fas fa-image"></i>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Acciones ─────────────────────────────────────────── --}}
        @if(in_array(auth()->user()->rol ?? 'vendedor', ['administrador', 'vendedor']))
        <div class="category-actions">

            {{-- Ver --}}
            <a href="{{ route('categorias.show', $categoria['id']) }}"
               class="btn btn-sm" title="Ver detalles">
                <i class="fas fa-eye text-info"></i>
            </a>

            {{-- Editar --}}
            <a href="{{ route('categorias.edit', $categoria['id']) }}"
               class="btn btn-sm" title="Editar">
                <i class="fas fa-edit text-warning"></i>
            </a>

            {{-- Cambiar estado --}}
            <form action="{{ route('categorias.change-status', $categoria['id']) }}"
                  method="POST" class="d-inline"
                  onsubmit="return confirm('¿Cambiar estado de \'{{ addslashes($categoria['nombre']) }}\'?')">
                @csrf
                <input type="hidden" name="estado"
                       value="{{ ($categoria['estado'] ?? 'activo') === 'activo' ? 'inactivo' : 'activo' }}">
                <button type="submit"
                        class="btn btn-sm"
                        title="{{ ($categoria['estado'] ?? 'activo') === 'activo' ? 'Desactivar' : 'Activar' }}">
                    @if(($categoria['estado'] ?? 'activo') === 'activo')
                        <i class="fas fa-toggle-on text-success"></i>
                    @else
                        <i class="fas fa-toggle-off text-secondary"></i>
                    @endif
                </button>
            </form>

            {{-- Agregar subcategoría --}}
            <a href="{{ route('categorias.create') }}?parent_id={{ $categoria['id'] }}"
               class="btn btn-sm" title="Agregar subcategoría">
                <i class="fas fa-plus text-primary"></i>
            </a>

            {{-- Eliminar --}}
            <form action="{{ route('categorias.destroy', $categoria['id']) }}"
                  method="POST" class="d-inline"
                  onsubmit="return confirm('¿Eliminar \'{{ addslashes($categoria['nombre']) }}\'?\nEsta acción no se puede deshacer.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm" title="Eliminar">
                    <i class="fas fa-trash text-danger"></i>
                </button>
            </form>

        </div>
        @endif

    </div>{{-- .category-item --}}

    {{-- ── Hijos recursivos ─────────────────────────────────────────── --}}
    @if($tieneHijos)
        <div id="{{ $collapseId }}" class="collapse show">
            <ul class="list-unstyled">
                @include('categorias.partials.tree', [
                    'categorias' => $hijos,
                    'nivel'      => $nivel + 1
                ])
            </ul>
        </div>
    @endif

</li>

@endforeach