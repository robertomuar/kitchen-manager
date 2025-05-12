<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->float('min_quantity')
                  ->default(0)
                  ->after('quantity')
                  ->comment('Cantidad mínima antes de requerir compra');
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('min_quantity');
        });
    }
};
