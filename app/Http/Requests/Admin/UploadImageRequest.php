<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // mimes vérifie le contenu réel du fichier, pas seulement son extension. SVG exclu (peut contenir du script).
            'file' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:6144', 'dimensions:max_width=6000,max_height=6000'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.max' => 'L\'image ne doit pas dépasser 6 Mo.',
            'file.mimes' => 'Formats acceptés : JPG, PNG, WebP, GIF.',
        ];
    }
}
