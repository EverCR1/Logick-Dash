@foreach($categorias as $categoria)
<li class="level-{{ $nivel }}">
    <div class="category-item">
        <div>
            <span class="category-name">{{ $categoria['nombre'] }}</span>
            @if($categoria['descripcion'])
                <small class="text-muted d-block">{{ $categoria['descripcion'] }}</small>
            @endif
            <small class="badge {{ $categoria['estado'] == 'activo' ? 'bg-success' : 'bg-danger' }}">
                {{ $categoria['estado'] }}
            </small>
        </div>
        @if(in_array($userRole, ['administrador', 'vendedor']))
        <div class="category-actions">
            <a href="{{ route('categorias.edit', $categoria['id']) }}" class="btn btn-sm btn-warning">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('categorias.destroy', $categoria['id']) }}" method="POST" 
                  class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría y sus subcategorías?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
        @endif
    </div>
    
    @if(isset($categoria['children']) && count($categoria['children']) > 0)
        <ul class="list-unstyled">
            @include('categorias.partials.tree', ['categorias' => $categoria['children'], 'nivel' => $nivel + 1])
        </ul>
    @endif
</li>
@endforeach