<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
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
    $imageName = time() . '.' . $request->image->extension();
    $request->image->move(public_path('img'), $imageName);
    Product::create([
        'name'        => $request->name,
        'price'       => $request->price,
        'image'       => $imageName,
        'description' => $request->description,
    ]);
    return redirect('/admin')->with('success', 'Producto agregado correctamente');
})->middleware('auth');

Route::put('/admin/update/{id}', function (Request $request, $id) {
    $product = Product::findOrFail($id);
    $product->name = $request->name;
    $product->price = $request->price;
    $product->description = $request->description;
    
    if ($request->hasFile('image')) {
        // Eliminar imagen anterior
        if (file_exists(public_path('img/' . $product->image))) {
            unlink(public_path('img/' . $product->image));
        }
        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('img'), $imageName);
        $product->image = $imageName;
    }
    
    $product->save();
    return redirect('/admin')->with('success', 'Producto actualizado correctamente');
})->middleware('auth');

Route::delete('/admin/delete/{id}', function ($id) {
    $product = Product::findOrFail($id);
    
    // Eliminar imagen
    if (file_exists(public_path('img/' . $product->image))) {
        unlink(public_path('img/' . $product->image));
    }
    
    $product->delete();
    return redirect('/admin')->with('success', 'Producto eliminado correctamente');
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