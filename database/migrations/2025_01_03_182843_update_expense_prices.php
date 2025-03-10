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
        Schema::table('expenses', function(Blueprint $table) {
            $table->dropColumn('grand_total');
        });

        Schema::table('expense_main_prices', function(Blueprint $table) {
            $table->renameColumn('total_price', 'price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function(Blueprint $table) {
            $table->bigInteger('grand_total');
        });
        Schema::table('expense_main_prices', function(Blueprint $table) {
            $table->renameColumn('price', 'total_price');
        });
    }
};
