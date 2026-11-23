<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\OutOfStockException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSize;

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
            !is_array($cart)
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
        | The cart comes from the browser (localStorage), so only the product
        | id, size and quantity are trusted. Name and price always come from
        | the database to prevent price tampering.
        */

        $validSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];

        $items = [];

        $subtotal = 0;

        foreach ($cart as $item) {

            $quantity = filter_var(
                $item['quantity'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 99]]
            );

            $size = $item['size'] ?? null;

            $product = Product::find(
                $item['productId'] ?? null
            );

            if (
                !$product
                ||
                $quantity === false
                ||
                ($size !== null && !in_array($size, $validSizes, true))
            ) {

                return redirect('/cart')
                    ->with(
                        'error',
                        'Hay productos no válidos en tu carrito'
                    );
            }

            $items[] = [
                'product'  => $product,
                'size'     => $size,
                'quantity' => $quantity,
            ];

            $subtotal +=
                $product->price
                *
                $quantity;
        }

        if (
            !in_array(
                $request->envio,
                ['recogida', 'domicilio'],
                true
            )
        ) {

            return redirect('/checkout')
                ->with(
                    'error',
                    'Selecciona un método de envío'
                );
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
        | Stock + Order
        |--------------------------------------------------------------------------
        | Everything runs in one transaction: if any size is out of stock,
        | no stock is discounted and no order is saved.
        */

        try {

            DB::transaction(function () use (
                $request,
                $items,
                $subtotal,
                $shipping,
                $total
            ) {

                $this->discountStock($items);

                $this->createOrder(
                    $request,
                    $items,
                    $subtotal,
                    $shipping,
                    $total
                );
            });

        } catch (OutOfStockException $e) {

            return redirect('/cart')
                ->with(
                    'error',
                    $e->getMessage()
                );
        }

        return redirect('/')
            ->with(
                'success',
                'Pedido realizado correctamente'
            )
            ->with(
                'order_placed',
                true
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Discount Stock
    |--------------------------------------------------------------------------
    | Products with sizes configured in the admin panel have stock control.
    | Products without any size configured are sold without stock control.
    */

    private function discountStock(
        array $items
    ): void {

        $needed = [];

        foreach ($items as $item) {

            $key =
                $item['product']->id
                . '|'
                . $item['size'];

            if (!isset($needed[$key])) {

                $needed[$key] = [
                    'product'  => $item['product'],
                    'size'     => $item['size'],
                    'quantity' => 0,
                ];
            }

            $needed[$key]['quantity'] +=
                $item['quantity'];
        }

        foreach ($needed as $line) {

            $product = $line['product'];

            if (
                !ProductSize::where(
                    'product_id',
                    $product->id
                )->exists()
            ) {
                continue;
            }

            $row = ProductSize::where(
                    'product_id',
                    $product->id
                )
                ->where(
                    'size',
                    $line['size']
                )
                ->lockForUpdate()
                ->first();

            if (
                !$row
                ||
                $row->stock < $line['quantity']
            ) {

                throw new OutOfStockException(
                    'No queda stock suficiente de '
                    . $product->name
                    . ' (talla '
                    . ($line['size'] ?? '-')
                    . '). Disponible: '
                    . ($row->stock ?? 0)
                );
            }

            $row->decrement(
                'stock',
                $line['quantity']
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Order
    |--------------------------------------------------------------------------
    */

    private function createOrder(
        Request $request,
        array $items,
        float $subtotal,
        float $shipping,
        float $total
    ): void {

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
            $items as $item
        ) {

            OrderItem::create([

                'order_id' =>
                    $order->id,

                'product_id' =>
                    $item['product']->id,

                'product_name' =>
                    $item['product']->name,

                'size' =>
                    $item['size'],

                'quantity' =>
                    $item['quantity'],

                'price' =>
                    $item['product']->price
            ]);
        }
    }
}