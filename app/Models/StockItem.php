<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockMovement;

class StockItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'location_id',
        'quantity',
        'open_units',
        'unit',
        'min_quantity',
        'expires_at',
        'is_open',
        'notes',
        'location', // si existía la columna de texto, la dejamos por compatibilidad
        'kitchen_id',
    ];

    protected $casts = [
        'quantity' => 'float',
        'open_units' => 'integer',
        'min_quantity' => 'float',
        'expires_at' => 'date',
        'is_open' => 'boolean',
    ];

    protected $appends = [
        'is_below_minimum',
        'available_units',
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

        return (float) $this->available_units < (float) $this->min_quantity;
    }

    public function getAvailableUnitsAttribute(): float
    {
        $quantity = (float) ($this->quantity ?? 0);
        $openUnits = (float) ($this->open_units ?? 0);

        return max($quantity - $openUnits, 0);
    }

    public function kitchen()
    {
        return $this->belongsTo(Kitchen::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
