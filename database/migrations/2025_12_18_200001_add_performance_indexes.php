<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $index): bool
    {
        $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);

        return !empty($result);
    }

    public function up(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (Schema::hasColumn('products', 'location_id')
                    && !$this->indexExists('products', 'products_location_id_index')) {
                    $table->index('location_id');
                }

                if (Schema::hasColumn('products', 'kitchen_id')
                    && !$this->indexExists('products', 'products_kitchen_id_index')) {
                    $table->index('kitchen_id');
                }

                if (Schema::hasColumn('products', 'user_id')
                    && Schema::hasColumn('products', 'location_id')
                    && !$this->indexExists('products', 'products_user_id_location_id_index')) {
                    $table->index(['user_id', 'location_id']);
                }
            });
        }

        if (Schema::hasTable('stock_items')) {
            Schema::table('stock_items', function (Blueprint $table) {
                if (Schema::hasColumn('stock_items', 'expires_at')
                    && !$this->indexExists('stock_items', 'stock_items_expires_at_index')) {
                    $table->index('expires_at');
                }

                if (Schema::hasColumn('stock_items', 'kitchen_id')
                    && !$this->indexExists('stock_items', 'stock_items_kitchen_id_index')) {
                    $table->index('kitchen_id');
                }

                if (Schema::hasColumn('stock_items', 'user_id')
                    && Schema::hasColumn('stock_items', 'product_id')
                    && !$this->indexExists('stock_items', 'stock_items_user_id_product_id_index')) {
                    $table->index(['user_id', 'product_id']);
                }

                if (Schema::hasColumn('stock_items', 'user_id')
                    && Schema::hasColumn('stock_items', 'location_id')
                    && !$this->indexExists('stock_items', 'stock_items_user_id_location_id_index')) {
                    $table->index(['user_id', 'location_id']);
                }

                if (Schema::hasColumn('stock_items', 'user_id')
                    && Schema::hasColumn('stock_items', 'expires_at')
                    && !$this->indexExists('stock_items', 'stock_items_user_id_expires_at_index')) {
                    $table->index(['user_id', 'expires_at']);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if ($this->indexExists('products', 'products_location_id_index')) {
                    $table->dropIndex('products_location_id_index');
                }

                if ($this->indexExists('products', 'products_kitchen_id_index')) {
                    $table->dropIndex('products_kitchen_id_index');
                }

                if ($this->indexExists('products', 'products_user_id_location_id_index')) {
                    $table->dropIndex('products_user_id_location_id_index');
                }
            });
        }

        if (Schema::hasTable('stock_items')) {
            Schema::table('stock_items', function (Blueprint $table) {
                if ($this->indexExists('stock_items', 'stock_items_expires_at_index')) {
                    $table->dropIndex('stock_items_expires_at_index');
                }

                if ($this->indexExists('stock_items', 'stock_items_kitchen_id_index')) {
                    $table->dropIndex('stock_items_kitchen_id_index');
                }

                if ($this->indexExists('stock_items', 'stock_items_user_id_product_id_index')) {
                    $table->dropIndex('stock_items_user_id_product_id_index');
                }

                if ($this->indexExists('stock_items', 'stock_items_user_id_location_id_index')) {
                    $table->dropIndex('stock_items_user_id_location_id_index');
                }

                if ($this->indexExists('stock_items', 'stock_items_user_id_expires_at_index')) {
                    $table->dropIndex('stock_items_user_id_expires_at_index');
                }
            });
        }
    }
};
