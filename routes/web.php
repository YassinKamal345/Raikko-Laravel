<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;

// ─── HOME ────────────────────────────────────────────────
Route::get('/', function () {
    $products = Product::all();
    return view('home', compact('products'));
});

// ─── SHOP ────────────────────────────────────────────────
Route::get('/shop', function (Request $request) {
    $query = Product::query();
    if ($request->search) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }
    $products = $query->get();
    return view('shop', compact('products'));
});

// ─── PRODUCT ─────────────────────────────────────────────
Route::get('/product/{id}', function ($id) {
    $product = Product::findOrFail($id);
    return view('product', compact('product'));
});

// ─── CART ────────────────────────────────────────────────
Route::get('/cart', function () {
    return view('cart');
});

// ─── ADMIN ───────────────────────────────────────────────
Route::get('/admin', function () {
    $products = Product::all();
    return view('admin', compact('products'));
})->middleware('auth');

Route::get('/admin/edit/{id}', function ($id) {
    $product = Product::findOrFail($id);
    $products = Product::all();
    return view('admin', compact('product', 'products'));
})->middleware('auth');

Route::post('/admin/add', function (Request $request) {
    $product = Product::create([
        'name'        => $request->name,
        'price'       => $request->price,
        'image'       => '',
        'description' => $request->description,
    ]);

    // Manejar múltiples imágenes
    if ($request->hasFile('images')) {
        $order = 0;
        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $image->move(public_path('img'), $imageName);
            ProductImage::create([
                'product_id' => $product->id,
                'image'      => $imageName,
                'order'      => $order++
            ]);
        }
        
        // Establecer la primera imagen como imagen principal
        if ($product->images->count() > 0) {
            $product->image = $product->images->first()->image;
            $product->save();
        }
    }

    // Manejar tallas y stock
    if ($request->sizes) {
        foreach ($request->sizes as $size => $stock) {
            if ($stock !== null && $stock !== '') {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size'       => $size,
                    'stock'      => intval($stock)
                ]);
            }
        }
    }

    return redirect('/admin')->with('success', 'Producto agregado correctamente');
})->middleware('auth');

Route::put('/admin/update/{id}', function (Request $request, $id) {
    $product = Product::findOrFail($id);
    $product->name = $request->name;
    $product->price = $request->price;
    $product->description = $request->description;
    
    // Manejar nuevas imágenes
    if ($request->hasFile('images')) {
        $order = $product->images->count();
        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $image->move(public_path('img'), $imageName);
            ProductImage::create([
                'product_id' => $product->id,
                'image'      => $imageName,
                'order'      => $order++
            ]);
        }
    }
    
    // Actualizar imagen principal si es necesario
    if ($product->images->count() > 0 && !$product->image) {
        $product->image = $product->images->first()->image;
    }

    // Actualizar tallas y stock
    if ($request->sizes) {
        foreach ($request->sizes as $size => $stock) {
            if ($stock !== null && $stock !== '') {
                ProductSize::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'size'       => $size
                    ],
                    [
                        'stock' => intval($stock)
                    ]
                );
            }
        }
    }
    
    $product->save();
    return redirect('/admin')->with('success', 'Producto actualizado correctamente');
})->middleware('auth');

Route::delete('/admin/delete/{id}', function ($id) {
    $product = Product::findOrFail($id);
    
    // Eliminar todas las imágenes
    foreach ($product->images as $image) {
        if (file_exists(public_path('img/' . $image->image))) {
            unlink(public_path('img/' . $image->image));
        }
        $image->delete();
    }
    
    // Eliminar imagen principal si existe
    if ($product->image && file_exists(public_path('img/' . $product->image))) {
        unlink(public_path('img/' . $product->image));
    }
    
    $product->delete();
    return redirect('/admin')->with('success', 'Producto eliminado correctamente');
})->middleware('auth');

Route::delete('/admin/delete-image/{id}', function ($id) {
    $image = ProductImage::findOrFail($id);
    $product = $image->product;
    
    // Eliminar archivo
    if (file_exists(public_path('img/' . $image->image))) {
        unlink(public_path('img/' . $image->image));
    }
    
    $image->delete();
    
    // Si no hay más imágenes, limpiar la imagen principal
    if ($product->images->count() === 0) {
        $product->image = '';
        $product->save();
    }
    
    return response()->json(['success' => true]);
})->middleware('auth');

Route::post('/admin/reorder-image', function (Request $request) {
    $image = ProductImage::findOrFail($request->image_id);
    $product = $image->product;
    $images = $product->images()->orderBy('order')->get();
    
    $currentIndex = $images->search(function($img) use ($image) {
        return $img->id === $image->id;
    });
    
    if ($request->direction === 'up' && $currentIndex > 0) {
        // Intercambiar con la anterior
        $prevImage = $images[$currentIndex - 1];
        $tempOrder = $image->order;
        $image->order = $prevImage->order;
        $prevImage->order = $tempOrder;
        $image->save();
        $prevImage->save();
    } elseif ($request->direction === 'down' && $currentIndex < $images->count() - 1) {
        // Intercambiar con la siguiente
        $nextImage = $images[$currentIndex + 1];
        $tempOrder = $image->order;
        $image->order = $nextImage->order;
        $nextImage->order = $tempOrder;
        $image->save();
        $nextImage->save();
    }
    
    return response()->json(['success' => true]);
})->middleware('auth');

// ─── AUTH (Breeze) ────────────────────────────────────────
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';