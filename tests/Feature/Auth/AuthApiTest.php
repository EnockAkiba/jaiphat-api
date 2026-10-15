<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    private const PASSWORD = 'motdepasse1';

    private function register(array $overrides = [])
    {
        return $this->postJson('/api/v1/auth/register', array_merge([
            'name' => 'Awa Diop',
            'email' => 'awa@example.com',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
        ], $overrides));
    }

    public function test_register_creates_an_account_and_returns_a_working_token(): void
    {
        $response = $this->register()->assertCreated();

        $this->assertSame('awa@example.com', $response->json('data.user.email'));
        $this->assertSame('Bearer', $response->json('data.token_type'));
        $this->assertNotEmpty($response->json('data.token'));
        $this->assertArrayNotHasKey('password', $response->json('data.user'));

        $user = User::where('email', 'awa@example.com')->firstOrFail();
        $this->assertNotSame(self::PASSWORD, $user->password);   // jamais stocké en clair
        $this->assertTrue(Hash::check(self::PASSWORD, $user->password));

        $this->withToken($response->json('data.token'))
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', 'awa@example.com');
    }

    public function test_register_normalizes_the_email(): void
    {
        $this->register(['email' => '  AWA@Example.COM '])->assertCreated();

        $this->assertDatabaseHas('users', ['email' => 'awa@example.com']);
        $this->register(['email' => 'awa@example.com'])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_register_validates_its_input(): void
    {
        $this->register(['email' => 'pas-un-email'])->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->register(['password' => 'court', 'password_confirmation' => 'court'])
            ->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->register(['password_confirmation' => 'autre-chose'])
            ->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->register(['name' => ''])->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_login_returns_a_token_named_after_the_device(): void
    {
        User::factory()->create(['email' => 'awa@example.com', 'password' => self::PASSWORD]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'awa@example.com',
            'password' => self::PASSWORD,
            'device_name' => 'iphone',
        ])->assertOk();

        $this->assertNotEmpty($response->json('data.token'));
        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'iphone']);
    }

    public function test_login_failures_do_not_reveal_whether_the_email_exists(): void
    {
        User::factory()->create(['email' => 'awa@example.com', 'password' => self::PASSWORD]);

        $wrongPassword = $this->postJson('/api/v1/auth/login', ['email' => 'awa@example.com', 'password' => 'faux-mot-de-passe'])
            ->assertUnprocessable()->assertJsonValidationErrors('email');
        $unknownEmail = $this->postJson('/api/v1/auth/login', ['email' => 'inconnu@example.com', 'password' => self::PASSWORD])
            ->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->assertSame($wrongPassword->json('errors.email'), $unknownEmail->json('errors.email'));
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
        $this->withToken('1|jeton-bidon')->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_me_returns_the_authenticated_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_logout_revokes_only_the_current_token(): void
    {
        $user = User::factory()->create(['password' => self::PASSWORD]);
        $user->createToken('autre-appareil');

        $token = $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => self::PASSWORD])
            ->json('data.token');
        $this->assertDatabaseCount('personal_access_tokens', 2);

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertDatabaseHas('personal_access_tokens', ['name' => 'autre-appareil']);
    }
}
