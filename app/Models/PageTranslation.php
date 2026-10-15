<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'page_id',
        'locale',
        'kicker',
        'title',
        'lede',
        'paragraphs',
        'values',
    ];

    protected function casts(): array
    {
        return [
            'paragraphs' => 'array',
            'values' => 'array',
        ];
    }
}
