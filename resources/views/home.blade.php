@extends('layout')

@section('content')

<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <p class="hero-eyebrow">SS26 Collection</p>
        <h1 class="hero-title">NEW<br><em>STREETWEAR</em><br>DROP</h1>
        <p class="hero-sub">Made for the best</p>
        <a class="hero-btn" href="/shop">
            <span>Shop Now</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>
    <div class="hero-scroll">
        <span>Scroll</span>
        <div class="scroll-line"></div>
    </div>
</section>

<section class="marquee-strip">
    <div class="marquee-track">
        <span>RAIKKO</span><span class="dot">·</span>
        <span>STREETWEAR</span><span class="dot">·</span>
        <span>SS26</span><span class="dot">·</span>
        <span>MADE FOR THE BEST</span><span class="dot">·</span>
        <span>RAIKKO</span><span class="dot">·</span>
        <span>STREETWEAR</span><span class="dot">·</span>
        <span>SS26</span><span class="dot">·</span>
        <span>MADE FOR THE BEST</span><span class="dot">·</span>
        <span>RAIKKO</span><span class="dot">·</span>
        <span>STREETWEAR</span><span class="dot">·</span>
        <span>SS26</span><span class="dot">·</span>
        <span>MADE FOR THE BEST</span><span class="dot">·</span>
        <span>RAIKKO</span><span class="dot">·</span>
        <span>STREETWEAR</span><span class="dot">·</span>
        <span>SS26</span><span class="dot">·</span>
        <span>MADE FOR THE BEST</span><span class="dot">·</span>
    </div>
</section>

<section class="home-featured">
    <div class="section-header">
        <h2 class="section-title">Featured</h2>
        <a href="/shop" class="section-link">View all →</a>
    </div>
    <div class="featured-grid">
        @foreach($products as $product)
        <div class="featured-item">
            <a href="/product/{{ $product->id }}" class="featured-img-wrap">
                <img src="/img/{{ $product->image }}" alt="{{ $product->name }}">
                <div class="featured-overlay">
                    <span class="featured-cta">Ver producto</span>
                </div>
            </a>
            <div class="featured-info">
                <span class="featured-name">{{ $product->name }}</span>
                <span class="featured-price">{{ $product->price }} €</span>
            </div>
        </div>
        @endforeach
    </div>
</section>

<section class="home-editorial">
    <div class="editorial-text">
        <p class="editorial-quote">"Worn by those who<br>don't ask permission."</p>
        <a href="/shop" class="editorial-link">Explore the collection</a>
    </div>
</section>

@endsection