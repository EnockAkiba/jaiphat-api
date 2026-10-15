<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SavePageRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'string', 'max:500'],
            'secondary' => ['nullable', 'string', 'max:500'],
            'translations.en.kicker' => ['nullable', 'string', 'max:255'],
            'translations.en.title' => ['required', 'string', 'max:255'],
            'translations.en.lede' => ['nullable', 'string', 'max:2000'],
            'translations.en.paragraphs' => ['nullable', 'array'],
            'translations.en.paragraphs.*' => ['string', 'max:3000'],
            'translations.en.values' => ['nullable', 'array'],
            'translations.fr.kicker' => ['nullable', 'string', 'max:255'],
            'translations.fr.title' => ['nullable', 'string', 'max:255'],
            'translations.fr.lede' => ['nullable', 'string', 'max:2000'],
            'translations.fr.paragraphs' => ['nullable', 'array'],
            'translations.fr.paragraphs.*' => ['string', 'max:3000'],
            'translations.fr.values' => ['nullable', 'array'],
        ];
    }
}