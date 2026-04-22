@extends('layout')

@section('content')

<div class="admin-page">
    <div class="admin-header">
        <div>
            <h1 class="admin-title">Admin Dashboard</h1>
            <p class="admin-subtitle">Gestión de productos</p>
        </div>
        <div class="admin-stats">
            <div class="stat-box">
                <span class="stat-number">{{ $products->count() }}</span>
                <span class="stat-label">Productos</span>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <!-- Formulario de agregar/editar -->
        <div class="admin-form-section">
            <h2>{{ isset($product) ? 'Editar Producto' : 'Agregar Nuevo Producto' }}</h2>
            <form method="POST" action="{{ isset($product) ? '/admin/update/' . $product->id : '/admin/add' }}" enctype="multipart/form-data" class="admin-form">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif
                
                <div class="form-group">
                    <label>Nombre del Producto</label>
                    <input type="text" name="name" placeholder="e.g. Aura T-Shirt" value="{{ $product->name ?? '' }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Precio (€)</label>
                        <input type="number" name="price" placeholder="65" step="0.01" value="{{ $product->price ?? '' }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Imagen</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="image" id="image-input" accept="image/*">
                        <label for="image-input" class="file-input-label">
                            <span class="file-input-text">Seleccionar imagen</span>
                        </label>
                    </div>
                    @if(isset($product) && $product->image)
                        <div class="image-preview">
                            <img src="/img/{{ $product->image }}" alt="{{ $product->name }}">
                            <p>Imagen actual</p>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="description" rows="4" placeholder="Describe el producto...">{{ $product->description ?? '' }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        {{ isset($product) ? 'Actualizar Producto' : 'Agregar Producto' }}
                    </button>
                    @if(isset($product))
                        <a href="/admin" class="btn-cancel">Cancelar</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Lista de productos -->
        <div class="admin-products-section">
            <h2>Lista de Productos</h2>
            @if($products->count() > 0)
                <div class="products-table">
                    @foreach($products as $prod)
                        <div class="product-row">
                            <div class="product-cell product-img">
                                <img src="/img/{{ $prod->image }}" alt="{{ $prod->name }}">
                            </div>
                            <div class="product-cell product-info">
                                <h3>{{ $prod->name }}</h3>
                                <p class="price">{{ $prod->price }} €</p>
                            </div>
                            <div class="product-cell product-actions">
                                <a href="/admin/edit/{{ $prod->id }}" class="btn-edit">Editar</a>
                                <form method="POST" action="/admin/delete/{{ $prod->id }}" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" onclick="return confirm('¿Eliminar este producto?')">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="no-products">No hay productos. Crea uno nuevo para empezar.</p>
            @endif
        </div>
    </div>
</div>

@endsection