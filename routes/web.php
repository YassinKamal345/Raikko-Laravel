<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index']);

Route::get('/shop', [ShopController::class, 'index']);

Route::get('/product/{id}', [ProductController::class, 'show']);

Route::get('/cart', [CheckoutController::class, 'cart']);

Route::get('/checkout', [CheckoutController::class, 'checkout']);

Route::post('/checkout/process', [
    CheckoutController::class,
    'process'
]);

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/admin', [
        AdminController::class,
        'index'
    ]);

    Route::get('/admin/edit/{id}', [
        AdminController::class,
        'edit'
    ]);

    Route::post('/admin/add', [
        AdminController::class,
        'store'
    ]);

    Route::put('/admin/update/{id}', [
        AdminController::class,
        'update'
    ]);

    Route::delete('/admin/delete/{id}', [
        AdminController::class,
        'destroy'
    ]);

    Route::delete('/admin/delete-image/{id}', [
        AdminController::class,
        'deleteImage'
    ]);

    Route::post('/admin/reorder-image', [
        AdminController::class,
        'reorderImage'
    ]);
});

/*
|--------------------------------------------------------------------------
| Breeze
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])
->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');
});

require __DIR__.'/auth.php';