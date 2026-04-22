@extends('layout')

@section('content')

<h2 class="title">Shop</h2>
<div>
    <div class="products">

    @foreach($products as $product)

    <div class="product">

    <img src="/img/{{$product->image}}">

    <h3>{{$product->name}}</h3>

    <p>{{$product->price}} €</p>

    <a href="/product/{{$product->id}}" class="btn-view">View</a>

    </div>

    @endforeach

    </div>
</div>
@endsection