<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Réglages du site (une seule ligne) : coordonnées, seuil de livraison offerte, réseaux. */
class Setting extends Model
{
    use HasTranslations;

    protected $fillable = [
        'brand',
        'phone',
        'phone_href',
        'email',
        'address',
        'free_shipping',
        'instagram',
    ];

    protected function casts(): array
    {
        return [
            'free_shipping' => 'decimal:2',
        ];
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SettingTranslation::class);
    }
}
