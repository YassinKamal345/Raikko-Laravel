<!DOCTYPE html>
<html>
<head>
<title>Raikko</title>
<link rel="stylesheet" href="/css/style.css">
</head>

<body>

<header class="header">

<div class="left">

    <div class="menu-container">
        <span class="menu-btn">☰ Menú</span>

        <div class="menu-dropdown">
            <a href="/">Home</a>
            <a href="/shop">Shop</a>
            <a href="/cart">Cart</a>
        </div>
    </div>

    <form action="/shop" method="GET">
        <input type="text" name="search" placeholder="Buscar">
    </form>

</div>

<a href="/" class="logo">RAIKKO</a>

<div class="right">

    <span>Llámenos</span>

    @guest
        <a href="/login">👤</a>
    @endguest

    @auth
        <span>👤</span>

        @if(auth()->user()->role === 'admin')
            <a href="/admin">Admin</a>
        @endif

        <form method="POST" action="/logout" style="display:inline;">
            @csrf
            <button class="logout-btn">Logout</button>
        </form>
    @endauth

    <a href="/cart">🛒</a>

</div>
</header>

@yield('content')

<footer>
<p>© 2026 Raikko</p>
<p>+34 612 345 678</p>
</footer>

</body>
</html>