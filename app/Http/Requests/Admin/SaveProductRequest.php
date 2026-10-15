<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999'],
            'category' => ['required', 'string', Rule::exists('categories', 'slug')],
            'color' => ['required', 'string', Rule::exists('colors', 'slug')],
            'accessory' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'badge' => ['nullable', 'string', 'max:100'],
            'images' => ['required', 'array', 'min:1', 'max:12'],
            'images.*' => ['required', 'string', 'max:2048'],
            'sizes' => ['nullable', 'array', 'max:30'],
            'sizes.*' => ['string', 'max:8'],
            'translations' => ['required', 'array'],
            'translations.*.excerpt' => ['nullable', 'string', 'max:500'],
            'translations.*.description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'Ajoutez au moins une photo.',
            'category.exists' => 'Catégorie inconnue.',
            'color.exists' => 'Couleur inconnue.',
        ];
    }
}
