<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kitchens', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Para URLs tipo /kitchen/mi-cocina
            $table->string('slug')->nullable()->unique();

            // Opcional, para personalizar visualmente
            $table->string('color', 7)->nullable();   // #RRGGBB
            $table->string('icon', 64)->nullable();   // nombre de icono o similar

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kitchens');
    }
};
