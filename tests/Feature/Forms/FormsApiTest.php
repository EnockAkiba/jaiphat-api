<?php

declare(strict_types=1);

namespace Tests\Feature\Forms;

use App\Models\Appointment;
use App\Models\Contact;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FormsApiTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'name' => 'Awa Diop',
            'email' => 'Awa@Example.com',
            'phone' => '+243970000000',
            'date' => now()->next('Monday')->toDateString(),
            'time' => '10:00',
            'party' => '2 personnes',
        ], $override);
    }

    public function test_guest_can_book_an_appointment(): void
    {
        $this->postJson('/api/v1/appointments', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.email', 'awa@example.com')
            ->assertJsonPath('data.time', '10:00');

        $this->assertDatabaseCount('appointments', 1);
        $this->assertNull(Appointment::first()->user_id);
    }

    public function test_connected_user_booking_is_linked_to_account(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/appointments', $this->payload())->assertCreated();

        $this->assertSame($user->id, Appointment::first()->user_id);
    }

    public function test_same_slot_cannot_be_booked_twice(): void
    {
        $this->postJson('/api/v1/appointments', $this->payload())->assertCreated();

        $this->postJson('/api/v1/appointments', $this->payload(['name' => 'Autre']))
            ->assertStatus(422)->assertJsonValidationErrors('time');
    }

    public function test_invalid_bookings_are_rejected(): void
    {
        $this->postJson('/api/v1/appointments', $this->payload(['date' => now()->next('Sunday')->toDateString()]))
            ->assertStatus(422)->assertJsonValidationErrors('date');
        $this->postJson('/api/v1/appointments', $this->payload(['date' => now()->toDateString()]))
            ->assertStatus(422)->assertJsonValidationErrors('date');
        $this->postJson('/api/v1/appointments', $this->payload(['time' => '03:00']))
            ->assertStatus(422)->assertJsonValidationErrors('time');
        $this->postJson('/api/v1/appointments', $this->payload(['website' => 'http://spam']))
            ->assertStatus(422)->assertJsonValidationErrors('website');
    }

    public function test_availability_marks_taken_slots(): void
    {
        $date = now()->next('Monday')->toDateString();
        $this->postJson('/api/v1/appointments', $this->payload(['date' => $date]))->assertCreated();

        $res = $this->getJson("/api/v1/appointments/availability?date={$date}")->assertOk();
        $slots = collect($res->json('data.slots'))->keyBy('time');

        $this->assertFalse($slots['10:00']['available']);
        $this->assertTrue($slots['11:00']['available']);
    }

    public function test_user_lists_and_cancels_only_own_appointments(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        Sanctum::actingAs($owner);
        $id = $this->postJson('/api/v1/appointments', $this->payload())->json('data.id');
        $this->getJson('/api/v1/appointments')->assertOk()->assertJsonCount(1, 'data');

        Sanctum::actingAs($other);
        $this->deleteJson("/api/v1/appointments/{$id}")->assertNotFound();

        Sanctum::actingAs($owner);
        $this->deleteJson("/api/v1/appointments/{$id}")->assertOk()->assertJsonPath('data.status', 'cancelled');

        // le créneau est libéré
        $this->postJson('/api/v1/appointments', $this->payload())->assertCreated();
    }

    public function test_my_appointments_requires_authentication(): void
    {
        $this->getJson('/api/v1/appointments')->assertUnauthorized();
    }

    public function test_contact_message_is_stored(): void
    {
        $this->postJson('/api/v1/contact', ['name' => 'Awa', 'email' => 'awa@example.com', 'message' => 'Bonjour, une question sur une robe.'])
            ->assertCreated();

        $this->assertSame(1, Contact::count());
    }

    public function test_contact_validation(): void
    {
        $this->postJson('/api/v1/contact', ['name' => '', 'email' => 'pas-un-email', 'message' => 'court'])
            ->assertStatus(422)->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_newsletter_is_idempotent(): void
    {
        $this->postJson('/api/v1/newsletter', ['email' => 'Awa@Example.com'])->assertCreated();
        $this->postJson('/api/v1/newsletter', ['email' => 'awa@example.com'])->assertOk();

        $this->assertSame(1, NewsletterSubscriber::count());
        $this->assertSame('awa@example.com', NewsletterSubscriber::first()->email);
    }
}
