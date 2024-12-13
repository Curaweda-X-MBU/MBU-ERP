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
        Schema::table('locations', function (Blueprint $table) {
            $table->integer('company_id')->nullable()->after('address');
        });
        Schema::table('locations', function (Blueprint $table) {
            $table->foreign(['company_id'], 'locations_ibfk_3')->references(['company_id'])->on('companies')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign('locations_ibfk_3');
            $table->dropColumn('company_id');
        });
    }
};
