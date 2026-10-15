<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;

final class ContactService
{
    /** @param array{name: string, email: string, message: string} $data */
    public function send(array $data): Contact
    {
        return Contact::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
        ]);
    }
}
