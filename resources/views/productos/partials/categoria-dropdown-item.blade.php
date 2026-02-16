@php
    $indent = str_repeat('&nbsp;', $nivel * 4);
    $prefix = $nivel > 0 ? '└─ ' : '';
@endphp

<div class="form-check mb-2 categoria-item nivel-{{ $nivel }}" data-categoria-id="{{ $categoria['id'] }}">
    <input class="form-check-input filter-categoria" type="radio" 
           name="categoriaFilter" id="categoria_{{ $categoria['id'] }}" 
           value="{{ $categoria['id'] }}">
    <label class="form-check-label" for="categoria_{{ $categoria['id'] }}">
        {!! $indent !!}{!! $prefix !!}{{ $categoria['nombre'] }}
        @if(isset($categoria['productos_count']) && $categoria['productos_count'] > 0)
            <small class="text-muted">({{ $categoria['productos_count'] }})</small>
        @endif
    </label>
</div>

@if(!empty($categoria['children']))
    @foreach($categoria['children'] as $child)
        @include('productos.partials.categoria-dropdown-item', [
            'categoria' => $child,
            'nivel' => $nivel + 1
        ])
    @endforeach
@endif