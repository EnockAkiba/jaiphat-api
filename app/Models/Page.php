<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Page de contenu (ex. « à propos ») identifiée par son slug. */
class Page extends Model
{
    use HasTranslations;

    protected $fillable = [
        'slug',
        'image',
        'secondary_image',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }
}
