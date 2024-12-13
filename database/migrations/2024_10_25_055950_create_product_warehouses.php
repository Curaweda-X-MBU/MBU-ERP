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
        Schema::create('product_warehouses', function (Blueprint $table) {
            $table->integer('product_warehouse_id', true);
            $table->integer('product_id')->index('product_id');
            $table->integer('warehouse_id')->index('warehouse_id');
            $table->integer('quantity')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        Schema::table('product_warehouses', function (Blueprint $table) {
            $table->foreign(['product_id'], 'pw_products_product_id')->references(['product_id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['warehouse_id'], 'pw_warehouses_warehouse_id')->references(['warehouse_id'])->on('warehouses')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_warehouses');
    }
};
