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
        // Si la columna ya existe, no hacemos nada
        if (Schema::hasColumn('products', 'location_id')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id')
                ->nullable()
                ->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Si la columna no existe, no hacemos nada
        if (!Schema::hasColumn('products', 'location_id')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
    }
};
