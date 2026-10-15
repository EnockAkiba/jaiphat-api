<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Contact;
use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Collection;

/** Rendez-vous, messages et abonnés : ce que l'équipe reçoit du site. */
final class InboxAdminService
{
    /** @return Collection<int, Appointment> */
    public function appointments(): Collection
    {
        return Appointment::query()->orderByDesc('date')->orderByDesc('time')->limit(500)->get();
    }

    public function setAppointmentStatus(string $publicId, string $status): Appointment
    {
        $appointment = Appointment::query()->where('public_id', $publicId)->firstOrFail();
        $appointment->update(['status' => AppointmentStatus::from($status)]);

        return $appointment;
    }

    /** @return Collection<int, Contact> */
    public function contacts(): Collection
    {
        return Contact::query()->latest()->limit(500)->get();
    }

    public function setContactRead(int $id, bool $read): Contact
    {
        $contact = Contact::query()->findOrFail($id);
        $contact->update(['read_at' => $read ? ($contact->read_at ?? now()) : null]);

        return $contact;
    }

    /** @return Collection<int, NewsletterSubscriber> */
    public function subscribers(): Collection
    {
        return NewsletterSubscriber::query()->latest()->limit(2000)->get();
    }

    public function removeSubscriber(int $id): void
    {
        NewsletterSubscriber::query()->findOrFail($id)->delete();
    }
}