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
        $foreignKeys = [
            'stockmv_suppliers_supplier_id',
            'stockmv_users_created_by',
            'stockmv_warehouses_destination_id',
            'stockmv_warehouses_origin_id',
        ];

        if (Schema::hasTable('stock_movements')) {
            Schema::table('stock_movements', function(Blueprint $table) use ($foreignKeys) {
                foreach ($foreignKeys as $foreignKey) {
                    if (Schema::hasColumn('stock_movements', $foreignKey)) {
                        $table->dropForeign($foreignKey);
                    }
                }
            });
        }

        if (Schema::hasTable('stock_movement_products')) {
            Schema::table('stock_movement_products', function(Blueprint $table) {
                $table->dropForeign('stockmvproduct_products_product_id');
                $table->dropForeign('stockmvproduct_stockmv_stock_movement_id');
            });
        }

        Schema::dropIfExists('stock_movement_products');
        Schema::dropIfExists('stock_movements');

        Schema::create('stock_movements', function(Blueprint $table) {
            $table->integer('stock_movement_id', true);
            $table->integer('origin_id')->index('origin_id');
            $table->integer('destination_id')->index('destination_id');
            $table->integer('product_id')->index('product_id');
            $table->integer('transfer_qty');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('stock_movements', function(Blueprint $table) {
            $table->foreign(['origin_id'], 'movements_origin_id')->references(['warehouse_id'])->on('warehouses')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['destination_id'], 'movements_destination_id')->references(['warehouse_id'])->on('warehouses')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_id'], 'movements_product_id')->references(['product_id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'movements_users_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('stock_movement_vehicles', function(Blueprint $table) {
            $table->integer('stock_movement_vehicle_id', true);
            $table->integer('stock_movement_id')->index('stock_movement_id');
            $table->integer('supplier_id')->index('supplier_id');
            $table->string('vehicle_number');
            $table->string('travel_document_number');
            $table->string('travel_document')->nullable();
            $table->string('driver_name');
        });

        Schema::table('stock_movement_vehicles', function(Blueprint $table) {
            $table->foreign(['stock_movement_id'], 'movement_vehicle_movement_id')->references(['stock_movement_id'])->on('stock_movements')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['supplier_id'], 'movement_vehicle_supplier_id')->references(['supplier_id'])->on('suppliers')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('stock_availabilities', function(Blueprint $table) {
            $table->integer('stock_availability_id', true);
            $table->integer('product_warehouse_id')->index('product_warehouse_id');
            $table->integer('current_qty');
            $table->bigInteger('product_price');
            $table->dateTime('received_date');
        });

        Schema::table('stock_availabilities', function(Blueprint $table) {
            $table->foreign(['product_warehouse_id'], 'availability_product_warehouse_id')->references(['product_warehouse_id'])->on('product_warehouses')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('stock_availability_usages', function(Blueprint $table) {
            $table->integer('stock_availability_usage_id', true);
            $table->integer('stock_availability_id')->index('stock_availability_id');
            $table->integer('stock_movement_id')->nullable()->index('stock_movement_id');
            $table->integer('recording_id')->nullable()->index('recording_id');
            $table->unsignedBigInteger('marketing_product_id')->nullable()->index('marketing_product_id');
            $table->bigInteger('usage_qty');
        });

        Schema::table('stock_availability_usages', function(Blueprint $table) {
            $table->foreign(['stock_availability_id'], 'avail_usage_stock_availability_id')->references(['stock_availability_id'])->on('stock_availabilities')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['stock_movement_id'], 'avail_usage_stock_movement_id')->references(['stock_movement_id'])->on('stock_movements')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['recording_id'], 'avail_usage_recording_id')->references(['recording_id'])->on('recordings')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['marketing_product_id'], 'avail_usage_marketing_product_id')->references(['marketing_product_id'])->on('marketing_products')->onUpdate('no action')->onDelete('no action');
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
