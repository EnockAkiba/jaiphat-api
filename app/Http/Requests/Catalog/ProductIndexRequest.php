<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use App\Enums\ProductSort;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // catalogue public
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'category' => ['nullable', 'string', 'exists:categories,slug'],
            'color' => ['nullable', 'string', 'exists:colors,slug'],
            'accessory' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'q' => ['nullable', 'string', 'max:100'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', Rule::enum(ProductSort::class)],
            'per_page' => ['nullable', 'integer', 'between:1,48'],
        ];
    }
}
