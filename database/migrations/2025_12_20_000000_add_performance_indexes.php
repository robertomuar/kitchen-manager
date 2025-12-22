<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createIndexIfMissing('products', ['location_id'], 'idx_products_location_id');
        $this->createIndexIfMissing('products', ['kitchen_id'], 'idx_products_kitchen_id');
        $this->createIndexIfMissing('products', ['kitchen_id', 'location_id'], 'idx_products_kitchen_location');

        $this->createIndexIfMissing('stock_items', ['user_id'], 'idx_stock_items_user_id');
        $this->createIndexIfMissing('stock_items', ['product_id'], 'idx_stock_items_product_id');
        $this->createIndexIfMissing('stock_items', ['location_id'], 'idx_stock_items_location_id');
        $this->createIndexIfMissing('stock_items', ['expires_at'], 'idx_stock_items_expires_at');
        $this->createIndexIfMissing('stock_items', ['min_quantity'], 'idx_stock_items_min_quantity');
        $this->createIndexIfMissing('stock_items', ['kitchen_id'], 'idx_stock_items_kitchen_id');
        $this->createIndexIfMissing('stock_items', ['kitchen_id', 'product_id'], 'idx_stock_items_kitchen_product');
        $this->createIndexIfMissing('stock_items', ['kitchen_id', 'location_id'], 'idx_stock_items_kitchen_location');
    }

    public function down(): void
    {
        $this->dropIndexIfExists('stock_items', 'idx_stock_items_kitchen_location');
        $this->dropIndexIfExists('stock_items', 'idx_stock_items_kitchen_product');
        $this->dropIndexIfExists('stock_items', 'idx_stock_items_kitchen_id');
        $this->dropIndexIfExists('stock_items', 'idx_stock_items_min_quantity');
        $this->dropIndexIfExists('stock_items', 'idx_stock_items_expires_at');
        $this->dropIndexIfExists('stock_items', 'idx_stock_items_location_id');
        $this->dropIndexIfExists('stock_items', 'idx_stock_items_product_id');
        $this->dropIndexIfExists('stock_items', 'idx_stock_items_user_id');

        $this->dropIndexIfExists('products', 'idx_products_kitchen_location');
        $this->dropIndexIfExists('products', 'idx_products_kitchen_id');
        $this->dropIndexIfExists('products', 'idx_products_location_id');
    }

    private function createIndexIfMissing(string $table, array $columns, string $indexName): void
    {
        if (!$this->tableExists($table) || !$this->columnsExist($table, $columns)) {
            return;
        }

        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($columns, $indexName) {
            $table->index($columns, $indexName);
        });
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (!$this->tableExists($table)) {
            return;
        }

        if (!$this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $table) use ($indexName) {
            $table->dropIndex($indexName);
        });
    }

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    private function columnsExist(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            return DB::table('information_schema.STATISTICS')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', $table)
                ->where('INDEX_NAME', $indexName)
                ->exists();
        }

        if ($driver === 'sqlite') {
            return count(DB::select(
                "SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = ? AND name = ?",
                [$table, $indexName]
            )) > 0;
        }

        if ($driver === 'pgsql') {
            return DB::table('pg_indexes')
                ->where('tablename', $table)
                ->where('indexname', $indexName)
                ->exists();
        }

        if ($driver === 'sqlsrv') {
            return DB::table('sys.indexes')
                ->where('name', $indexName)
                ->whereRaw('object_id = OBJECT_ID(?)', [$table])
                ->exists();
        }

        return false;
    }
};
