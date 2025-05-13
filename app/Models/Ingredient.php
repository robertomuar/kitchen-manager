<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    /**
     * Campos que pueden asignarse masivamente.
     *
     * @var array<string>
     */
   protected $fillable = [
    'name',
    'quantity',
    'min_quantity',
    'unit',
    'location',
    'expires_at',
    'user_id', // 👈 ¡ESTE ES EL QUE FALTA!
];


    /**
     * Casts automáticos para atributos.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'expires_at'   => 'datetime',
        'quantity'     => 'float',
        'min_quantity' => 'float',
    ];

    /**
     * Atributos adicionales a serializar.
     *
     * @var array<string>
     */
    protected $appends = [
        'formatted_quantity',
        'alarm',
    ];

    /**
     * Formatea la cantidad quitando .00 en enteros.
     *
     * @return string
     */
    public function getFormattedQuantityAttribute(): string
    {
        $qty = $this->quantity;
        if (floor($qty) == $qty) {
            return (string) (int) $qty;
        }
        return rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.');
    }

    /**
     * Indica si la cantidad está por debajo de la mínima configurada.
     *
     * @return bool
     */
    public function getAlarmAttribute(): bool
    {
        return $this->quantity < $this->min_quantity;
    }

    /**
     * Lista centralizada de ubicaciones válidas.
     *
     * @return string[]
     */
    public static function locations(): array
    {
        return [
            'Armario de dentro',
            'Armario de la terraza',
            'Armario fregadero',
            'Armario bajo',
            'Frigorífico',
            'Congelador',
            'Armario despensa',
        ];
    }

    /**
     * Lista centralizada de unidades válidas.
     *
     * @return array<string,string> código => etiqueta
     */
    public static function units(): array
    {
        return [
            'L'       => 'Litros',
            'ML'      => 'Mililitros',
            'KG'      => 'Kilos',
            'GRMS'    => 'Gramos',
            'UND'     => 'Unidades',
            'PAQUETE' => 'Paquete',
            'BOTE'    => 'Bote',
        ];
    }
}
