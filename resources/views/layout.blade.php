<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>RAIKKO</title>
<link rel="icon" href="/img/raikko_logo.jpg" type="image/jpeg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Bebas+Neue&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/style.css">
</head>

<body>

<div class="noise-overlay"></div>

<header class="header">
    <div class="header-left">
        <div class="menu-container">
            <span class="menu-btn"><span class="menu-lines"><span></span><span></span><span></span></span> Menú</span>
            <div class="menu-dropdown">
                <a href="/">Home</a>
                <a href="/shop">Shop</a>
                <a href="/cart">Cart</a>
            </div>
        </div>
    </div>

    <a href="/" class="logo">RAIKKO</a>

    <div class="header-right">
        @guest
            <a href="/register" class="header-link">Registrarse</a>
            <a href="/login" class="header-link">Inicia sesión</a>
        @endguest

        @auth
            <a href="/profile" class="account-icon" title="Mi cuenta">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </a>
            @if(auth()->user()->role === 'admin')
                <a href="/admin" class="header-link">Admin</a>
            @endif
            <form method="POST" action="/logout" style="display:inline;">
                @csrf
                <button class="logout-btn">Salir</button>
            </form>
        @endauth

        <a href="/cart" class="icon-btn cart-icon" title="Cart">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        </a>
    </div>
</header>

<main>
@yield('content')
</main>

<footer class="footer">
    <div class="footer-inner">
        <div class="footer-brand">RAIKKO</div>
        <div class="footer-links">
            <a href="/shop">Shop</a>
            <a href="/cart">Cart</a>
            <span>+34 612 345 678</span>
        </div>
        <div class="footer-copy">© 2026 Raikko. All rights reserved.</div>
    </div>
</footer>

</body>
</html>