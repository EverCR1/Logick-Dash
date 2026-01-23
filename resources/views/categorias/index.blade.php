@extends('layouts.app')

@section('title', 'Categorías - LOGICK')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Categorías</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Árbol de Categorías</h5>
            @if(in_array($userRole, ['administrador', 'vendedor']))
            <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Nueva Categoría
            </a>
            @endif
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="tree-view">
                @if(count($categorias) > 0)
                    <ul class="list-unstyled">
                        @include('categorias.partials.tree', ['categorias' => $categorias, 'nivel' => 0])
                    </ul>
                @else
                    <p class="text-center text-muted">No hay categorías registradas</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.tree-view ul {
    padding-left: 20px;
}

.tree-view li {
    margin: 10px 0;
    position: relative;
}

.tree-view .category-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tree-view .category-name {
    font-weight: 500;
}

.tree-view .category-actions {
    display: flex;
    gap: 5px;
}

.tree-view .level-0 > .category-item {
    background: #e3f2fd;
    border-color: #bbdefb;
}

.tree-view .level-1 > .category-item {
    background: #f3e5f5;
    border-color: #e1bee7;
    margin-left: 20px;
}

.tree-view .level-2 > .category-item {
    background: #fff8e1;
    border-color: #ffecb3;
    margin-left: 40px;
}

.tree-view .level-3 > .category-item {
    background: #e8f5e8;
    border-color: #c8e6c9;
    margin-left: 60px;
}
</style>
@endsection