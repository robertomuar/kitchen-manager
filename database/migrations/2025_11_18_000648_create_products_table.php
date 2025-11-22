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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Usuario "dueño" del producto (para que cada uno pueda tener sus productos personalizados)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('category')->nullable(); // ej: 'alimentación', 'limpieza', etc.
            $table->string('barcode')->nullable()->index(); // código de barras (si lo usas)
            $table->string('default_unit')->default('unidad'); // unidad por defecto: unidad, kg, l...
            $table->decimal('default_quantity', 8, 2)->default(1); // cantidad típica del envase
            $table->integer('default_pack_size')->nullable(); // ej: paquete de 6, 8, 12...
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
