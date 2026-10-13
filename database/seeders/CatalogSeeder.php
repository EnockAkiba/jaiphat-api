<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Données de démonstration du catalogue (fr + en). Idempotent : on peut le relancer sans doublons.
 *   php artisan db:seed --class=CatalogSeeder
 */
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'robes' => ['menu' => 'dresses', 'sort' => 1, 'fr' => 'Robes de mariée', 'en' => 'Wedding dresses'],
            'voiles' => ['menu' => 'accessories', 'sort' => 2, 'fr' => 'Voiles', 'en' => 'Veils'],
            'bijoux' => ['menu' => 'accessories', 'sort' => 3, 'fr' => 'Bijoux', 'en' => 'Jewelry'],
        ];

        $colors = [
            'ivoire' => ['swatch' => '#F5F0E6', 'sort' => 1, 'fr' => 'Ivoire', 'en' => 'Ivory'],
            'blanc' => ['swatch' => '#FFFFFF', 'sort' => 2, 'fr' => 'Blanc', 'en' => 'White'],
            'champagne' => ['swatch' => '#E8D5B5', 'sort' => 3, 'fr' => 'Champagne', 'en' => 'Champagne'],
            'blush' => ['swatch' => '#F2D4D0', 'sort' => 4, 'fr' => 'Blush', 'en' => 'Blush'],
        ];

        $categoryIds = [];
        foreach ($categories as $slug => $data) {
            $category = Category::updateOrCreate(
                ['slug' => $slug],
                ['menu' => $data['menu'], 'sort_order' => $data['sort']],
            );
            foreach (['fr', 'en'] as $locale) {
                $category->translations()->updateOrCreate(['locale' => $locale], ['name' => $data[$locale]]);
            }
            $categoryIds[$slug] = $category->id;
        }

        $colorIds = [];
        foreach ($colors as $slug => $data) {
            $color = Color::updateOrCreate(
                ['slug' => $slug],
                ['swatch' => $data['swatch'], 'image' => "colors/{$slug}.jpg", 'sort_order' => $data['sort']],
            );
            foreach (['fr', 'en'] as $locale) {
                $color->translations()->updateOrCreate(['locale' => $locale], ['name' => $data[$locale]]);
            }
            $colorIds[$slug] = $color->id;
        }

        $dressSizes = ['34', '36', '38', '40', '42', '44'];
        $oneSize = ['U'];

        $products = [
            ['robe-aurore', 'Robe Aurore', 1450, 'robes', 'ivoire', false, true, 'new', $dressSizes,
                ['Dentelle française, coupe sirène.', 'French lace, mermaid cut.'],
                ['Une robe sirène en dentelle de Calais, dos échancré et traîne courte.', 'A Calais lace mermaid gown with a low back and a short train.']],
            ['robe-celeste', 'Robe Céleste', 1890, 'robes', 'blanc', false, true, null, $dressSizes,
                ['Tulle fluide et bustier perlé.', 'Flowing tulle and a beaded bodice.'],
                ['Une robe princesse en tulle léger, bustier brodé de perles.', 'A princess gown in light tulle with a pearl-embroidered bodice.']],
            ['robe-eloise', 'Robe Éloïse', 2200, 'robes', 'champagne', false, false, 'bestseller', $dressSizes,
                ['Satin duchesse, silhouette épurée.', 'Duchess satin, clean silhouette.'],
                ['Une robe droite en satin duchesse, manches longues et col bateau.', 'A straight duchess satin gown with long sleeves and a boat neckline.']],
            ['robe-margaux', 'Robe Margaux', 2650, 'robes', 'blush', false, true, null, $dressSizes,
                ['Crêpe soyeux, dos nu romantique.', 'Silky crepe, romantic open back.'],
                ['Une robe fluide en crêpe, dos nu et fente subtile.', 'A flowing crepe gown with an open back and a subtle slit.']],
            ['voile-cathedrale', 'Voile Cathédrale', 320, 'voiles', 'ivoire', true, true, null, $oneSize,
                ['Trois mètres de tulle bordé de dentelle.', 'Three meters of lace-edged tulle.'],
                ['Un voile cathédrale en tulle doux, bordure de dentelle florale.', 'A cathedral veil in soft tulle with a floral lace edge.']],
            ['voile-court', 'Voile Court', 180, 'voiles', 'blanc', true, false, null, $oneSize,
                ['Voile blusher léger.', 'Light blusher veil.'],
                ['Un voile court de style blusher, tulle ultra-léger.', 'A short blusher-style veil in ultra-light tulle.']],
            ['diademe-perle', 'Diadème Perlé', 140, 'bijoux', 'champagne', true, true, null, $oneSize,
                ['Perles de culture et cristaux.', 'Cultured pearls and crystals.'],
                ['Un diadème serti de perles de culture et de cristaux.', 'A tiara set with cultured pearls and crystals.']],
            ['boucles-etoile', "Boucles d'oreilles Étoile", 95, 'bijoux', 'blanc', true, false, 'new', $oneSize,
                ['Pendantes, plaqué or.', 'Gold-plated drop earrings.'],
                ['Des boucles pendantes en forme d\'étoile, plaquées or.', 'Star-shaped drop earrings, gold-plated.']],
        ];

        foreach ($products as $i => [$slug, $name, $price, $category, $color, $accessory, $featured, $badge, $sizes, $excerpt, $description]) {
            $product = Product::updateOrCreate(['slug' => $slug], [
                'name' => $name,
                'price' => $price,
                'category_id' => $categoryIds[$category],
                'color_id' => $colorIds[$color],
                'is_accessory' => $accessory,
                'is_featured' => $featured,
                'badge' => $badge,
                'sort_order' => $i + 1,
            ]);

            foreach (['fr' => 0, 'en' => 1] as $locale => $index) {
                $product->translations()->updateOrCreate(['locale' => $locale], [
                    'excerpt' => $excerpt[$index],
                    'description' => $description[$index],
                ]);
            }

            $product->images()->delete();
            $product->images()->createMany([
                ['path' => "products/{$slug}-1.jpg", 'sort_order' => 0],
                ['path' => "products/{$slug}-2.jpg", 'sort_order' => 1],
            ]);

            $product->sizes()->delete();
            foreach ($sizes as $position => $label) {
                $product->sizes()->create(['label' => $label, 'sort_order' => $position]);
            }
        }
    }
}
