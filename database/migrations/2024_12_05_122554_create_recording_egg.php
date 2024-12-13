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
        Schema::create('recording_eggs', function(Blueprint $table) {
            $table->integer('recording_egg_id', true);
            $table->integer('recording_id')->index('recording_id');
            $table->integer('product_warehouse_id')->index('product_warehouse_id');
            $table->bigInteger('increase')->default(0);
            $table->bigInteger('decrease')->default(0);
            $table->bigInteger('big')->default(0);
            $table->bigInteger('small')->default(0);
            $table->bigInteger('crack')->default(0);
            $table->bigInteger('dirty')->default(0);
            $table->bigInteger('broken')->default(0);
            $table->bigInteger('total_egg')->default(0);
            $table->string('notes')->nullable();
        });

        Schema::table('recording_eggs', function(Blueprint $table) {
            $table->foreign(['recording_id'], 'recegg_recordings_id')->references(['recording_id'])->on('recordings')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_warehouse_id'], 'recegg_prdwh_id')->references(['product_warehouse_id'])->on('product_warehouses')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recording_eggs');
    }
};
