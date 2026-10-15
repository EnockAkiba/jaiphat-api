<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Crée (ou met à jour) le compte administrateur.
 * Identifiants : ADMIN_EMAIL / ADMIN_PASSWORD dans .env. En production, mets un mot de passe fort !
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew(['email' => strtolower((string) env('ADMIN_EMAIL', 'admin@jaiphat.com'))]);
        $user->forceFill([
            'name' => $user->name ?: 'Studio',
            'password' => (string) env('ADMIN_PASSWORD', 'jaiphat'), // hashé automatiquement par le cast du modèle User
            'is_admin' => true,
        ])->save();
    }
}
