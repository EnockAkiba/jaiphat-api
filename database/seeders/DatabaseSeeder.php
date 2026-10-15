<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // firstOrCreate : relancer `db:seed` ne plante plus sur l'email déjà pris.
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password'],
        );

        $this->call([
            CatalogSeeder::class,
            ShowcaseSeeder::class, // après le catalogue : les mariées pointent vers des produits
        ]);
    }
}
