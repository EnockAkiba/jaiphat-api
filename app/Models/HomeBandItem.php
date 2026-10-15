<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Un argument du bandeau de l'accueil (ex. livraison, garantie). */
class HomeBandItem extends Model
{
    use HasTranslations;

    public $timestamps = false;

    protected $fillable = [
        'icon',
        'sort_order',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(HomeBandItemTranslation::class);
    }
}
