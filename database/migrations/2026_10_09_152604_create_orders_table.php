<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();                       // numéro public : JP-261015-K3M9Q
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable()->index();        // jeton invité (même nom que carts/wishlists)

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 30);

            $table->string('address');
            $table->string('city', 100);
            $table->string('district', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();           // aide à estimer la distance
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('subtotal_cents');
            $table->unsignedBigInteger('shipping_cents')->nullable(); // null = frais à confirmer
            $table->unsignedBigInteger('total_cents')->nullable();    // null tant que la livraison n'est pas chiffrée

            $table->string('status', 20)->default('pending')->index();
            $table->string('payment_status', 20)->default('unpaid');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            // Instantané : le nom et le prix au moment de l'achat ne changent jamais, même si le produit est modifié.
            $table->string('product_name');
            $table->string('size', 20);
            $table->unsignedBigInteger('unit_price_cents');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('line_total_cents');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
