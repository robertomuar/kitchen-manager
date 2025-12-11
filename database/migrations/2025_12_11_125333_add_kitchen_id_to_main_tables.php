<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Añadimos kitchen_id a las tablas principales
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('kitchen_id')
                ->nullable()
                ->after('user_id')
                ->constrained('kitchens')
                ->nullOnDelete();
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->foreignId('kitchen_id')
                ->nullable()
                ->after('user_id')
                ->constrained('kitchens')
                ->nullOnDelete();
        });

        Schema::table('stock_items', function (Blueprint $table) {
            $table->foreignId('kitchen_id')
                ->nullable()
                ->after('user_id')
                ->constrained('kitchens')
                ->nullOnDelete();
        });

        /**
         * 2) Migramos datos existentes:
         *
         * Para cada usuario:
         *  - Creamos una cocina por defecto: "Cocina de {nombre}".
         *  - Lo añadimos a kitchen_user como owner.
         *  - Asignamos kitchen_id a todos sus products/locations/stock_items.
         */
        DB::transaction(function () {
            $users = DB::table('users')->select('id', 'name', 'email')->get();

            foreach ($users as $user) {
                $name = $user->name ?: ('Cocina de '.$user->email);
                $slugBase = Str::slug($name) ?: 'cocina-'.$user->id;

                // Nos aseguramos de que el slug sea único
                $slug = $slugBase;
                $i = 1;

                while (DB::table('kitchens')->where('slug', $slug)->exists()) {
                    $slug = $slugBase.'-'.$i;
                    $i++;
                }

                $kitchenId = DB::table('kitchens')->insertGetId([
                    'name'       => $name,
                    'owner_id'   => $user->id,
                    'slug'       => $slug,
                    'color'      => null,
                    'icon'       => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Relación pivot: el usuario es owner de esta cocina
                DB::table('kitchen_user')->insert([
                    'kitchen_id' => $kitchenId,
                    'user_id'    => $user->id,
                    'role'       => 'owner',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Asignamos kitchen_id a los registros existentes de ese usuario
                DB::table('products')
                    ->where('user_id', $user->id)
                    ->update(['kitchen_id' => $kitchenId]);

                DB::table('locations')
                    ->where('user_id', $user->id)
                    ->update(['kitchen_id' => $kitchenId]);

                DB::table('stock_items')
                    ->where('user_id', $user->id)
                    ->update(['kitchen_id' => $kitchenId]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kitchen_id');
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kitchen_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kitchen_id');
        });
    }
};
