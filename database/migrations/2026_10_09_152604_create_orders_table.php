<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->timestamps();
        });

        // Schema::create('wishlist_items', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('wishlist_id')->constrained()->cascadeOnDelete();
        //     $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        //     $table->timestamps();
        //     $table->unique(['wishlist_id', 'product_id']);
        // });
    }

    public function down(): void
    {
        // Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('wishlists');
    }
};
