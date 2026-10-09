<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('phone');
            $table->string('phone_href');
            $table->string('email');
            $table->string('address');
            $table->decimal('free_shipping', 8, 2)->default(150);
            $table->string('instagram');
            $table->timestamps();

        });
        Schema::create('setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setting_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 2);
            $table->string('tagline');
            $table->string('shipping_note');
            $table->string('hours');
            $table->unique(['setting_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_translations');
        Schema::dropIfExists('settings');
    }
};
