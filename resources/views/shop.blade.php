@extends('layout')

@section('content')

<div class="shop-page">
    <div class="shop-hero">
        <h1 class="shop-title">Shop</h1>
        <p class="shop-count">{{ $products->count() }} productos</p>
    </div>

    <div class="products-grid">
        @foreach($products as $product)
        <div class="product-card">
            <a href="/product/{{ $product->id }}" class="product-card-link">
                <div class="product-card-img">
                    <img src="/img/{{ $product->images->first()?->image ?? $product->image }}" alt="{{ $product->name }}">
                    <div class="product-card-overlay">
                        <span>Ver producto →</span>
                    </div>
                </div>
                <div class="product-card-info">
                    <h3 class="product-card-name">{{ $product->name }}</h3>
                    <p class="product-card-price">{{ $product->price }} €</p>
                </div>
            </a>
            <a href="/product/{{ $product->id }}" class="btn-view">View</a>
        </div>
        @endforeach
    </div>
</div>

@endsection