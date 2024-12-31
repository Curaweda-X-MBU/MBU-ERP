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
        Schema::table('marketing_products', function(Blueprint $table) {
            $table->dropForeign(['kandang_id']);

            $table->renameColumn('kandang_id', 'warehouse_id');
            $table->foreign('warehouse_id')->references('warehouse_id')->on('warehouses')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_products', function(Blueprint $table) {
            $table->dropForeign(['warehouse_id']);

            $table->renameColumn('warehouse_id', 'kandang_id');
            $table->foreign('kandang_id')->references('kandang_id')->on('kandang')->onUpdate('no action')->onDelete('no action');
        });
    }
};
