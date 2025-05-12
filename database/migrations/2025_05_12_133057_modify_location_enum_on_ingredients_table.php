<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Redefinimos el enum para incluir "Armario despensa"
        DB::statement("
            ALTER TABLE `ingredients`
            MODIFY `location`
            ENUM(
                'Armario de dentro',
                'Armario de la terraza',
                'Armario fregadero',
                'Armario bajo',
                'Frigorífico',
                'Congelador',
                'Armario horno'
            )
            NOT NULL
            DEFAULT 'Armario de dentro'
            COMMENT 'Ubicación exacta del ingrediente en la cocina'
        ");
    }

    public function down(): void
    {
        // Volvemos al enum original (sin "Armario despensa")
        DB::statement("
            ALTER TABLE `ingredients`
            MODIFY `location`
            ENUM(
                'Armario de dentro',
                'Armario de la terraza',
                'Armario fregadero',
                'Armario bajo',
                'Frigorífico',
                'Congelador'
            )
            NOT NULL
            DEFAULT 'Armario de dentro'
            COMMENT 'Ubicación exacta del ingrediente en la cocina'
        ");
    }
};
