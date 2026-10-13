<?php

namespace Tests\Feature\Shopping;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WishlistApiTest extends TestCase
{
    use RefreshDatabase;

    private const GUEST_A = 'guest-token-aaaaaaaaaa';

    private const GUEST_B = 'guest-token-bbbbbbbbbb';

    private function withGuest(string $token): array
    {
        return ['X-Guest-Token' => $token];
    }

    private function add(Product $product, string $guest = self::GUEST_A)
    {
        return $this->postJson('/api/v1/wishlist/items', ['product_id' => $product->id], $this->withGuest($guest));
    }

    public function test_guest_token_is_required(): void
    {
        $this->getJson('/api/v1/wishlist')->assertStatus(400);
    }

    public function test_new_guest_gets_an_empty_wishlist(): void
    {
        $response = $this->getJson('/api/v1/wishlist', $this->withGuest(self::GUEST_A))->assertOk();

        $this->assertSame(0, $response->json('data.count'));
        $this->assertSame([], $response->json('data.products'));
        $this->assertDatabaseCount('wishlists', 0);
    }

    public function test_add_product_to_wishlist(): void
    {
        $product = Product::factory()->create();

        $response = $this->add($product)->assertCreated();

        $this->assertSame(1, $response->json('data.count'));
        $this->assertSame($product->slug, $response->json('data.products.0.slug'));
    }

    public function test_adding_twice_does_not_create_duplicates(): void
    {
        $product = Product::factory()->create();

        $this->add($product)->assertCreated();
        $response = $this->add($product)->assertCreated();

        $this->assertSame(1, $response->json('data.count'));
        $this->assertDatabaseCount('wishlist_items', 1);
    }

    public function test_most_recently_added_product_comes_first(): void
    {
        $first = Product::factory()->create();
        $second = Product::factory()->create();

        $this->add($first)->assertCreated();
        $response = $this->add($second)->assertCreated();

        $this->assertSame($second->slug, $response->json('data.products.0.slug'));
        $this->assertSame($first->slug, $response->json('data.products.1.slug'));
    }

    public function test_remove_product(): void
    {
        $product = Product::factory()->create();
        $this->add($product)->assertCreated();

        $response = $this->deleteJson("/api/v1/wishlist/items/{$product->id}", [], $this->withGuest(self::GUEST_A))->assertOk();
        $this->assertSame(0, $response->json('data.count'));

        // Retirer un produit absent est sans effet (pas d'erreur)
        $this->deleteJson("/api/v1/wishlist/items/{$product->id}", [], $this->withGuest(self::GUEST_A))->assertOk();
    }

    public function test_unknown_product_is_rejected(): void
    {
        $this->postJson('/api/v1/wishlist/items', ['product_id' => 99999], $this->withGuest(self::GUEST_A))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('product_id');
    }

    public function test_guests_have_separate_wishlists(): void
    {
        $this->add(Product::factory()->create(), self::GUEST_A)->assertCreated();

        $this->assertSame(0, $this->getJson('/api/v1/wishlist', $this->withGuest(self::GUEST_B))->json('data.count'));
    }

    public function test_authenticated_user_gets_a_wishlist_linked_to_the_account(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/wishlist/items', ['product_id' => Product::factory()->create()->id])->assertCreated();

        $this->assertDatabaseHas('wishlists', ['user_id' => $user->id, 'session_id' => null]);
    }
}
