<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('quantity');
            $table->string('unit');

            // Creamos la columna ENUM location desde el inicio
            $table->enum('location', [
                'Armario de dentro',
                'Armario de la terraza',
                'Armario fregadero',
                'Armario bajo',
                'Frigorífico',
                'Congelador',
            ])->default('Armario de dentro')
              ->comment('Ubicación exacta del ingrediente en la cocina');

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
