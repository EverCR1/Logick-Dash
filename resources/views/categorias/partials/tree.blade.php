@foreach($categorias as $categoria)
@php
    $collapseId = 'collapse-' . $categoria['id'];
    $hasChildren = isset($categoria['children']) && count($categoria['children']) > 0;
    
    // Asignar clase de color según el nivel
    $colorClass = 'level-color-' . ($nivel % 6);
@endphp
<li class="level-{{ $nivel }} mb-2" data-categoria-id="{{ $categoria['id'] }}" data-estado="{{ $categoria['estado'] }}">
    <div class="d-flex align-items-start">
        @if($hasChildren)
        <button class="toggle-btn me-2" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}" aria-expanded="true" aria-controls="{{ $collapseId }}">
            <i class="fas fa-chevron-down"></i>
        </button>
        @else
        <div class="toggle-btn me-2" style="opacity: 0.3; cursor: default;">
            <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
        </div>
        @endif
        
        <div class="category-item flex-grow-1 {{ $colorClass }}" data-estado="{{ $categoria['estado'] }}">
            <div>
                <a href="{{ route('categorias.show', $categoria['id']) }}" class="text-decoration-none">
                    <span class="category-name fw-bold">{{ $categoria['nombre'] }}</span>
                </a>
                @if(!empty($categoria['descripcion']))
                    <small class="category-desc text-muted d-block">{{ Str::limit($categoria['descripcion'], 60) }}</small>
                @endif
                <div class="mt-2">
                    <span class="badge {{ $categoria['estado'] == 'activo' ? 'bg-success' : 'bg-danger' }}">
                        <i class="fas fa-{{ $categoria['estado'] == 'activo' ? 'check-circle' : 'times-circle' }} me-1"></i>
                        {{ ucfirst($categoria['estado']) }}
                    </span>
                    
                    @if(isset($categoria['productos_count']) && $categoria['productos_count'] > 0)
                    <span class="badge bg-info">
                        <i class="fas fa-box me-1"></i>
                        {{ $categoria['productos_count'] }} productos
                    </span>
                    @endif
                </div>
            </div>
            
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <div class="category-actions">
                <a href="{{ route('categorias.show', $categoria['id']) }}" class="btn btn-sm" title="Ver detalles">
                    <i class="fas fa-eye text-primary"></i>
                </a>
                <a href="{{ route('categorias.edit', $categoria['id']) }}" class="btn btn-sm" title="Editar categoría">
                    <i class="fas fa-edit text-warning"></i>
                </a>
                <form action="{{ route('categorias.destroy', $categoria['id']) }}" method="POST" 
                      class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm" title="Eliminar categoría">
                        <i class="fas fa-trash text-danger"></i>
                    </button>
                </form>
            </div>
            @else
            <div class="category-actions">
                <a href="{{ route('categorias.show', $categoria['id']) }}" class="btn btn-sm" title="Ver detalles">
                    <i class="fas fa-eye text-primary"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
    
    @if($hasChildren)
    <div class="collapse show mt-2" id="{{ $collapseId }}">
        <ul class="list-unstyled" style="margin-left: 20px;">
            @include('categorias.partials.tree', ['categorias' => $categoria['children'], 'nivel' => $nivel + 1])
        </ul>
    </div>
    @endif
</li>
@endforeach