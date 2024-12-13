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
        Schema::table('recording_nonstocks', function(Blueprint $table) {
            $table->dropColumn('avg_weight');
            $table->dropColumn('total_chick');
            $table->dropColumn('total_calc');
        });

        Schema::table('recording_bw', function(Blueprint $table) {
            $table->dropForeign('recbw_recording_nonstock_id');
        });

        Schema::dropIfExists('recording_bw');
        Schema::create('recording_bw', function(Blueprint $table) {
            $table->integer('recording_bw_id', true);
            $table->integer('recording_id')->index('recording_id');
            $table->integer('product_warehouse_id')->index('product_warehouse_id');
            $table->decimal('avg_weight', 8, 2);
            $table->bigInteger('total_chick');
            $table->bigInteger('total_calc');
            $table->decimal('value', 8, 2)->default(0);
            $table->string('notes')->nullable();
        });

        Schema::table('recording_bw', function(Blueprint $table) {
            $table->foreign(['recording_id'], 'recbw_recordings_id')->references(['recording_id'])->on('recordings')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_warehouse_id'], 'recbw_prdwh_id')->references(['product_warehouse_id'])->on('product_warehouses')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('recording_bw_lists', function(Blueprint $table) {
            $table->integer('recording_bw_list_id', true);
            $table->integer('recording_bw_id')->index('recording_bw_id');
            $table->decimal('weight', 8, 2);
            $table->bigInteger('total');
            $table->bigInteger('weight_calc');
        });

        Schema::table('recording_bw_lists', function(Blueprint $table) {
            $table->foreign(['recording_bw_id'], 'recbwlist_recording_bw_id')->references(['recording_bw_id'])->on('recording_bw')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recording_bw_lists');
    }
};
