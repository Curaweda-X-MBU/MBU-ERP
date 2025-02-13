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
            $table->json('warehouse_ids')->nullable()->after('supplier_id');
            $table->dropForeign('purchase_warehouse_warehouse_id');
            $table->dropColumn('warehouse_id');
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
