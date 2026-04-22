@extends('layout')

@section('content')

<div class="cart-page">
    <h1 class="cart-title">Your Cart</h1>
    <div class="cart-empty">
        <p>Tu carrito está vacío.</p>
        <a href="/shop" class="btn-back">Ir a la tienda →</a>
    </div>
</div>

@endsection