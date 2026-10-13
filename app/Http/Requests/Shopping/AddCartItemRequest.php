<?php

declare(strict_types=1);

namespace App\Http\Requests\Shopping;

use App\Services\CartService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            // La taille doit exister POUR CE produit (table product_sizes)
            'size' => ['required', 'string', 'max:8',
                Rule::exists('product_sizes', 'label')->where('product_id', $this->input('product_id'))],
            'quantity' => ['nullable', 'integer', 'between:1,'.CartService::MAX_QUANTITY_PER_LINE],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return ['size.exists' => "Cette taille n'existe pas pour ce produit."];
    }
}
