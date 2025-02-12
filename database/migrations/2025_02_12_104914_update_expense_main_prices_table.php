<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function migrateUp()
    {
        DB::table('expense_main_prices')->update([
            'nonstock_id' => DB::raw('(SELECT nonstock_id FROM nonstocks WHERE nonstocks.name = expense_main_prices.sub_category LIMIT 1)'),
        ]);
    }

    private function migrateDown()
    {
        DB::table('expense_main_prices')->update([
            'sub_category' => DB::raw('(SELECT name FROM nonstocks WHERE nonstocks.nonstock_id = expense_main_prices.nonstock_id LIMIT 1)'),
        ]);
    }

    public function up(): void
    {
        try {
            DB::beginTransaction();
            Schema::table('expense_main_prices', function(Blueprint $table) {
                $table->renameColumn('expense_item_id', 'expense_main_price_id');
                $table->dropForeign('expense_items_expense_id_foreign');
                $table->foreign(['expense_id'], 'expense_main_prices_expense_id_foreign')->references('expense_id')->on('expenses')->onDelete('cascade');

                $table->integer('nonstock_id')->nullable()->after('expense_id');
                $table->integer('supplier_id')->nullable()->after('nonstock_id');
            });

            $this->migrateUp();

            Schema::table('expense_main_prices', function(Blueprint $table) {
                $table->foreign('nonstock_id')->references('nonstock_id')->on('nonstocks')->onDelete('set null');
                $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('set null');
            });
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }

    public function down(): void
    {
        try {
            DB::beginTransaction();
            Schema::table('expense_main_prices', function(Blueprint $table) {
                $table->renameColumn('expense_main_price_id', 'expense_item_id');
                $table->dropForeign('expense_main_prices_expense_id_foreign');
                $table->foreign('expense_items_expense_id_foreign');
                $table->foreign(['expense_id'], 'expense_items_expense_id_foreign')->references('expense_id')->on('expenses')->onDelete('cascade');

                $table->dropForeign(['nonstock_id']);
                $table->dropForeign(['supplier_id']);
            });

            $this->migrateDown();

            Schema::table('expense_main_prices', function(Blueprint $table) {
                $table->dropColumn('nonstock_id');
                $table->dropColumn('supplier_id');
            });
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
};
