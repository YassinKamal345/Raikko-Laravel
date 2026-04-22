@extends('layout')

@section('content')

<div class="product-page">

    <div class="product-image">
        <img src="/img/{{$product->image}}">
    </div>

    <div class="product-info">

        <h2>{{$product->name}}</h2>

        <p class="price">{{$product->price}}€</p>

        <p>{{$product->description}}</p>

        <button class="btn">Add to cart</button>

    </div>

</div>

@endsection