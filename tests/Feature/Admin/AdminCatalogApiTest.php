<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminCatalogApiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): void
    {
        Sanctum::actingAs(User::factory()->create(['is_admin' => true]));
    }

    private function category(string $name = 'Robes'): array
    {
        return $this->postJson('/api/v1/admin/categories', [
            'menu' => 'shop',
            'translations' => ['en' => ['name' => $name], 'fr' => ['name' => $name.' FR']],
        ])->assertCreated()->json('data');
    }

    private function color(string $name = 'Ivory'): array
    {
        return $this->postJson('/api/v1/admin/colors', [
            'swatch' => '#F5EFE6', 'image' => '/images/ivory.jpg',
            'translations' => ['en' => ['name' => $name], 'fr' => ['name' => 'Ivoire']],
        ])->assertCreated()->json('data');
    }

    private function productPayload(string $category, string $color, array $override = []): array
    {
        return array_merge([
            'name' => 'Robe Aurore', 'price' => 1250.5, 'category' => $category, 'color' => $color,
            'accessory' => false, 'featured' => true, 'badge' => 'New',
            'images' => ['/images/a.jpg', '/images/b.jpg'],
            'sizes' => [],
            'translations' => [
                'en' => ['excerpt' => 'Soft lace', 'description' => 'A long description'],
                'fr' => ['excerpt' => 'Dentelle douce', 'description' => 'Une longue description'],
            ],
        ], $override);
    }

    public function test_admin_routes_require_an_admin(): void
    {
        $this->getJson('/api/v1/admin/products')->assertUnauthorized();

        Sanctum::actingAs(User::factory()->create(['is_admin' => false]));
        $this->getJson('/api/v1/admin/products')->assertForbidden();
    }

    public function test_category_crud_and_delete_protection(): void
    {
        $this->admin();
        $cat = $this->category();
        $this->assertSame('robes', $cat['slug']);
        $this->assertSame('Robes FR', $cat['translations']['fr']['name']);

        $this->putJson("/api/v1/admin/categories/{$cat['id']}", [
            'menu' => 'collections', 'translations' => ['en' => ['name' => 'Gowns'], 'fr' => ['name' => 'Robes']],
        ])->assertOk()->assertJsonPath('data.menu', 'collections')->assertJsonPath('data.name', 'Gowns');

        $this->getJson('/api/v1/admin/categories')->assertOk()->assertJsonCount(1, 'data');

        $color = $this->color();
        $this->postJson('/api/v1/admin/products', $this->productPayload($cat['slug'], $color['slug']))->assertCreated();
        $this->deleteJson("/api/v1/admin/categories/{$cat['id']}")->assertStatus(422)->assertJsonValidationErrors('category');
    }

    public function test_color_validation(): void
    {
        $this->admin();
        $this->postJson('/api/v1/admin/colors', ['swatch' => 'rouge', 'image' => '', 'translations' => ['en' => ['name' => '']]])
            ->assertStatus(422)->assertJsonValidationErrors(['swatch', 'image', 'translations.en.name']);
    }

    public function test_product_create_update_and_shape(): void
    {
        $this->admin();
        $cat = $this->category();
        $color = $this->color();

        $created = $this->postJson('/api/v1/admin/products', $this->productPayload($cat['slug'], $color['slug']))
            ->assertCreated()
            ->assertJsonPath('data.slug', 'robe-aurore')
            ->assertJsonPath('data.price', 1250.5)
            ->assertJsonPath('data.image', '/images/a.jpg')
            ->assertJsonPath('data.translations.fr.excerpt', 'Dentelle douce')
            ->assertJsonPath('data.sizes.0', '0')          // tailles de robe par défaut
            ->json('data');

        $this->putJson("/api/v1/admin/products/{$created['id']}", $this->productPayload($cat['slug'], $color['slug'], [
            'images' => ['/images/c.jpg'], 'accessory' => true, 'name' => 'Robe Aurore 2',
        ]))->assertOk()
            ->assertJsonCount(1, 'data.images')
            ->assertJsonPath('data.sizes', ['OS'])         // accessoire : taille unique
            ->assertJsonPath('data.name', 'Robe Aurore 2');

        $this->getJson('/api/v1/admin/products')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_product_slug_must_be_unique_and_refs_must_exist(): void
    {
        $this->admin();
        $cat = $this->category();
        $color = $this->color();
        $this->postJson('/api/v1/admin/products', $this->productPayload($cat['slug'], $color['slug']))->assertCreated();

        $this->postJson('/api/v1/admin/products', $this->productPayload($cat['slug'], $color['slug']))
            ->assertStatus(422)->assertJsonValidationErrors('slug');
        $this->postJson('/api/v1/admin/products', $this->productPayload('inconnue', 'inconnue', ['name' => 'Autre']))
            ->assertStatus(422)->assertJsonValidationErrors(['category', 'color']);
        $this->postJson('/api/v1/admin/products', $this->productPayload($cat['slug'], $color['slug'], ['name' => 'Sans photo', 'images' => []]))
            ->assertStatus(422)->assertJsonValidationErrors('images');
    }

    public function test_product_can_be_deleted(): void
    {
        $this->admin();
        $p = $this->postJson('/api/v1/admin/products', $this->productPayload($this->category()['slug'], $this->color()['slug']))->json('data');

        $this->deleteJson("/api/v1/admin/products/{$p['id']}")->assertOk();
        $this->getJson('/api/v1/admin/products')->assertJsonCount(0, 'data');
    }

    public function test_admin_uploads_an_image_and_url_round_trips(): void
    {
        Storage::fake('public');
        $this->admin();

        $res = $this->postJson('/api/v1/admin/uploads', ['file' => UploadedFile::fake()->image('robe.jpg', 600, 800)])
            ->assertCreated()->assertJsonStructure(['data' => ['path', 'url']]);

        Storage::disk('public')->assertExists($res->json('data.path'));

        // le front renvoie l'URL reçue : en base on ne garde que le chemin relatif
        $cat = $this->category();
        $this->postJson('/api/v1/admin/colors', [
            'swatch' => '#ffffff', 'image' => $res->json('data.url'),
            'translations' => ['en' => ['name' => 'Snow']],
        ])->assertCreated();
        $this->assertDatabaseHas('colors', ['slug' => 'snow', 'image' => $res->json('data.path')]);
    }

    public function test_upload_rejects_non_images(): void
    {
        Storage::fake('public');
        $this->admin();

        $this->postJson('/api/v1/admin/uploads', ['file' => UploadedFile::fake()->create('virus.php', 10, 'text/x-php')])
            ->assertStatus(422)->assertJsonValidationErrors('file');
    }
}
