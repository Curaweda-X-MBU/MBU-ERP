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
        Schema::table('purchases', function(Blueprint $table) {
            $table->integer('warehouse_id')->index('warehouse_id')->nullable()->after('po_date');
            $table->foreign(['warehouse_id'], 'purchase_warehouse_warehouse_id')->references(['warehouse_id'])->on('warehouses')->onUpdate('no action')->onDelete('no action');
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
