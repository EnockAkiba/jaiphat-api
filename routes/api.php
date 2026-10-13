<?php

use App\Http\Controllers\Api\V1\Catalog\CategoryController;
use App\Http\Controllers\Api\V1\Catalog\ColorController;
use App\Http\Controllers\Api\V1\Catalog\ProductController;
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
});
