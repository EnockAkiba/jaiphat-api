<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NewsletterSubscriber;

final class NewsletterService
{
    /** Idempotent : s'inscrire deux fois ne crée pas de doublon. Renvoie [abonné, créé ?]. */
    public function subscribe(string $email, string $locale): array
    {
        $subscriber = NewsletterSubscriber::firstOrCreate(['email' => $email], ['locale' => $locale]);

        return [$subscriber, $subscriber->wasRecentlyCreated];
    }
}
