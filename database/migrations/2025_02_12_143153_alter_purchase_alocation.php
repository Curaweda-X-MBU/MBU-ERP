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
        Schema::table('purchase_item_alocations', function(Blueprint $table) {
            $table->foreign(['purchase_item_id'], 'purchase_item_alocation_purchase_item_id')->references(['purchase_item_id'])->on('purchase_items')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['warehouse_id'], 'purchase_item_alocation_warehouse_id')->references(['warehouse_id'])->on('warehouses')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
