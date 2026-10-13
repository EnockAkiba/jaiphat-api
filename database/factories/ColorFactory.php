<?php

namespace Database\Factories;

use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Color> */
class ColorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'swatch' => fake()->hexColor(),
            'image' => 'colors/'.fake()->unique()->word().'.jpg',
            'sort_order' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Color $color): void {
            foreach (['fr', 'en'] as $locale) {
                $color->translations()->create([
                    'locale' => $locale,
                    'name' => "Couleur {$color->slug} ({$locale})",
                ]);
            }
        });
    }
}
