<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Models\Product;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('home');
});

Route::get('/shop', function (Illuminate\Http\Request $request) {

    $query = App\Models\Product::query();

    if($request->search){
        $query->where('name','like','%'.$request->search.'%');
    }

    $products = $query->get();

    return view('shop', compact('products'));
});

Route::get('/product/{id}', function ($id) {
    $product = Product::find($id);
    return view('product', compact('product'));
});

Route::get('/cart', function () {
    return view('cart');
});

Route::get('/admin', function () {
    return view('admin');
})->middleware('auth');

Route::post('/admin/add', function (Request $request) {

    $imageName = time().'.'.$request->image->extension();

    $request->image->move(public_path('img'), $imageName);

    Product::create([
        'name'=>$request->name,
        'price'=>$request->price,
        'image'=>$imageName,
        'description'=>$request->description
    ]);

    return redirect('/shop');

});

require __DIR__.'/auth.php';
