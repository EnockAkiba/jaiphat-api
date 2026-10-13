<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class)->orderBy('id');
    }

    /** Nombre total d'articles (somme des quantités). */
    public function itemsCount(): int
    {
        return (int) $this->items->sum('qty');
    }

    /** Sous-total en centimes (entier : évite les erreurs d'arrondi des décimaux). */
    public function subtotalCents(): int
    {
        return (int) $this->items->sum(fn (CartItem $item) => $item->lineTotalCents());
    }
}
