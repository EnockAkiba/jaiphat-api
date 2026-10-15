<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Un service présenté sur l'accueil (ex. sur mesure, retouches). */
class HomeService extends Model
{
    use HasTranslations;

    public $timestamps = false;

    protected $fillable = [
        'icon',
        'sort_order',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(HomeServiceTranslation::class);
    }
}
