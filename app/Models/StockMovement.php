<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'user_id',
        'kitchen_id',
        'product_id',
        'location_id',
        'action',
        'quantity_before',
        'quantity_after',
    ];

    protected $casts = [
        'quantity_before' => 'float',
        'quantity_after'  => 'float',
    ];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class);
    }
}
