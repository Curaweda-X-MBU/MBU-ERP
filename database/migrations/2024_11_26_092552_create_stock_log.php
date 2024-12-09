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
        Schema::create('stock_logs', function(Blueprint $table) {
            $table->integer('stock_log_id', true);
            $table->integer('product_warehouse_id')->index('product_warehouse_id');
            $table->date('stock_date');
            $table->bigInteger('increase')->default(0);
            $table->bigInteger('decrease')->default(0);
            $table->bigInteger('remaining_total')->default(0);
            $table->string('stocked_by');
            $table->string('notes');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
        });

        Schema::table('stock_logs', function(Blueprint $table) {
            $table->foreign(['product_warehouse_id'], 'stocklog_productwarehouse_product_warehouse_id')->references(['product_warehouse_id'])->on('product_warehouses')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'stocklog_users_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('stock_movements', function(Blueprint $table) {
            $table->integer('stock_movement_id', true);
            $table->integer('origin_id')->index('origin_id');
            $table->integer('destination_id')->index('destination_id');
            $table->integer('supplier_id')->nullable()->index('supplier_id');
            $table->bigInteger('movement_fee')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('stock_movements', function(Blueprint $table) {
            $table->foreign(['origin_id'], 'stockmv_warehouses_origin_id')->references(['warehouse_id'])->on('warehouses')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['destination_id'], 'stockmv_warehouses_destination_id')->references(['warehouse_id'])->on('warehouses')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['supplier_id'], 'stockmv_suppliers_supplier_id')->references(['supplier_id'])->on('suppliers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'stockmv_users_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');

        });

        Schema::create('stock_movement_products', function(Blueprint $table) {
            $table->integer('stock_movement_product_id', true);
            $table->integer('stock_movement_id')->index('stock_movement_id');
            $table->integer('product_id')->index('product_id');
            $table->bigInteger('quantity')->default(0);
            $table->string('notes')->nullable();
        });

        Schema::table('stock_movement_products', function(Blueprint $table) {
            $table->foreign(['stock_movement_id'], 'stockmvproduct_stockmv_stock_movement_id')->references(['stock_movement_id'])->on('stock_movements')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_id'], 'stockmvproduct_products_product_id')->references(['product_id'])->on('products')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_log');
    }
};
