<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool { return true; } // accès géré par EnsureAdmin

    public function rules(): array
    {
        return [
            'brand' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'phoneHref' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'freeShipping' => ['required', 'numeric', 'min:0'],
            'instagram' => ['required', 'string', 'max:255'],
            'translations' => ['required', 'array'],
            'translations.*.tagline' => ['nullable', 'string', 'max:255'],
            'translations.*.shippingNote' => ['nullable', 'string', 'max:255'],
            'translations.*.hours' => ['nullable', 'string', 'max:255'],
        ];
    }
}