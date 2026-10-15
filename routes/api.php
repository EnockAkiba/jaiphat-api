<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Catalog\CategoryController;
use App\Http\Controllers\Api\V1\Catalog\ColorController;
use App\Http\Controllers\Api\V1\Catalog\ProductController;
use App\Http\Controllers\Api\V1\Forms\AppointmentController;
use App\Http\Controllers\Api\V1\Forms\ContactController;
use App\Http\Controllers\Api\V1\Forms\NewsletterController;
use App\Http\Controllers\Api\V1\Orders\OrderController;
use App\Http\Controllers\Api\V1\Shopping\CartController;
use App\Http\Controllers\Api\V1\Showcase\HomeController;
use App\Http\Controllers\Api\V1\Showcase\SiteController;
use App\Http\Controllers\Api\V1\Shopping\CartItemController;
use App\Http\Controllers\Api\V1\Shopping\WishlistController;
use App\Http\Middleware\IdentifyShopper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function (): void {
    // --- Authentification ---
    Route::prefix('auth')->group(function (): void {
        // throttle:10,1 = 10 tentatives par minute et par adresse IP (protège contre les essais de mots de passe)
        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // --- Catalogue (public) ---
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('colors', [ColorController::class, 'index']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/featured', [ProductController::class, 'featured']); // avant {slug} !
    Route::get('products/{slug}', [ProductController::class, 'show']);
    Route::get('products/{slug}/related', [ProductController::class, 'related']);

    // --- Vitrine (public) ---
    Route::get('home', [HomeController::class, 'show']);
    Route::get('settings', [SiteController::class, 'settings']);
    Route::get('pages/{slug}', [SiteController::class, 'page']);
    Route::get('testimonials', [SiteController::class, 'testimonials']);
    Route::get('brides', [SiteController::class, 'brides']);

    // --- Formulaires ---
    Route::get('appointments/availability', [AppointmentController::class, 'availability']); // avant {publicId} !
    Route::post('appointments', [AppointmentController::class, 'store'])->middleware('throttle:10,1');
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('appointments', [AppointmentController::class, 'index']);
        Route::delete('appointments/{publicId}', [AppointmentController::class, 'cancel']);
    });
    Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:5,1');
    Route::post('newsletter', [NewsletterController::class, 'store'])->middleware('throttle:10,1');

    // --- Panier & wishlist : utilisateur connecté (Bearer) OU invité (en-tête X-Guest-Token) ---
    Route::middleware(IdentifyShopper::class)->group(function (): void {
        Route::get('cart', [CartController::class, 'show']);
        Route::delete('cart', [CartController::class, 'destroy']);
        Route::post('cart/items', [CartItemController::class, 'store']);
        Route::patch('cart/items/{item}', [CartItemController::class, 'update'])->whereNumber('item');
        Route::delete('cart/items/{item}', [CartItemController::class, 'destroy'])->whereNumber('item');

        Route::get('wishlist', [WishlistController::class, 'show']);
        Route::post('wishlist/items', [WishlistController::class, 'store']);
        Route::delete('wishlist/items/{product}', [WishlistController::class, 'destroy'])->whereNumber('product');

        // Commandes : même identification (compte OU invité). 
        Route::get('orders', [OrderController::class, 'index']);
        Route::post('orders', [OrderController::class, 'store'])->middleware('throttle:10,1');
        Route::get('orders/{number}', [OrderController::class, 'show']);
        Route::post('orders/{number}/cancel', [OrderController::class, 'cancel']);
    });
});
