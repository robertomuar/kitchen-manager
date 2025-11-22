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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();

            // Propietario del stock (cada usuario tiene su propio stock)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Producto al que pertenece este registro de stock
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Cantidad actual
            $table->decimal('quantity', 8, 2)->default(0);
            $table->string('unit')->default('unidad'); // unidad, kg, l...

            // Dónde lo tienes guardado
            $table->string('location')->nullable(); // ej: 'Nevera', 'Congelador', 'Despensa'

            // Cantidad mínima para que salte en la lista de reposición
            $table->integer('min_quantity')->nullable();

            // Caducidad
            $table->date('expires_at')->nullable();

            // Si está abierto o no (por ejemplo, un brick de leche empezado)
            $table->boolean('is_open')->default(false);

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
