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
        Schema::table('recordings', function(Blueprint $table) {
            $table->integer('day')->nullable()->after('on_time');
        });

        Schema::table('stock_availabilities', function(Blueprint $table) {
            $table->integer('recording_stock_id')->nullable()->after('purchase_item_reception_id');
            $table->foreign(['recording_stock_id'], 'stockavailablity_recording_stock_id')->references(['recording_stock_id'])->on('recording_stocks')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('recording_stocks', function(Blueprint $table) {
            $table->bigInteger('usage_amount')->nullable()->after('decrease');
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
