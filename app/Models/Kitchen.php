<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kitchen extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
        'slug',
        'color',
        'icon',
    ];

    /**
     * Usuario propietario de la cocina.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Usuarios que tienen acceso a esta cocina (owner + invitados).
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'kitchen_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Productos asociados a esta cocina.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Ubicaciones asociadas a esta cocina.
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Items de stock asociados a esta cocina.
     */
    public function stockItems()
    {
        return $this->hasMany(StockItem::class);
    }
}
