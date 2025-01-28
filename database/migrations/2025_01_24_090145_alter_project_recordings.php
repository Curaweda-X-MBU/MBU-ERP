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
            $table->integer('total_depletion')->nullable()->after('day');
            $table->integer('cum_depletion')->nullable()->after('total_depletion');
            $table->decimal('daily_depletion_rate', 5, 2)->nullable()->after('total_depletion');
            $table->decimal('cum_depletion_rate', 5, 2)->nullable()->after('total_depletion');

            $table->integer('daily_gain')->nullable()->after('cum_depletion_rate');
            $table->integer('avg_daily_gain')->nullable()->after('daily_gain');
            $table->integer('cum_intake')->nullable()->after('avg_daily_gain');
            $table->decimal('fcr_value', 5, 2)->nullable()->after('cum_intake');

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
