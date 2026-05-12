<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;

class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Admin Page
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $products = Product::all();

        return view('admin', compact('products'));
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Product
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $products = Product::all();

        return view(
            'admin',
            compact('product', 'products')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Add Product
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $product = Product::create([
            'name'        => $request->name,
            'price'       => $request->price,
            'image'       => '',
            'description' => $request->description,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Multiple Images
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('images')) {

            $order = 0;

            foreach (
                $request->file('images')
                as $image
            ) {

                $imageName =
                    time()
                    . '_'
                    . uniqid()
                    . '.'
                    . $image->extension();

                $image->move(
                    public_path('img'),
                    $imageName
                );

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $imageName,
                    'order'      => $order++
                ]);
            }

            $product->refresh();

            if (
                $product->images->count() > 0
            ) {

                $product->image =
                    $product
                    ->images
                    ->first()
                    ->image;

                $product->save();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Sizes & Stock
        |--------------------------------------------------------------------------
        */

        if ($request->sizes) {

            foreach (
                $request->sizes
                as $size => $stock
            ) {

                if (
                    $stock !== null
                    && $stock !== ''
                ) {

                    ProductSize::create([
                        'product_id' => $product->id,
                        'size'       => $size,
                        'stock'      => intval($stock)
                    ]);
                }
            }
        }

        return redirect('/admin')
            ->with(
                'success',
                'Producto agregado correctamente'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Update Product
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $product =
            Product::findOrFail($id);

        $product->name =
            $request->name;

        $product->price =
            $request->price;

        $product->description =
            $request->description;

        /*
        |--------------------------------------------------------------------------
        | New Images
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('images')) {

            $order =
                $product
                ->images
                ->count();

            foreach (
                $request->file('images')
                as $image
            ) {

                $imageName =
                    time()
                    . '_'
                    . uniqid()
                    . '.'
                    . $image->extension();

                $image->move(
                    public_path('img'),
                    $imageName
                );

                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => $imageName,
                    'order'      => $order++
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Main Image
        |--------------------------------------------------------------------------
        */

        $product->refresh();

        if (
            $product->images->count() > 0
            && !$product->image
        ) {

            $product->image =
                $product
                ->images
                ->first()
                ->image;
        }

        /*
        |--------------------------------------------------------------------------
        | Update Sizes & Stock
        |--------------------------------------------------------------------------
        */

        if ($request->sizes) {

            foreach (
                $request->sizes
                as $size => $stock
            ) {

                if (
                    $stock !== null
                    && $stock !== ''
                ) {

                    ProductSize::updateOrCreate(
                        [
                            'product_id' =>
                            $product->id,

                            'size' => $size
                        ],
                        [
                            'stock' =>
                            intval($stock)
                        ]
                    );
                }
            }
        }

        $product->save();

        return redirect('/admin')
            ->with(
                'success',
                'Producto actualizado correctamente'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Product
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $product =
            Product::findOrFail($id);

        foreach (
            $product->images
            as $image
        ) {

            $path =
                public_path(
                    'img/' .
                    $image->image
                );

            if (file_exists($path)) {
                unlink($path);
            }

            $image->delete();
        }

        if ($product->image) {

            $mainImage =
                public_path(
                    'img/' .
                    $product->image
                );

            if (
                file_exists($mainImage)
            ) {
                unlink($mainImage);
            }
        }

        $product->delete();

        return redirect('/admin')
            ->with(
                'success',
                'Producto eliminado correctamente'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Single Image
    |--------------------------------------------------------------------------
    */

    public function deleteImage($id)
    {
        $image =
            ProductImage::findOrFail($id);

        $product =
            $image->product;

        $path =
            public_path(
                'img/' .
                $image->image
            );

        if (file_exists($path)) {
            unlink($path);
        }

        $image->delete();

        $product->refresh();

        if (
            $product->images->count()
            === 0
        ) {

            $product->image = '';
            $product->save();
        }

        return response()->json([
            'success' => true
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder Images
    |--------------------------------------------------------------------------
    */

    public function reorderImage(
        Request $request
    ) {

        $image =
            ProductImage::findOrFail(
                $request->image_id
            );

        $product =
            $image->product;

        $images =
            $product
            ->images()
            ->orderBy('order')
            ->get();

        $currentIndex =
            $images->search(
                function ($img)
                use ($image) {

                    return
                        $img->id
                        ===
                        $image->id;
                }
            );

        if (
            $request->direction
            === 'up'
            &&
            $currentIndex > 0
        ) {

            $prevImage =
                $images[
                    $currentIndex - 1
                ];

            $tempOrder =
                $image->order;

            $image->order =
                $prevImage->order;

            $prevImage->order =
                $tempOrder;

            $image->save();
            $prevImage->save();
        }

        elseif (
            $request->direction
            === 'down'
            &&
            $currentIndex
            <
            $images->count() - 1
        ) {

            $nextImage =
                $images[
                    $currentIndex + 1
                ];

            $tempOrder =
                $image->order;

            $image->order =
                $nextImage->order;

            $nextImage->order =
                $tempOrder;

            $image->save();
            $nextImage->save();
        }

        return response()->json([
            'success' => true
        ]);
    }
}