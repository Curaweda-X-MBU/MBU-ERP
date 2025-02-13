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
        Schema::table('purchase_item_receptions', function(Blueprint $table) {
            $table->integer('supplier_id')->index('supplier_id')->nullable();
            $table->bigInteger('transport_per_item')->nullable();
            $table->bigInteger('transport_total')->nullable();
            $table->foreign(['supplier_id'], 'purchase_item_reception_supplier_id')->references(['supplier_id'])->on('suppliers')->onUpdate('no action')->onDelete('no action');
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
