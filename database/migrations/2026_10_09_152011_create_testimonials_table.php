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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('testimonial_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('testimonial_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 2);
            $table->text('quote');
            $table->unique(['testimonial_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonial_translations');
        Schema::dropIfExists('testimonials');
    }
};
