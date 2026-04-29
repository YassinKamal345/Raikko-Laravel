@extends('layout')

@section('content')

<div class="product-detail">
    <div class="product-detail-container">
        <div class="product-detail-gallery">
            <div class="gallery-container">
                <img id="mainImage" src="/img/{{ $product->images->first()?->image ?? $product->image }}" alt="{{ $product->name }}" class="gallery-main-image">
                
                @if($product->images->count() > 1)
                    <button class="gallery-nav gallery-prev" onclick="previousImage()">❮</button>
                    <button class="gallery-nav gallery-next" onclick="nextImage()">❯</button>
                @endif
            </div>
            
            @if($product->images->count() > 1)
                <div class="gallery-thumbnails">
                    @foreach($product->images as $index => $image)
                        <img src="/img/{{ $image->image }}" 
                             alt="{{ $product->name }}" 
                             class="gallery-thumbnail {{ $index === 0 ? 'active' : '' }}"
                             onclick="changeImage({{ $index }})">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="product-detail-info">
            <h1 class="product-detail-title">{{ $product->name }}</h1>
            <p class="product-detail-price">{{ $product->price }} €</p>
            <p class="product-detail-description">{{ $product->description ?? 'No hay descripción disponible' }}</p>
            
            <div class="product-sizes-section">
                <label class="product-size-label">Selecciona una talla:</label>
                <div class="product-sizes">
                    @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL'] as $size)
                        @php
                            $productSize = $product->sizes->firstWhere('size', $size);
                            $isAvailable = $productSize && $productSize->stock > 0;
                        @endphp
                        <label class="size-option {{ !$isAvailable ? 'disabled' : '' }}">
                            <input type="radio" name="size" value="{{ $size }}" {{ !$isAvailable ? 'disabled' : '' }} required>
                            <span class="size-label">{{ $size }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="size-stock-info">Stock: <span id="size-stock">—</span></p>
            </div>
            
            <button class="btn-add-cart" onclick="addToCart({{ $product->id }})">Añadir al carrito</button>
        </div>
    </div>
</div>

<script>
    let currentImageIndex = 0;
    // @ts-ignore
    const images = {!! json_encode($product->images->pluck('image')->toArray()) !!};

    function changeImage(index) {
        currentImageIndex = index;
        updateImage();
    }

    function nextImage() {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        updateImage();
    }

    function previousImage() {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        updateImage();
    }

    function updateImage() {
        document.getElementById('mainImage').src = '/img/' + images[currentImageIndex];
        document.querySelectorAll('.gallery-thumbnail').forEach((thumb, index) => {
            thumb.classList.toggle('active', index === currentImageIndex);
        });
    }
</script>

@endsection
