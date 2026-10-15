<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

final class SettingAdminService
{
    public function get(): Setting
    {
        return Setting::query()->with('translations')->firstOrFail();
    }

    public function update(array $data): Setting
    {
        return DB::transaction(function () use ($data): Setting {
            $setting = Setting::query()->firstOrFail();

            $setting->update([
                'brand' => $data['brand'],
                'phone' => $data['phone'],
                'phone_href' => $data['phoneHref'],
                'email' => $data['email'],
                'address' => $data['address'],
                'free_shipping' => $data['freeShipping'],
                'instagram' => $data['instagram'],
            ]);

            foreach (['en', 'fr'] as $locale) {
                $t = $data['translations'][$locale] ?? null;

                if ($t !== null) {
                    $setting->translations()->updateOrCreate(['locale' => $locale], [
                        'tagline' => $t['tagline'] ?? '',
                        'shipping_note' => $t['shippingNote'] ?? '',
                        'hours' => $t['hours'] ?? '',
                    ]);
                }
            }

            return $setting->load('translations');
        });
    }
}