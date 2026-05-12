<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class CheckoutController
extends Controller
{
    public function cart()
    {
        return view('cart');
    }

    public function checkout()
    {
        return view('checkout');
    }

    public function process(
        Request $request
    ) {

        /*
        |--------------------------------------------------------------------------
        | Cart
        |--------------------------------------------------------------------------
        */

        $cart = json_decode(
            $request->cart,
            true
        );

        if (
            !$cart
            ||
            count($cart) === 0
        ) {

            return redirect('/cart')
                ->with(
                    'error',
                    'Tu carrito está vacío'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Prices
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;

        foreach ($cart as $item) {

            $subtotal +=
                $item['productPrice']
                *
                $item['quantity'];
        }

        $shipping =
            $request->envio
            === 'domicilio'
            ? 5
            : 0;

        $total =
            $subtotal
            +
            ($subtotal * 0.21)
            +
            $shipping;

        /*
        |--------------------------------------------------------------------------
        | Create Order
        |--------------------------------------------------------------------------
        */

        $order = Order::create([

            'user_id' =>
                auth()->id(),

            'status' =>
                'pending',

            'payment_status' =>
                'pending',

            'subtotal' =>
                $subtotal,

            'shipping' =>
                $shipping,

            'total' =>
                $total,

            'customer_name' =>
                $request->nombre
                . ' '
                . $request->apellidos,

            'customer_email' =>
                $request->email,

            'address' =>
                $request->direccion,

            'city' =>
                $request->ciudad,

            'postal_code' =>
                $request->codigo_postal,

            'country' =>
                'Spain',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create Order Items
        |--------------------------------------------------------------------------
        */

        foreach (
            $cart as $item
        ) {

            OrderItem::create([

                'order_id' =>
                    $order->id,

                'product_id' =>
                    $item['productId'],

                'product_name' =>
                    $item['productName'],

                'size' =>
                    $item['size'],

                'quantity' =>
                    $item['quantity'],

                'price' =>
                    $item['productPrice']
            ]);
        }

        return redirect('/')
            ->with(
                'success',
                'Pedido realizado correctamente'
            );
    }
}