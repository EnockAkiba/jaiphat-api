<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; } // accès géré par EnsureAdmin

    public function rules(): array
    {
        return [
            'status' => ['required_without:shippingFee', Rule::in(['pending', 'confirmed', 'fulfilled', 'cancelled'])],
            'shippingFee' => ['required_without:status', 'nullable', 'numeric', 'min:0', 'max:1000000'],
        ];
    }
}