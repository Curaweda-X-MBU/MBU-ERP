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
        Schema::table('stock_logs', function (Blueprint $table) {
            $table->dropForeign('stocklog_purchases_purchase_id');
            $table->dropColumn('purchase_id');
            $table->integer('purchase_item_id')->index('purchase_item_id')->nullable()->after('notes');
            $table->foreign(['purchase_item_id'], 'stocklog_purchase_item_purchase_item_id')->references(['purchase_item_id'])->on('purchase_items')->onUpdate('no action')->onDelete('no action');
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
