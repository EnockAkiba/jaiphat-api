<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bride extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'gown_name',
        'product_id',
        'image',
        'sort_order',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(BrideTranslation::class);
    }

    /** La robe portée (facultatif) : permet un lien « voir la robe ». */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
