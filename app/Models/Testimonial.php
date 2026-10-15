<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Testimonial extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'avatar',
        'sort_order',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(TestimonialTranslation::class);
    }
}
