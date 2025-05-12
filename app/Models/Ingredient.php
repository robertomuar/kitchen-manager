<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name', 'quantity', 'unit', 'expires_at'];

    // Añade esto:
    protected $casts = [
        'expires_at' => 'datetime',  // o 'date' si solo quieres la parte fecha
    ];

    public function recipes()
    {
        return $this->belongsToMany(Recipe::class)
                    ->withPivot('quantity', 'unit')
                    ->withTimestamps();
    }
}
