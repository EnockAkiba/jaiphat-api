<?php

declare(strict_types=1);

namespace App\Http\Requests\Shopping;

use App\Services\CartService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'between:1,'.CartService::MAX_QUANTITY_PER_LINE],
        ];
    }
}
