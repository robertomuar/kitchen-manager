<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            // Dueño de la ubicación (cada usuario puede tener sus propias ubicaciones)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('name'); // Ej: Nevera, Congelador, Despensa
            $table->string('category')->nullable(); // Ej: Frío, Seco, Limpieza...
            $table->string('color')->nullable(); // Para futuras etiquetas de color en el front
            $table->integer('sort_order')->default(0); // Orden de listado

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
