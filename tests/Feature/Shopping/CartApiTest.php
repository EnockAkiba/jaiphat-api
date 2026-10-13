<?php

namespace Tests\Feature\Shopping;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartApiTest extends TestCase
{
    use RefreshDatabase;

    private const GUEST_A = 'guest-token-aaaaaaaaaa';

    private const GUEST_B = 'guest-token-bbbbbbbbbb';

    private function withGuest(string $token): array
    {
        return ['X-Guest-Token' => $token];
    }

    private function addItem(Product $product, string $size = 'M', int $quantity = 1, string $guest = self::GUEST_A)
    {
        return $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'size' => $size,
            'quantity' => $quantity,
        ], $this->withGuest($guest));
    }

    public function test_guest_token_is_required(): void
    {
        $this->getJson('/api/v1/cart')->assertStatus(400);
        $this->getJson('/api/v1/cart', ['X-Guest-Token' => 'trop-court'])->assertStatus(400);
    }

    public function test_new_guest_gets_an_empty_cart_without_creating_a_row(): void
    {
        $response = $this->getJson('/api/v1/cart', $this->withGuest(self::GUEST_A))->assertOk();

        $this->assertSame([], $response->json('data.items'));
        $this->assertSame(0, $response->json('data.items_count'));
        $this->assertEquals(0, $response->json('data.subtotal'));
        $this->assertDatabaseCount('carts', 0);
    }

    public function test_add_item_creates_cart_and_returns_totals(): void
    {
        $product = Product::factory()->create(['price' => 100]);

        $response = $this->addItem($product, 'M', 2)->assertCreated();

        $this->assertSame(2, $response->json('data.items_count'));
        $this->assertEquals(200, $response->json('data.subtotal'));
        $this->assertEquals(200, $response->json('data.items.0.line_total'));
        $this->assertSame($product->slug, $response->json('data.items.0.product.slug'));
        $this->assertDatabaseHas('cart_items', ['product_id' => $product->id, 'size' => 'M', 'qty' => 2]);
    }

    public function test_subtotal_is_exact_with_decimal_prices(): void
    {
        $product = Product::factory()->create(['price' => 19.99]);

        $response = $this->addItem($product, 'S', 3)->assertCreated();

        $this->assertEquals(59.97, $response->json('data.subtotal'));
    }

    public function test_adding_the_same_product_and_size_increments_the_quantity(): void
    {
        $product = Product::factory()->create();

        $this->addItem($product, 'M', 2)->assertCreated();
        $response = $this->addItem($product, 'M', 3)->assertCreated();

        $this->assertCount(1, $response->json('data.items'));
        $this->assertSame(5, $response->json('data.items.0.quantity'));
    }

    public function test_different_sizes_are_separate_lines(): void
    {
        $product = Product::factory()->create();

        $this->addItem($product, 'S')->assertCreated();
        $response = $this->addItem($product, 'M')->assertCreated();

        $this->assertCount(2, $response->json('data.items'));
    }

    public function test_cumulative_quantity_cannot_exceed_the_limit(): void
    {
        $product = Product::factory()->create();

        $this->addItem($product, 'M', 6)->assertCreated();
        $this->addItem($product, 'M', 6)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');

        $this->assertDatabaseHas('cart_items', ['qty' => 6]);
    }

    public function test_add_item_validates_its_input(): void
    {
        $product = Product::factory()->create();

        $this->addItem($product, 'XXL')->assertUnprocessable()->assertJsonValidationErrors('size');
        $this->addItem($product, 'M', 0)->assertUnprocessable()->assertJsonValidationErrors('quantity');
        $this->postJson('/api/v1/cart/items', ['product_id' => 99999, 'size' => 'M'], $this->withGuest(self::GUEST_A))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('product_id');
    }

    public function test_update_item_quantity(): void
    {
        $product = Product::factory()->create();
        $itemId = $this->addItem($product, 'M', 1)->json('data.items.0.id');

        $response = $this->patchJson("/api/v1/cart/items/{$itemId}", ['quantity' => 4], $this->withGuest(self::GUEST_A))
            ->assertOk();

        $this->assertSame(4, $response->json('data.items.0.quantity'));

        $this->patchJson("/api/v1/cart/items/{$itemId}", ['quantity' => 11], $this->withGuest(self::GUEST_A))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('quantity');
    }

    public function test_remove_item_and_clear_cart(): void
    {
        $products = Product::factory()->count(2)->create();
        $this->addItem($products[0])->assertCreated();
        $itemId = $this->addItem($products[1])->json('data.items.1.id');

        $afterRemove = $this->deleteJson("/api/v1/cart/items/{$itemId}", [], $this->withGuest(self::GUEST_A))->assertOk();
        $this->assertCount(1, $afterRemove->json('data.items'));

        $afterClear = $this->deleteJson('/api/v1/cart', [], $this->withGuest(self::GUEST_A))->assertOk();
        $this->assertSame([], $afterClear->json('data.items'));
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_guests_have_separate_carts(): void
    {
        $product = Product::factory()->create();
        $this->addItem($product, 'M', 1, self::GUEST_A)->assertCreated();

        $this->assertSame([], $this->getJson('/api/v1/cart', $this->withGuest(self::GUEST_B))->json('data.items'));
    }

    public function test_a_client_cannot_modify_another_clients_item(): void
    {
        $product = Product::factory()->create();
        $itemId = $this->addItem($product, 'M', 1, self::GUEST_A)->json('data.items.0.id');

        $this->patchJson("/api/v1/cart/items/{$itemId}", ['quantity' => 5], $this->withGuest(self::GUEST_B))->assertNotFound();
        $this->deleteJson("/api/v1/cart/items/{$itemId}", [], $this->withGuest(self::GUEST_B))->assertNotFound();

        $this->assertDatabaseHas('cart_items', ['id' => $itemId, 'qty' => 1]);
    }

    public function test_authenticated_user_gets_a_cart_linked_to_the_account(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        Sanctum::actingAs($user);

        // Pas d'en-tête X-Guest-Token : l'utilisateur connecté suffit
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'size' => 'M'])->assertCreated();

        $this->assertDatabaseHas('carts', ['user_id' => $user->id, 'session_id' => null]);
    }
}
