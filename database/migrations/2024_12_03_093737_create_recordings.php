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
        Schema::create('recordings', function(Blueprint $table) {
            $table->integer('recording_id', true);
            $table->integer('project_id')->index('project_id');
            $table->dateTime('record_datetime');
            $table->tinyInteger('status');
            $table->boolean('on_time')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('recordings', function(Blueprint $table) {
            $table->foreign(['project_id'], 'recordings_projects_project_id')->references(['project_id'])->on('projects')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'recordings_users_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('recording_stocks', function(Blueprint $table) {
            $table->integer('recording_stock_id', true);
            $table->integer('recording_id')->index('recording_id');
            $table->integer('product_warehouse_id')->index('product_warehouse_id');
            $table->bigInteger('increase')->default(0);
            $table->bigInteger('decrease')->default(0);
            $table->string('notes')->nullable();
        });

        Schema::table('recording_stocks', function(Blueprint $table) {
            $table->foreign(['recording_id'], 'recstock_recordings_id')->references(['recording_id'])->on('recordings')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_warehouse_id'], 'recstock_product_warehouses_id')->references(['product_warehouse_id'])->on('product_warehouses')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('nonstocks', function(Blueprint $table) {
            $table->integer('nonstock_id', true);
            $table->string('name');
            $table->integer('uom_id')->index('uom_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('nonstocks', function(Blueprint $table) {
            $table->foreign(['uom_id'], 'nonstock_uom_id')->references(['uom_id'])->on('uom')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'nonstock_users_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('recording_nonstocks', function(Blueprint $table) {
            $table->integer('recording_nonstock_id', true);
            $table->integer('nonstock_id')->index('nonstock_id');
            $table->decimal('avg_weight', 8, 2)->nullable();
            $table->bigInteger('total_chick')->nullable();
            $table->bigInteger('total_calc')->nullable();
            $table->decimal('value', 8, 2)->default(0);
            $table->string('notes')->nullable();
        });

        Schema::table('recording_nonstocks', function(Blueprint $table) {
            $table->foreign(['nonstock_id'], 'recnonstock_nonstock_id')->references(['nonstock_id'])->on('nonstocks')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('recording_bw', function(Blueprint $table) {
            $table->integer('recording_bw_id', true);
            $table->integer('recording_nonstock_id')->index('recording_nonstock_id');
            $table->decimal('weight', 8, 2);
            $table->bigInteger('total');
            $table->bigInteger('weight_calc');
        });

        Schema::table('recording_bw', function(Blueprint $table) {
            $table->foreign(['recording_nonstock_id'], 'recbw_recording_nonstock_id')->references(['recording_nonstock_id'])->on('recording_nonstocks')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('recording_depletions', function(Blueprint $table) {
            $table->integer('recording_depletion_id', true);
            $table->integer('recording_id')->index('recording_id');
            $table->integer('product_warehouse_id')->index('product_warehouse_id');
            $table->bigInteger('increase')->default(0);
            $table->bigInteger('decrease')->default(0);
            $table->bigInteger('death')->default(0);
            $table->bigInteger('culling')->default(0);
            $table->bigInteger('afkir')->default(0);
            $table->bigInteger('total_depletion')->default(0);
            $table->string('notes')->nullable();
        });

        Schema::table('recording_depletions', function(Blueprint $table) {
            $table->foreign(['recording_id'], 'recdepletion_recordings_id')->references(['recording_id'])->on('recordings')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_warehouse_id'], 'recdepletion_prdwh_id')->references(['product_warehouse_id'])->on('product_warehouses')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recordings');
    }
};
