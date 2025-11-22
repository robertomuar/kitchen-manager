<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'default_quantity',
        'default_unit',
        'default_pack_size',
        'location_id',   // 🔴 IMPORTANTE: para que se guarde la ubicación
        'notes',
    ];

    protected $casts = [
        'default_quantity' => 'float',
        'default_pack_size' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function stockItems()
    {
        return $this->hasMany(StockItem::class);
    }
}
