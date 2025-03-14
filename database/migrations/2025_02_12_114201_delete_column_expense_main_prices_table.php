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
        Schema::table('expense_main_prices', function(Blueprint $table) {
            $table->dropColumn(['sub_category', 'uom']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_main_prices', function(Blueprint $table) {
            $table->string('sub_category')->nullable()->after('expense_id');
            $table->string('uom')->nullable()->after('sub_category');
        });
    }
};
