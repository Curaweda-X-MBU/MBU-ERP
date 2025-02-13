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
        Schema::create('purchase_item_alocations', function(Blueprint $table) {
            $table->integer('purchase_item_alocation_id', true);
            $table->integer('purchase_item_id')->index('purchase_item_id');
            $table->integer('warehouse_id')->index('warehouse_id');
            $table->bigInteger('alocation_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('purchase_alocation');
    }
};
