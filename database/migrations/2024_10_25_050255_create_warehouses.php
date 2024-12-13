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
        Schema::create('warehouses', function(Blueprint $table) {
            $table->integer('warehouse_id', true);
            $table->string('name', 50);
            $table->tinyInteger('type');
            $table->integer('location_id')->nullable()->index('location_id');
            $table->integer('kandang_id')->nullable()->index('kandang_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('warehouses', function(Blueprint $table) {
            $table->foreign(['location_id'], 'warehouses_locations_location_id')->references(['location_id'])->on('locations')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'warehouses_users_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
