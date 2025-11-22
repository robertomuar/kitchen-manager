<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_shares', function (Blueprint $table) {
            $table->id();

            // Dueño de la cocina
            $table->foreignId('owner_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Usuario invitado
            $table->foreignId('invited_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->boolean('can_edit')->default(true);

            $table->timestamps();

            $table->unique(['owner_id', 'invited_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_shares');
    }
};
