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
        Schema::create('home_contents', function (Blueprint $table) {
            $table->id();
            $table->string('hero_image');
            $table->string('hero_detail_image');
            $table->string('salon_image');
            $table->string('hero_primary_to')->default('/shop');
            $table->string('hero_secondary_to')->default('/appointment');
            $table->unsignedInteger('stat_brides')->default(10000);
            $table->decimal('stat_rating', 2, 1)->default(4.9);
            $table->unsignedInteger('stat_reviews')->default(2500);
            $table->timestamps();
        });

        Schema::create('home_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_content_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 2);
            $table->string('hero_kicker');
            $table->string('hero_title');
            $table->string('hero_script');
            $table->text('hero_text');
            $table->string('band_title');
            $table->unique(['home_content_id', 'locale']);
        });

        Schema::create('home_services', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 32);
            $table->unsignedSmallInteger('sort_order')->default(0);
        });

        Schema::create('home_service_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_service_id')->constrained('home_services')->cascadeOnDelete();
            $table->string('locale', 2);
            $table->string('title');
            $table->string('text');
            $table->unique(['home_service_id', 'locale']);
        });

        Schema::create('home_band_items', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 32);
            $table->unsignedSmallInteger('sort_order')->default(0);
        });

        Schema::create('home_band_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_band_item_id')->constrained('home_band_items')->cascadeOnDelete();
            $table->string('locale', 2);
            $table->string('title');
            $table->string('text');
            $table->unique(['home_band_item_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_band_item_translations');
        Schema::dropIfExists('home_band_items');
        Schema::dropIfExists('home_service_translations');
        Schema::dropIfExists('home_services');
        Schema::dropIfExists('home_setting_translations');
        Schema::dropIfExists('home_settings');
    }
};
