<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Forms\BookAppointmentData;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Prise de rendez-vous au salon : un seul rendez-vous actif par créneau. */
final class AppointmentService
{
    /** Créneaux d'une heure, du lundi au samedi. */
    public const SLOTS = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'];

    public static function isOpenDay(CarbonImmutable $date): bool
    {
        return ! $date->isSunday();
    }

    /** @return array<int, array{time: string, available: bool}> */
    public function availability(CarbonImmutable $date): array
    {
        $taken = Appointment::query()
            ->whereDate('date', $date->toDateString())
            ->where('status', '!=', AppointmentStatus::Cancelled)
            ->pluck('time')
            ->map(fn (string $t) => substr($t, 0, 5))
            ->all();

        $open = self::isOpenDay($date);

        return array_map(
            fn (string $slot) => ['time' => $slot, 'available' => $open && ! in_array($slot, $taken, true)],
            self::SLOTS,
        );
    }

    /** @throws ValidationException si le créneau vient d'être pris */
    public function book(BookAppointmentData $data): Appointment
    {
        return DB::transaction(function () use ($data): Appointment {
            $taken = Appointment::query()
                ->whereDate('date', $data->date->toDateString())
                ->where('time', $data->time.':00')
                ->where('status', '!=', AppointmentStatus::Cancelled)
                ->lockForUpdate()
                ->exists();

            if ($taken) {
                throw ValidationException::withMessages(['time' => 'Ce créneau n\'est plus disponible.']);
            }

            return Appointment::create([
                'public_id' => (string) Str::ulid(),
                'user_id' => $data->userId,
                'name' => $data->name,
                'email' => $data->email,
                'phone' => $data->phone,
                'date' => $data->date->toDateString(),
                'time' => $data->time.':00',
                'party' => $data->party,
                'notes' => $data->notes,
                'status' => AppointmentStatus::Pending,
            ]);
        });
    }

    /** @return Collection<int, Appointment> */
    public function forUser(int $userId): Collection
    {
        return Appointment::query()->where('user_id', $userId)->orderByDesc('date')->orderBy('time')->get();
    }

    /** @throws ModelNotFoundException|ValidationException */
    public function cancel(int $userId, string $publicId): Appointment
    {
        $appointment = Appointment::query()
            ->where('user_id', $userId)
            ->where('public_id', $publicId)
            ->firstOrFail(); // pas le sien -> 404

        if ($appointment->status === AppointmentStatus::Cancelled) {
            return $appointment;
        }

        if ($appointment->date->isBefore(now()->startOfDay())) {
            throw ValidationException::withMessages(['appointment' => 'Ce rendez-vous est déjà passé.']);
        }

        $appointment->update(['status' => AppointmentStatus::Cancelled]);

        return $appointment;
    }
}
