<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(3),
            'name' => fake()->unique()->words(2, true),
            'price' => fake()->randomFloat(2, 50, 3000),
            'category_id' => Category::factory(),
            'color_id' => Color::factory(),
            'is_accessory' => false,
            'is_featured' => false,
            'badge' => null,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function accessory(): static
    {
        return $this->state(['is_accessory' => true]);
    }

    /** Crée automatiquement 2 traductions, 1 image et 2 tailles pour chaque produit. */
    public function configure(): static
    {
        return $this->afterCreating(function (Product $product): void {
            foreach (['fr', 'en'] as $locale) {
                $product->translations()->create([
                    'locale' => $locale,
                    'excerpt' => "Extrait {$locale}",
                    'description' => "Description {$locale}",
                ]);
            }

            $product->images()->create(['path' => "products/{$product->slug}-1.jpg", 'sort_order' => 0]);
            $product->sizes()->createMany([
                ['label' => 'S', 'sort_order' => 0],
                ['label' => 'M', 'sort_order' => 1],
            ]);
        });
    }
}
