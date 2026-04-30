@extends('layout')

@section('content')

<div class="cart-page">
    <h1 class="cart-title">Carrito</h1>
    
    <div id="cart-container">
        <div class="cart-empty">
            <p>Tu carrito está vacío.</p>
            <a href="/shop" class="btn-back">Ir a la tienda →</a>
        </div>
    </div>

    <div id="cart-summary" style="display: none;">
        <div class="cart-items">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Talla</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="cart-items-tbody">
                </tbody>
            </table>
        </div>

        <div class="cart-totals">
            <div class="totals-row">
                <span>Subtotal:</span>
                <span><span id="subtotal">0.00</span><span class="currency">€</span></span>
            </div>
            <div class="totals-row">
                <span>IVA (21%):</span>
                <span><span id="tax">0.00</span><span class="currency">€</span></span>
            </div>
            <div class="totals-row totals-final">
                <span>Total:</span>
                <span><span id="total">0.00</span><span class="currency">€</span></span>
            </div>
            <a href="/checkout" class="btn-checkout">Proceder al pago</a>
            <a href="/shop" class="btn-continue-shopping">Continuar comprando</a>
        </div>
    </div>
</div>

<script>
    function loadCart() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const container = document.getElementById('cart-container');
        const summary = document.getElementById('cart-summary');
        const tbody = document.getElementById('cart-items-tbody');

        if (cart.length === 0) {
            container.style.display = 'block';
            summary.style.display = 'none';
            return;
        }

        container.style.display = 'none';
        summary.style.display = 'block';
        tbody.innerHTML = '';

        let subtotal = 0;

        cart.forEach((item, index) => {
            const itemTotal = item.productPrice * item.quantity;
            subtotal += itemTotal;

            const row = `
                <tr>
                    <td>${item.productName}</td>
                    <td>${item.size}</td>
                    <td>${item.productPrice.toFixed(2)}</td>
                    <td>
                        <div class="quantity-controls">
                            <button onclick="updateQuantity(${index}, -1)">−</button>
                            <span>${item.quantity}</span>
                            <button onclick="updateQuantity(${index}, 1)">+</button>
                        </div>
                    </td>
                    <td>${itemTotal.toFixed(2)}</td>
                    <td><button onclick="removeFromCart(${index})" class="btn-remove">Eliminar</button></td>
                </tr>
            `;
            tbody.innerHTML += row;
        });

        const tax = subtotal * 0.21;
        const total = subtotal + tax;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('tax').textContent = tax.toFixed(2);
        document.getElementById('total').textContent = total.toFixed(2);
    }

    function updateQuantity(index, change) {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart[index].quantity += change;

        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        loadCart();
    }

    function removeFromCart(index) {
        if (confirm('¿Eliminar este producto?')) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart();
        }
    }

    loadCart();
</script>

@endsection
