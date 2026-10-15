<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Contenu de la page d'accueil (une seule ligne) : images, boutons et chiffres clés. */
class HomeContent extends Model
{
    use HasTranslations;

    protected $fillable = [
        'hero_image',
        'hero_detail_image',
        'salon_image',
        'hero_primary_to',
        'hero_secondary_to',
        'stat_brides',
        'stat_rating',
        'stat_reviews',
    ];

    protected function casts(): array
    {
        return [
            'stat_brides' => 'integer',
            'stat_rating' => 'decimal:1',
            'stat_reviews' => 'integer',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(HomeContentTranslation::class);
    }
}
