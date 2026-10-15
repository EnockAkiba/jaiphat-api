<?php

declare(strict_types=1);

namespace App\Http\Requests\Forms;

use App\Services\AppointmentService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->email)) {
            $this->merge(['email' => mb_strtolower(trim($this->email))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'date' => [
                'required', 'date_format:Y-m-d', 'after:today', 'before_or_equal:'.now()->addMonths(6)->toDateString(),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! AppointmentService::isOpenDay(CarbonImmutable::parse($value))) {
                        $fail('Le salon est fermé le dimanche.');
                    }
                },
            ],
            'time' => ['required', Rule::in(AppointmentService::SLOTS)],
            'party' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            // Piège à robots : champ caché que seuls les robots remplissent.
            'website' => ['prohibited'],
        ];
    }
}
