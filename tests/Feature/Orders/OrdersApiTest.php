<?php

declare(strict_types=1);

namespace Tests\Feature\Orders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdersApiTest extends TestCase
{
    use RefreshDatabase;

    private const GUEST_A = 'guest-token-aaaaaaaaaa';

    private const GUEST_B = 'guest-token-bbbbbbbbbb';

    private function h(string $guest): array
    {
        return ['X-Guest-Token' => $guest];
    }

    private function fillCart(string $guest = self::GUEST_A, int $price = 100, int $qty = 2): Product
    {
        $product = Product::factory()->create(['price' => $price]);
        $this->postJson('/api/v1/cart/items', ['product_id' => $product->id, 'size' => 'M', 'quantity' => $qty], $this->h($guest))->assertCreated();

        return $product;
    }

    private function checkout(string $guest = self::GUEST_A, array $override = [])
    {
        return $this->postJson('/api/v1/orders', array_merge([
            'customer_name' => 'Awa Diop',
            'customer_email' => 'Awa@Example.com',
            'customer_phone' => '+243970000000',
            'address' => '12 avenue de la Paix',
            'city' => 'Kinshasa',
            'district' => 'Gombe',
        ], $override), $this->h($guest));
    }

    public function test_guest_places_an_order_and_cart_is_emptied(): void
    {
        $this->fillCart(price: 100, qty: 2);

        $this->checkout()
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.subtotal', 200)
            ->assertJsonPath('data.shipping_pending', true)
            ->assertJsonPath('data.shipping_fee', null)
            ->assertJsonPath('data.total', null)
            ->assertJsonPath('data.customer.email', 'awa@example.com')
            ->assertJsonCount(1, 'data.items');

        $this->getJson('/api/v1/cart', $this->h(self::GUEST_A))->assertJsonPath('data.items_count', 0);
        $this->assertNull(Order::first()->user_id);
    }

    public function test_empty_cart_cannot_be_checked_out(): void
    {
        $this->checkout()->assertStatus(422)->assertJsonValidationErrors('cart');
    }

    public function test_address_is_validated(): void
    {
        $this->fillCart();

        $this->checkout(override: ['address' => '', 'city' => '', 'latitude' => 5])
            ->assertStatus(422)->assertJsonValidationErrors(['address', 'city', 'longitude']);
    }

    public function test_order_keeps_the_price_paid_even_if_product_changes(): void
    {
        $product = $this->fillCart(price: 100, qty: 1);
        $number = $this->checkout()->json('data.number');

        $product->update(['price' => 999]);

        $this->getJson("/api/v1/orders/{$number}", $this->h(self::GUEST_A))
            ->assertOk()
            ->assertJsonPath('data.items.0.unit_price', 100)
            ->assertJsonPath('data.subtotal', 100);
    }

    public function test_guest_cannot_see_someone_elses_order(): void
    {
        $this->fillCart();
        $number = $this->checkout()->json('data.number');

        $this->getJson("/api/v1/orders/{$number}", $this->h(self::GUEST_B))->assertNotFound();
        $this->getJson('/api/v1/orders', $this->h(self::GUEST_B))->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/orders', $this->h(self::GUEST_A))->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_shipping_quote_command_confirms_the_order(): void
    {
        $this->fillCart(price: 100, qty: 2);
        $number = $this->checkout()->json('data.number');

        $this->artisan('orders:quote', ['number' => $number, 'amount' => '12.50'])->assertSuccessful();

        $this->getJson("/api/v1/orders/{$number}", $this->h(self::GUEST_A))
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.shipping_pending', false)
            ->assertJsonPath('data.shipping_fee', 12.5)
            ->assertJsonPath('data.total', 212.5);
    }

    public function test_pending_order_can_be_cancelled_but_not_a_confirmed_one(): void
    {
        $this->fillCart();
        $number = $this->checkout()->json('data.number');

        $this->postJson("/api/v1/orders/{$number}/cancel", [], $this->h(self::GUEST_A))
            ->assertOk()->assertJsonPath('data.status', 'cancelled');

        $this->fillCart();
        $second = $this->checkout()->json('data.number');
        Order::where('number', $second)->update(['status' => OrderStatus::Confirmed]);

        $this->postJson("/api/v1/orders/{$second}/cancel", [], $this->h(self::GUEST_A))
            ->assertStatus(422)->assertJsonValidationErrors('order');
    }

    public function test_guest_orders_join_the_account_on_register(): void
    {
        $this->fillCart();
        $this->checkout()->assertCreated();

        $this->postJson('/api/v1/auth/register', [
            'name' => 'Awa Diop', 'email' => 'awa.new@example.com',
            'password' => 'motdepasse1', 'password_confirmation' => 'motdepasse1', 'device_name' => 'tests',
        ], $this->h(self::GUEST_A))->assertCreated();

        $order = Order::first();
        $this->assertNotNull($order->user_id);
        $this->assertNull($order->session_id);
    }
}
