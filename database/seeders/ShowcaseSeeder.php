<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Bride;
use App\Models\HomeBandItem;
use App\Models\HomeContent;
use App\Models\HomeService;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Contenu de démonstration de la vitrine. Peut être relancé sans créer de doublons.
 * Les images sont des chemins fictifs : remplace-les par tes vraies images.
 */
class ShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->home();
        $this->about();
        $this->testimonials();
        $this->brides();
    }

    private function settings(): void
    {
        if (Setting::query()->exists()) {
            return;
        }

        $s = Setting::create([
            'brand' => 'Jaiphat',
            'phone' => '+243 970 000 000',
            'phone_href' => '+243970000000',
            'email' => 'contact@jaiphat.test',
            'address' => 'Kinshasa, Gombe',
            'free_shipping' => 150,
            'instagram' => 'https://instagram.com/jaiphat',
        ]);
        $s->translations()->create(['locale' => 'fr', 'tagline' => 'La robe de vos rêves, sur mesure.', 'shipping_note' => 'Livraison offerte dès 150 $.', 'hours' => 'Lun–Sam, 9h–18h']);
        $s->translations()->create(['locale' => 'en', 'tagline' => 'The gown of your dreams, made for you.', 'shipping_note' => 'Free shipping from $150.', 'hours' => 'Mon–Sat, 9am–6pm']);
    }

    private function home(): void
    {
        if (! HomeContent::query()->exists()) {
            $h = HomeContent::create([
                'hero_image' => 'home/hero.jpg',
                'hero_detail_image' => 'home/hero-detail.jpg',
                'salon_image' => 'home/salon.jpg',
                'hero_primary_to' => '/shop',
                'hero_secondary_to' => '/appointment',
                'stat_brides' => 10000,
                'stat_rating' => 4.9,
                'stat_reviews' => 2500,
            ]);
            $h->translations()->create(['locale' => 'fr', 'hero_kicker' => 'Collection 2026', 'hero_title' => 'Le jour où tout commence', 'hero_script' => 'votre histoire', 'hero_text' => 'Des robes pensées pour sublimer chaque mariée.', 'band_title' => 'Pourquoi nous choisir']);
            $h->translations()->create(['locale' => 'en', 'hero_kicker' => '2026 Collection', 'hero_title' => 'The day everything begins', 'hero_script' => 'your story', 'hero_text' => 'Gowns designed to make every bride shine.', 'band_title' => 'Why choose us']);
        }

        if (! HomeBandItem::query()->exists()) {
            $this->items(HomeBandItem::class, [
                ['truck', ['Livraison offerte', 'Dès 150 $ d\'achat'], ['Free shipping', 'On orders over $150']],
                ['scissors', ['Retouches incluses', 'Ajustées à votre taille'], ['Free alterations', 'Fitted to your size']],
                ['heart', ['Conseils personnalisés', 'Par nos stylistes'], ['Personal advice', 'From our stylists']],
            ]);
        }

        if (! HomeService::query()->exists()) {
            $this->items(HomeService::class, [
                ['calendar', ['Rendez-vous au salon', 'Essayez vos robes en toute intimité.'], ['Salon appointment', 'Try your gowns in complete privacy.']],
                ['needle', ['Sur mesure', 'Une robe unique, créée pour vous.'], ['Made to measure', 'A one-of-a-kind gown made for you.']],
                ['gem', ['Accessoires', 'Voiles, diadèmes et bijoux assortis.'], ['Accessories', 'Matching veils, tiaras and jewellery.']],
            ]);
        }
    }

    /** @param class-string<\Illuminate\Database\Eloquent\Model> $model */
    private function items(string $model, array $rows): void
    {
        foreach ($rows as $i => [$icon, [$frTitle, $frText], [$enTitle, $enText]]) {
            $item = $model::create(['icon' => $icon, 'sort_order' => $i + 1]);
            $item->translations()->create(['locale' => 'fr', 'title' => $frTitle, 'text' => $frText]);
            $item->translations()->create(['locale' => 'en', 'title' => $enTitle, 'text' => $enText]);
        }
    }

    private function about(): void
    {
        if (Page::query()->where('slug', 'about')->exists()) {
            return;
        }

        $p = Page::create(['slug' => 'about', 'image' => 'pages/about.jpg', 'secondary_image' => 'pages/about-2.jpg']);
        $p->translations()->create([
            'locale' => 'fr', 'kicker' => 'Notre histoire', 'title' => 'À propos de Jaiphat',
            'lede' => 'Une maison dédiée aux mariées.',
            'paragraphs' => ['Née d\'une passion pour la couture, Jaiphat habille les mariées depuis plus de dix ans.', 'Chaque robe est choisie avec soin.'],
            'values' => [['title' => 'Qualité', 'text' => 'Des tissus nobles.'], ['title' => 'Écoute', 'text' => 'Vos envies d\'abord.']],
        ]);
        $p->translations()->create([
            'locale' => 'en', 'kicker' => 'Our story', 'title' => 'About Jaiphat',
            'lede' => 'A house devoted to brides.',
            'paragraphs' => ['Born from a passion for couture, Jaiphat has been dressing brides for over ten years.', 'Every gown is chosen with care.'],
            'values' => [['title' => 'Quality', 'text' => 'Noble fabrics.'], ['title' => 'Listening', 'text' => 'Your wishes first.']],
        ]);
    }

    private function testimonials(): void
    {
        if (Testimonial::query()->exists()) {
            return;
        }

        $rows = [
            ['Awa D.', 'Une expérience magique, la robe était parfaite.', 'A magical experience, the gown was perfect.'],
            ['Grâce M.', 'Équipe aux petits soins, je recommande.', 'Attentive team, highly recommended.'],
            ['Sarah K.', 'Les retouches étaient impeccables.', 'The alterations were flawless.'],
        ];
        foreach ($rows as $i => [$name, $fr, $en]) {
            $t = Testimonial::create(['name' => $name, 'avatar' => 'testimonials/'.($i + 1).'.jpg', 'sort_order' => $i + 1]);
            $t->translations()->create(['locale' => 'fr', 'quote' => $fr]);
            $t->translations()->create(['locale' => 'en', 'quote' => $en]);
        }
    }

    private function brides(): void
    {
        if (Bride::query()->exists()) {
            return;
        }

        $rows = [
            ['Naomi', 'Aurore', 'robe-aurore', 'Je me suis sentie reine.', 'I felt like a queen.'],
            ['Esther', 'Céleste', 'robe-celeste', 'Exactement la robe que je voulais.', 'Exactly the gown I wanted.'],
            ['Prisca', 'Éloïse', 'robe-eloise', 'Un souvenir inoubliable.', 'An unforgettable memory.'],
        ];
        foreach ($rows as $i => [$name, $gown, $slug, $fr, $en]) {
            $b = Bride::create([
                'name' => $name,
                'gown_name' => $gown,
                'product_id' => Product::query()->where('slug', $slug)->value('id'), // null si le catalogue n'est pas seedé
                'image' => 'brides/'.($i + 1).'.jpg',
                'sort_order' => $i + 1,
            ]);
            $b->translations()->create(['locale' => 'fr', 'quote' => $fr]);
            $b->translations()->create(['locale' => 'en', 'quote' => $en]);
        }
    }
}
