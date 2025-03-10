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
        Schema::table('stock_availabilities', function(Blueprint $table) {
            $table->integer('purchase_item_id')->index('purchase_item_id')->nullable();
            $table->foreign(['purchase_item_id'], 'availability_purchase_item_id')->references(['purchase_item_id'])->on('purchase_items')->onUpdate('no action')->onDelete('no action');

            $table->integer('purchase_item_reception_id')->index('purchase_item_reception_id')->nullable();
            $table->foreign(['purchase_item_reception_id'], 'availability_purchase_item_reception_id')->references(['purchase_item_reception_id'])->on('purchase_item_receptions')->onUpdate('no action')->onDelete('no action');
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
