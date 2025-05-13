<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            // Asegúrate de que no existe la foreign key antes de añadirla
            if (!Schema::hasColumn('ingredients', 'user_id')) return;

            // Añade la clave foránea (si aún no está)
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
