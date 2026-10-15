<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveTestimonialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'avatar' => ['required', 'string', 'max:500'],
            'translations.en.quote' => ['required', 'string', 'max:1000'],
            'translations.fr.quote' => ['nullable', 'string', 'max:1000'],
        ];
    }
}