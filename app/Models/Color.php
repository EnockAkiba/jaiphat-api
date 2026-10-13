<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\ColorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Color extends Model
{
    /** @use HasFactory<ColorFactory> */
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'swatch',
        'image',
        'sort_order',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ColorTranslation::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
