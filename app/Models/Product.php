<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasTranslations;

    protected $fillable = [
        'slug',
        'name',
        'price',
        'category_id',
        'color_id',
        'is_accessory',
        'is_featured',
        'badge',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_accessory' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ProductTranslation::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class)->orderBy('sort_order');
    }
}
