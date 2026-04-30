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
                    <label>Tallas y Stock</label>
                    <div class="sizes-grid">
                        @php
                            $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                            $productSizes = isset($product) ? $product->sizes->keyBy('size') : collect([]);
                        @endphp
                        @foreach($sizes as $size)
                            <div class="size-input-group">
                                <label>{{ $size }}</label>
                                <input type="number" name="sizes[{{ $size }}]" placeholder="Stock" min="0" value="{{ $productSizes->get($size)?->stock ?? '' }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="form-group">
                    <label>Imágenes del Producto</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="images[]" id="images-input" accept="image/*" multiple>
                        <label for="images-input" class="file-input-label">
                            <span class="file-input-text">Seleccionar imágenes (múltiples)</span>
                        </label>
                    </div>
                    @if(isset($product) && $product->images->count() > 0)
                        <div class="images-preview">
                            <p style="font-size: 11px; color: var(--white-dim); margin-bottom: 12px;">Imágenes actuales (arrastra para cambiar orden):</p>
                            <div class="preview-grid" id="images-grid">
                                @foreach($product->images as $index => $image)
                                    <div class="preview-item" data-image-id="{{ $image->id }}" data-order="{{ $image->order }}">
                                        <img src="/img/{{ $image->image }}" alt="{{ $product->name }}">
                                        <div class="image-order-badge">{{ $index + 1 }}</div>
                                        <div class="image-controls">
                                            @if($index > 0)
                                                <button type="button" class="btn-order-up" onclick="moveImage({{ $image->id }}, 'up')">↑</button>
                                            @endif
                                            @if($index < $product->images->count() - 1)
                                                <button type="button" class="btn-order-down" onclick="moveImage({{ $image->id }}, 'down')">↓</button>
                                            @endif
                                            <button type="button" class="btn-delete-image" onclick="deleteImage({{ $image->id }})">×</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
                                <img src="/img/{{ $prod->images->first()?->image ?? $prod->image }}" alt="{{ $prod->name }}">
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

<script>
function deleteImage(imageId) {
    if (!confirm('¿Eliminar esta imagen?')) return;
    
    fetch('/admin/delete-image/' + imageId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al eliminar imagen');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar imagen');
    });
}

function moveImage(imageId, direction) {
    fetch('/admin/reorder-image', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            image_id: imageId,
            direction: direction
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error al cambiar orden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cambiar orden');
    });
}
</script>

@endsection