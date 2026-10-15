<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveBrideRequest extends FormRequest
{
    public function authorize(): bool { return true; } // accès géré par EnsureAdmin

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'gown' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'string', 'max:500'],
            'translations.en.quote' => ['required', 'string', 'max:1000'],
            'translations.fr.quote' => ['nullable', 'string', 'max:1000'],
        ];
    }
}