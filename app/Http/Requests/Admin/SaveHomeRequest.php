<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveHomeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'hero.image' => ['required', 'string', 'max:500'],
            'hero.detail' => ['required', 'string', 'max:500'],
            'hero.primaryTo' => ['required', 'string', 'max:255'],
            'hero.secondaryTo' => ['required', 'string', 'max:255'],
            'hero.translations.*.kicker' => ['nullable', 'string', 'max:255'],
            'hero.translations.*.title' => ['nullable', 'string', 'max:255'],
            'hero.translations.*.script' => ['nullable', 'string', 'max:255'],
            'hero.translations.*.text' => ['nullable', 'string', 'max:2000'],

            'services' => ['required', 'array', 'max:8'],
            'services.*.icon' => ['required', 'string', 'max:32'],
            'services.*.translations.*.title' => ['nullable', 'string', 'max:255'],
            'services.*.translations.*.text' => ['nullable', 'string', 'max:255'],

            'band.translations.*.title' => ['nullable', 'string', 'max:255'],
            'band.items' => ['required', 'array', 'max:8'],
            'band.items.*.icon' => ['required', 'string', 'max:32'],
            'band.items.*.translations.*.title' => ['nullable', 'string', 'max:255'],
            'band.items.*.translations.*.text' => ['nullable', 'string', 'max:255'],

            'stats.brides' => ['required', 'integer', 'min:0'],
            'stats.rating' => ['required', 'numeric', 'between:0,5'],
            'stats.reviews' => ['required', 'integer', 'min:0'],

            'salon' => ['nullable', 'string', 'max:500'],
            'salonImages' => ['nullable', 'array'],
            'salonImages.*' => ['string', 'max:500'],
        ];
    }
}