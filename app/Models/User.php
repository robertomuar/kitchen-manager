<?php

namespace App\Models;

use App\Models\UserShare;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Atributos asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'acting_as_user_id',
    ];

    /**
     * Atributos que se deben ocultar.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * ID del dueño de la cocina actual.
     * Si no estamos "actuando como" nadie, es nuestro propio id.
     */
    public function kitchenOwnerId(): int
    {
        return $this->acting_as_user_id ?? $this->id;
    }

    /**
     * Usuario dueño de la cocina actual.
     */
    public function kitchenOwner(): self
    {
        if ($this->acting_as_user_id === null) {
            return $this;
        }

        return static::findOrFail($this->acting_as_user_id);
    }

    /**
     * Productos de esta persona (cuando ella es dueña).
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Ubicaciones de esta persona (cuando ella es dueña).
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Registros de stock de esta persona (cuando ella es dueña).
     */
    public function stockItems(): HasMany
    {
        return $this->hasMany(StockItem::class);
    }

    /**
     * Cocinas que yo comparto con otras personas.
     */
    public function sharedUsers(): HasMany
    {
        return $this->hasMany(UserShare::class, 'owner_id');
    }

    /**
     * Cocinas de otros que me han compartido.
     */
    public function sharedWithOwners(): HasMany
    {
        return $this->hasMany(UserShare::class, 'invited_user_id');
    }
}
