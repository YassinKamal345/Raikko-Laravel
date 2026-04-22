@extends('layout')

@section('content')

<h2>Add Product</h2>

<form method="POST" action="/admin/add" enctype="multipart/form-data">
@csrf

<input name="name" placeholder="Product name">

<input name="price" placeholder="Price">

<input type="file" name="image">

<textarea name="description"></textarea>

<button>Add product</button>

</form>

@endsection