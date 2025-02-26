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
        Schema::table('stock_movement_vehicles', function(Blueprint $table) {
            $table->bigInteger('transport_amount')->nullable()->after('travel_document');
        });

        Schema::table('stock_movements', function(Blueprint $table) {
            $table->string('movement_number')->nullable()->after('stock_movement_id');
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
