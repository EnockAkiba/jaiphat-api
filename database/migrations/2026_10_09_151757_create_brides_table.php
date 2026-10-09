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
        Schema::create('brides', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gown_name');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('bride_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bride_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 2);
            $table->text('quote');
            $table->unique(['bride_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bride_translations');
        Schema::dropIfExists('brides');
    }
};
