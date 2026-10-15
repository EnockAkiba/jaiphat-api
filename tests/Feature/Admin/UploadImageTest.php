<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_photo_est_convertie_en_webp_et_reduite(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->image('robe.jpg', 3000, 2000),
        ])->assertCreated();

        $path = $response->json('data.path');

        $this->assertStringEndsWith('.webp', $path);
        Storage::disk('public')->assertExists($path);

        [$width] = getimagesize(Storage::disk('public')->path($path));
        $this->assertLessThanOrEqual(2000, $width);
    }
}