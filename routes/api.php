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

use App\Http\Controllers\Api\V1\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Api\V1\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\V1\Admin\ColorController as AdminColorController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\V1\Admin\UploadController as AdminUploadController;
use App\Http\Middleware\EnsureAdmin;

use App\Http\Controllers\Api\V1\Admin\BrideController as AdminBrideController;
use App\Http\Controllers\Api\V1\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Api\V1\Admin\PageController as AdminPageController;
use App\Http\Controllers\Api\V1\Admin\TestimonialController as AdminTestimonialController;

use App\Http\Controllers\Api\V1\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Api\V1\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Api\V1\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Api\V1\Admin\OrderController as AdminOrderController;

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
    Route::prefix('admin')->group(function (): void {
        Route::post('login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware(['auth:sanctum', EnsureAdmin::class])->group(function (): void {
            Route::post('logout', [AdminAuthController::class, 'logout']);
            Route::get('me', [AdminAuthController::class, 'me']);
            Route::get('dashboard', DashboardController::class);
            Route::get('settings', [AdminSettingController::class, 'show']);
            Route::put('settings', [AdminSettingController::class, 'update']);

            // Catalogue
            Route::apiResource('categories', AdminCategoryController::class)->except('show')->whereNumber('category');
            Route::apiResource('colors', AdminColorController::class)->except('show')->whereNumber('color');
            Route::apiResource('products', AdminProductController::class)->except('show')->whereNumber('product');

            // Contenu du site
            Route::get('home', [AdminHomeController::class, 'show']);
            Route::put('home', [AdminHomeController::class, 'update']);
            Route::get('pages/{slug}', [AdminPageController::class, 'show'])->where('slug', '[a-z0-9-]+');
            Route::put('pages/{slug}', [AdminPageController::class, 'update'])->where('slug', '[a-z0-9-]+');
            Route::apiResource('brides', AdminBrideController::class)->except('show')->whereNumber('bride');
            Route::apiResource('testimonials', AdminTestimonialController::class)->except('show')->whereNumber('testimonial');

            // Réception : commandes, rendez-vous, messages, newsletter
            Route::get('orders', [AdminOrderController::class, 'index']);
            Route::patch('orders/{number}', [AdminOrderController::class, 'update'])->where('number', '[A-Za-z0-9-]+');
            Route::get('appointments', [AdminAppointmentController::class, 'index']);
            Route::patch('appointments/{id}', [AdminAppointmentController::class, 'update'])->where('id', '[A-Za-z0-9-]+');
            Route::get('contacts', [AdminContactController::class, 'index']);
            Route::patch('contacts/{id}', [AdminContactController::class, 'update'])->whereNumber('id');
            Route::get('newsletter', [AdminNewsletterController::class, 'index']);
            Route::delete('newsletter/{id}', [AdminNewsletterController::class, 'destroy'])->whereNumber('id');
            // Envoi de photos (multipart/form-data, champ "file")
            Route::post('uploads', AdminUploadController::class)->middleware('throttle:60,1');
        });
    });
});
