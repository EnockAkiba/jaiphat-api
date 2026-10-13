<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Category> */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'menu' => fake()->randomElement(['dresses', 'accessories']),
            'sort_order' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Category $category): void {
            foreach (['fr', 'en'] as $locale) {
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => "Catégorie {$category->slug} ({$locale})",
                ]);
            }
        });
    }
}
