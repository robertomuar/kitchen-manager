<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Normalizamos valores existentes que no estén en la lista actualizada
        DB::statement("
            UPDATE `ingredients`
            SET `unit` = 'UND'
            WHERE `unit`
              NOT IN ('L','ML','KG','GRMS','UND','PAQUETE','BOTE')
        ");

        // 2) Alteramos la columna para incluir PAQUETE y BOTE
        DB::statement("
            ALTER TABLE `ingredients`
            MODIFY `unit`
            ENUM(
                'L',
                'ML',
                'KG',
                'GRMS',
                'UND',
                'PAQUETE',
                'BOTE'
            )
            NOT NULL
            DEFAULT 'UND'
            COMMENT 'Unidad de medida del ingrediente'
        ");
    }

    public function down(): void
    {
        // Volvemos al enum previo sin PAQUETE ni BOTE
        DB::statement("
            ALTER TABLE `ingredients`
            MODIFY `unit`
            ENUM(
                'L',
                'ML',
                'KG',
                'GRMS',
                'UND'
            )
            NOT NULL
            DEFAULT 'UND'
            COMMENT 'Unidad de medida del ingrediente'
        ");
    }
};
