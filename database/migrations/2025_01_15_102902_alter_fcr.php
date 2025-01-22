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
        Schema::create('fcr_standards', function(Blueprint $table) {
            $table->integer('fcr_standard_id', true);
            $table->integer('fcr_id')->index('fcr_id');
            $table->integer('day');
            $table->integer('weight');
            $table->integer('daily_gain')->nullable();
            $table->integer('avg_daily_gain')->nullable();
            $table->integer('daily_intake')->nullable();
            $table->integer('cum_intake')->nullable();
            $table->decimal('fcr', 5, 3);
        });

        Schema::table('fcr_standards', function(Blueprint $table) {
            $table->foreign(['fcr_id'], 'fcr_std_fcr_id')->references(['fcr_id'])->on('fcr')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('fcr', function(Blueprint $table) {
            $table->dropForeign('fcr_products_product_id');
            $table->dropColumn('product_id');
            $table->dropForeign('fcr_uom_uom_id');
            $table->dropColumn('uom_id');

            $table->integer('company_id')->index('company_id')->nullable()->after('fcr_id');
            $table->foreign(['company_id'], 'fcr_companies_company_id')->references(['company_id'])->on('companies')->onUpdate('no action')->onDelete('no action');
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
