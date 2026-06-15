<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () { return view('app'); })->name('home');
Route::get('/shop', function () { return view('app'); })->name('shop');
Route::get('/product/{slug}', function () { return view('app'); })->name('product.show');
Route::get('/category/{slug}', function () { return view('app'); })->name('category.show');
Route::get('/about', function () { return view('app'); })->name('about');
Route::get('/contact', function () { return view('app'); })->name('contact');
Route::get('/wishlist', function () { return view('app'); })->name('wishlist');
Route::get('/track', function () { return view('app'); })->name('track');

Route::get('/cart', function () { return view('app'); })->name('cart');

Route::get('/checkout', function () { return view('app'); })->name('checkout');

Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');

    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
});

Route::get('/login', function () {
    return view('app');
})->name('login');

Route::get('/register', function () {
    return view('app');
})->name('register');

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
