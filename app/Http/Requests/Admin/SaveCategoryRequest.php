<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // accès géré par EnsureAdmin
    }

    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:255'],
            'menu' => ['nullable', Rule::in(['shop', 'collections'])],
            'translations' => ['required', 'array'],
            'translations.en.name' => ['required', 'string', 'max:255'],
            'translations.fr.name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
