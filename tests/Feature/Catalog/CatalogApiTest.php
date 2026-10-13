<?php

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_products(): void
    {
        Product::factory()->count(3)->create();

        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'id', 'slug', 'name', 'price', 'excerpt', 'cover',
                    'category' => ['slug', 'name'],
                    'color' => ['slug', 'name', 'swatch'],
                ]],
                'links',
                'meta',
            ]);
    }

    public function test_index_can_filter_by_category(): void
    {
        $dresses = Category::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $dresses->id]);
        Product::factory()->create(); // autre catégorie

        $this->getJson('/api/v1/products?category='.$dresses->slug)
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_index_can_filter_by_color(): void
    {
        $ivory = Color::factory()->create();
        Product::factory()->create(['color_id' => $ivory->id]);
        Product::factory()->count(2)->create();

        $this->getJson('/api/v1/products?color='.$ivory->slug)
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_index_can_filter_accessories(): void
    {
        Product::factory()->accessory()->count(2)->create();
        Product::factory()->create();

        $this->getJson('/api/v1/products?accessory=1')->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/products?accessory=0')->assertJsonCount(1, 'data');
    }

    public function test_index_can_search_by_name(): void
    {
        Product::factory()->create(['name' => 'Robe Aurore']);
        Product::factory()->create(['name' => 'Voile Celeste']);

        $this->getJson('/api/v1/products?q=Aurore')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Robe Aurore');
    }

    public function test_index_can_sort_by_price(): void
    {
        Product::factory()->create(['price' => 300]);
        Product::factory()->create(['price' => 100]);
        Product::factory()->create(['price' => 200]);

        $asc = $this->getJson('/api/v1/products?sort=price_asc')->json('data.*.price');
        $desc = $this->getJson('/api/v1/products?sort=price_desc')->json('data.*.price');

        $this->assertEquals([100, 200, 300], $asc);
        $this->assertEquals([300, 200, 100], $desc);
    }

    public function test_index_rejects_invalid_parameters(): void
    {
        $this->getJson('/api/v1/products?sort=nope')->assertUnprocessable()->assertJsonValidationErrors('sort');
        $this->getJson('/api/v1/products?category=unknown')->assertUnprocessable()->assertJsonValidationErrors('category');
        $this->getJson('/api/v1/products?per_page=500')->assertUnprocessable()->assertJsonValidationErrors('per_page');
    }

    public function test_content_follows_requested_locale(): void
    {
        Product::factory()->create();

        $this->getJson('/api/v1/products?lang=fr')->assertJsonPath('data.0.excerpt', 'Extrait fr');
        $this->getJson('/api/v1/products?lang=en')->assertJsonPath('data.0.excerpt', 'Extrait en');
        $this->getJson('/api/v1/products', ['Accept-Language' => 'fr-FR,fr;q=0.9'])
            ->assertJsonPath('data.0.excerpt', 'Extrait fr');
    }

    public function test_missing_translation_falls_back_to_fallback_locale(): void
    {
        $product = Product::factory()->create();
        $product->translations()->where('locale', 'fr')->delete();

        $fallback = config('app.fallback_locale'); // 'en' par défaut

        $this->getJson('/api/v1/products?lang=fr')
            ->assertJsonPath('data.0.excerpt', "Extrait {$fallback}");
    }

    public function test_show_returns_product_detail(): void
    {
        $product = Product::factory()->create();

        $this->getJson('/api/v1/products/'.$product->slug.'?lang=fr')
            ->assertOk()
            ->assertJsonPath('data.slug', $product->slug)
            ->assertJsonPath('data.description', 'Description fr')
            ->assertJsonPath('data.sizes', ['S', 'M'])
            ->assertJsonCount(1, 'data.images');
    }

    public function test_show_returns_json_404_for_unknown_slug(): void
    {
        $this->getJson('/api/v1/products/does-not-exist')->assertNotFound();
    }

    public function test_featured_returns_only_featured_products_up_to_the_limit(): void
    {
        Product::factory()->featured()->count(10)->create();
        Product::factory()->count(2)->create();

        $this->getJson('/api/v1/products/featured')
            ->assertOk()
            ->assertJsonCount(8, 'data');
    }

    public function test_related_returns_same_category_without_the_product_itself(): void
    {
        $category = Category::factory()->create();
        $products = Product::factory()->count(3)->create(['category_id' => $category->id]);
        Product::factory()->create(); // autre catégorie

        $this->getJson('/api/v1/products/'.$products[0]->slug.'/related')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_categories_and_colors_are_listed(): void
    {
        Category::factory()->count(2)->create();
        Color::factory()->count(3)->create();

        $this->getJson('/api/v1/categories')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/v1/colors')->assertOk()->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['slug', 'name', 'swatch', 'image']]]);
    }
}
