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
        Schema::table('purchase_items', function(Blueprint $table) {
            $table->dropForeign('items_projects_project_id');
            $table->dropForeign('items_warehouses_warehouse_id');
            $table->dropColumn('project_id');
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
