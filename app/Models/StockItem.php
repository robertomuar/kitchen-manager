<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'location_id',
        'quantity',
        'unit',
        'min_quantity',
        'expires_at',
        'is_open',
        'notes',
        'location', // si existía la columna de texto, la dejamos por compatibilidad
    ];

    protected $casts = [
        'quantity' => 'float',
        'min_quantity' => 'float',
        'expires_at' => 'date',
        'is_open' => 'boolean',
    ];

    protected $appends = [
        'is_below_minimum',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function getIsBelowMinimumAttribute(): bool
    {
        if ($this->min_quantity === null) {
            return false;
        }

        return (float) $this->quantity < (float) $this->min_quantity;
    }
}
