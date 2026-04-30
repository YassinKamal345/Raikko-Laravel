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
                        <label class="size-option">
                            <input type="radio" name="size" value="{{ $size }}" required>
                            <span class="size-label">{{ $size }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="product-quantity-section">
                <label class="product-quantity-label">Cantidad:</label>
                <div class="quantity-input">
                    <button type="button" class="qty-btn" onclick="decreaseQty()">−</button>
                    <input type="number" id="quantity" min="1" max="99" value="1" readonly>
                    <button type="button" class="qty-btn" onclick="increaseQty()">+</button>
                </div>
            </div>
            
            <button class="btn-add-cart" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})">Añadir al carrito</button>
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

    function increaseQty() {
        const qtyInput = document.getElementById('quantity');
        if (qtyInput.value < 99) {
            qtyInput.value = parseInt(qtyInput.value) + 1;
        }
    }

    function decreaseQty() {
        const qtyInput = document.getElementById('quantity');
        if (qtyInput.value > 1) {
            qtyInput.value = parseInt(qtyInput.value) - 1;
        }
    }

    function addToCart(productId, productName, productPrice) {
        const selectedSize = document.querySelector('input[name="size"]:checked')?.value;
        const quantity = parseInt(document.getElementById('quantity').value);

        if (!selectedSize) {
            alert('Por favor selecciona una talla');
            return;
        }

        // Obtener carrito actual
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Buscar si el producto con esa talla ya existe
        const existingItem = cart.find(item => item.productId == productId && item.size === selectedSize);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                productId: productId,
                productName: productName,
                productPrice: productPrice,
                size: selectedSize,
                quantity: quantity
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        alert('¡Producto añadido al carrito!');
        document.getElementById('quantity').value = 1;
        document.querySelector('input[name="size"]').checked = false;
    }
</script>

@endsection
