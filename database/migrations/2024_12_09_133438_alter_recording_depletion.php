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
        Schema::table('recording_depletions', function (Blueprint $table) {
            $table->dropColumn('increase');
            $table->dropColumn('decrease');
            $table->dropColumn('death');
            $table->dropColumn('culling');
            $table->dropColumn('afkir');
            $table->dropColumn('total_depletion');
            $table->bigInteger('total')->default(0)->after('product_warehouse_id');
        });

        Schema::table('recording_eggs', function (Blueprint $table) {
            $table->dropColumn('increase');
            $table->dropColumn('decrease');
            $table->dropColumn('big');
            $table->dropColumn('small');
            $table->dropColumn('crack');
            $table->dropColumn('dirty');
            $table->dropColumn('broken');
            $table->dropColumn('total_egg');
            $table->bigInteger('total')->default(0)->after('product_warehouse_id');
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
