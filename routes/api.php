<?php

use App\Http\Controllers\Api\V1\Catalog\CategoryController;
use App\Http\Controllers\Api\V1\Catalog\ColorController;
use App\Http\Controllers\Api\V1\Catalog\ProductController;
use App\Http\Controllers\Api\V1\Shopping\CartController;
use App\Http\Controllers\Api\V1\Shopping\CartItemController;
use App\Http\Controllers\Api\V1\Shopping\WishlistController;
use App\Http\Middleware\IdentifyShopper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function (): void {
    // --- Catalogue (public) ---
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('colors', [ColorController::class, 'index']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/featured', [ProductController::class, 'featured']); // avant {slug} !
    Route::get('products/{slug}', [ProductController::class, 'show']);
    Route::get('products/{slug}/related', [ProductController::class, 'related']);

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
    });
});
