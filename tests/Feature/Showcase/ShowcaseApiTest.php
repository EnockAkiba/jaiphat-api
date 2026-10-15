<?php

declare(strict_types=1);

namespace Tests\Feature\Showcase;

use Database\Seeders\ShowcaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowcaseApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_returns_every_section(): void
    {
        $this->seed(ShowcaseSeeder::class);

        $this->getJson('/api/v1/home')
            ->assertOk()
            ->assertJsonStructure(['data' => [
                'hero' => ['kicker', 'title', 'script', 'text', 'image', 'primary_to', 'secondary_to'],
                'stats' => ['brides', 'rating', 'reviews'],
                'band' => ['title', 'items'],
                'services', 'featured_products', 'testimonials', 'brides',
            ]])
            ->assertJsonCount(3, 'data.services')
            ->assertJsonCount(3, 'data.testimonials');
    }

    public function test_home_is_translated_and_falls_back_to_french(): void
    {
        $this->seed(ShowcaseSeeder::class);

        $this->getJson('/api/v1/home?lang=en')->assertJsonPath('data.hero.title', 'The day everything begins');
        $this->getJson('/api/v1/home?lang=fr')->assertJsonPath('data.hero.title', 'Le jour où tout commence');
    }

    public function test_home_is_404_without_content(): void
    {
        $this->getJson('/api/v1/home')->assertNotFound();
    }

    public function test_settings(): void
    {
        $this->seed(ShowcaseSeeder::class);

        $this->getJson('/api/v1/settings?lang=en')
            ->assertOk()
            ->assertJsonPath('data.brand', 'Jaiphat')
            ->assertJsonPath('data.free_shipping', 150)
            ->assertJsonPath('data.tagline', 'The gown of your dreams, made for you.');
    }

    public function test_page_by_slug_and_unknown_slug(): void
    {
        $this->seed(ShowcaseSeeder::class);

        $this->getJson('/api/v1/pages/about')
            ->assertOk()
            ->assertJsonPath('data.slug', 'about')
            ->assertJsonCount(2, 'data.paragraphs');

        $this->getJson('/api/v1/pages/inconnue')->assertNotFound();
    }

    public function test_testimonials_and_brides_lists(): void
    {
        $this->seed(ShowcaseSeeder::class);

        $this->getJson('/api/v1/testimonials')->assertOk()->assertJsonCount(3, 'data');
        $this->getJson('/api/v1/brides')->assertOk()->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'gown_name', 'image', 'quote', 'product_slug']]]);
    }

    public function test_seeder_can_run_twice_without_duplicates(): void
    {
        $this->seed(ShowcaseSeeder::class);
        $this->seed(ShowcaseSeeder::class);

        $this->getJson('/api/v1/testimonials')->assertJsonCount(3, 'data');
    }
}
