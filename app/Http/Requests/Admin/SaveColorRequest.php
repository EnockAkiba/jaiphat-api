<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:255'],
            'swatch' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'image' => ['required', 'string', 'max:2048'],
            'translations' => ['required', 'array'],
            'translations.en.name' => ['required', 'string', 'max:255'],
            'translations.fr.name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return ['swatch.regex' => 'La couleur doit être au format #RRGGBB.'];
    }
}
