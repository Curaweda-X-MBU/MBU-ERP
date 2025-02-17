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
            $table->decimal('cum_depletion_rate', 7, 3)->change();
            $table->decimal('daily_gain', 7, 3)->change();
            $table->decimal('avg_daily_gain', 7, 3)->change();
            $table->decimal('fcr_value', 7, 3)->change();
            $table->decimal('daily_depletion_rate', 7, 3)->change();
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
