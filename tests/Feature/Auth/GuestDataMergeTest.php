<?php

namespace Tests\Feature\Auth;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestDataMergeTest extends TestCase
{
    use RefreshDatabase;

    private const GUEST = 'guest-token-aaaaaaaaaa';

    private const PASSWORD = 'motdepasse1';

    private function guestAddsToCart(Product $product, int $quantity): void
    {
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'size' => 'M', 'quantity' => $quantity],
            ['X-Guest-Token' => self::GUEST])->assertCreated();
    }

    private function guestAddsToWishlist(Product $product): void
    {
        $this->postJson('/api/v1/wishlist/items', ['product_id' => $product->id],
            ['X-Guest-Token' => self::GUEST])->assertCreated();
    }

    public function test_register_moves_the_guest_cart_and_wishlist_to_the_new_account(): void
    {
        $product = Product::factory()->create();
        $this->guestAddsToCart($product, 2);
        $this->guestAddsToWishlist($product);

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Awa Diop',
            'email' => 'awa@example.com',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
        ], ['X-Guest-Token' => self::GUEST])->assertCreated();

        $user = User::where('email', 'awa@example.com')->firstOrFail();

        // Le compte a récupéré le panier et la wishlist...
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => Cart::where('user_id', $user->id)->value('id'),
            'product_id' => $product->id, 'size' => 'M', 'qty' => 2,
        ]);
        $this->assertDatabaseHas('wishlist_items', ['product_id' => $product->id]);
        $this->assertDatabaseHas('wishlists', ['user_id' => $user->id]);

        // ...et l'invité n'a plus rien.
        $this->assertDatabaseMissing('carts', ['session_id' => self::GUEST]);
        $this->assertDatabaseMissing('wishlists', ['session_id' => self::GUEST]);
    }

    public function test_login_adds_quantities_together_up_to_the_limit(): void
    {
        $product = Product::factory()->create();
        $user = User::factory()->create(['email' => 'awa@example.com', 'password' => self::PASSWORD]);

        // L'utilisateur avait déjà 8 articles dans son compte...
        $userCart = Cart::create(['user_id' => $user->id]);
        $userCart->items()->create(['product_id' => $product->id, 'size' => 'M', 'qty' => 8]);

        // ...et l'invité en ajoute 5 : 8 + 5 = 13, plafonné à 10.
        $this->guestAddsToCart($product, 5);

        $this->postJson('/api/v1/auth/login', ['email' => 'awa@example.com', 'password' => self::PASSWORD],
            ['X-Guest-Token' => self::GUEST])->assertOk();

        $this->assertDatabaseHas('cart_items', ['cart_id' => $userCart->id, 'product_id' => $product->id, 'qty' => 10]);
        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseMissing('carts', ['session_id' => self::GUEST]);
    }

    public function test_login_without_guest_data_or_with_a_malformed_token_still_works(): void
    {
        User::factory()->create(['email' => 'awa@example.com', 'password' => self::PASSWORD]);
        $credentials = ['email' => 'awa@example.com', 'password' => self::PASSWORD];

        $this->postJson('/api/v1/auth/login', $credentials)->assertOk();
        $this->postJson('/api/v1/auth/login', $credentials, ['X-Guest-Token' => 'x'])->assertOk();
        $this->postJson('/api/v1/auth/login', $credentials, ['X-Guest-Token' => str_repeat('a', 200)])->assertOk();
    }
}
