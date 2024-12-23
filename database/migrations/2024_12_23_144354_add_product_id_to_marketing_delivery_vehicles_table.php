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
            $table->unsignedBigInteger('marketing_product_id');

            $table->foreign('marketing_product_id')->references('marketing_product_id')->on('marketing_products')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_delivery_vehicles', function(Blueprint $table) {
            $table->dropForeign(['marketing_product_id']);

            $table->dropColumn('marketing_product_id');
        });
    }
};
