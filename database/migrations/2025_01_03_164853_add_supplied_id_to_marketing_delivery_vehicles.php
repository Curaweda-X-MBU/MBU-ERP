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
        Schema::table('marketing_delivery_vehicles', function(Blueprint $table) {
            $table->integer('supplier_id')->after('driver_name')->nullable();
            $table->bigInteger('delivery_fee')->after('supplier_id')->nullable();

            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_delivery_vehicles', function(Blueprint $table) {
            $table->dropForeign(['supplier_id']);

            $table->dropColumn('supplier_id');
            $table->dropColumn('delivery_fee');
        });
    }
};
