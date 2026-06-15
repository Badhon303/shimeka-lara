<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public tracking route (no auth required)
Route::get('/track', [OrderController::class, 'track']);

// Guest checkout
Route::post('/guest-orders', [OrderController::class, 'guestStore']);

// Public routes
Route::get('/settings', [AdminController::class, 'settings']);
Route::post('/coupon/validate', [OrderController::class, 'validateCoupon']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/featured', [ProductController::class, 'featured']);
Route::get('/products/new', [ProductController::class, 'newArrivals']);
Route::get('/product/{slug}', [ProductController::class, 'show']);
Route::get('/products/search', [ProductController::class, 'search']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/category/{slug}', [CategoryController::class, 'show']);
Route::get('/category/{slug}/products', [CategoryController::class, 'products']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update/{id}', [CartController::class, 'update']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    
    // User Profile
    Route::get('/profile', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::put('/profile/password', [UserController::class, 'updatePassword']);
    
    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users', [AdminController::class, 'createUser']);
        Route::put('/users/{id}', [AdminController::class, 'updateUser']);
        Route::get('/orders', [AdminController::class, 'orders']);
        
        // Products CRUD
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        
        // Categories CRUD
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
        
        // Order management
        Route::get('/orders/{id}', [OrderController::class, 'adminShow']);
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);

        // Settings
        Route::get('/settings/all', [AdminController::class, 'allSettings']);
        Route::post('/settings', [AdminController::class, 'updateSettings']);

        // Coupons
        Route::get('/coupons', [AdminController::class, 'coupons']);
        Route::post('/coupons', [AdminController::class, 'createCoupon']);
        Route::put('/coupons/{id}', [AdminController::class, 'updateCoupon']);
        Route::delete('/coupons/{id}', [AdminController::class, 'deleteCoupon']);

        // Reports
        Route::get('/reports/sales', [AdminController::class, 'salesReport']);
        Route::get('/reports/orders', [AdminController::class, 'ordersReport']);

        // Database Tools
        Route::get('/db-export', [AdminController::class, 'exportDb']);
        Route::post('/db-import', [AdminController::class, 'importDb']);
    });
});
