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
        Schema::table('ph_complaints', function (Blueprint $table) {
            $table->integer('kandang_id')->after('location_id');
        });

        Schema::table('ph_complaints', function (Blueprint $table) {
            $table->foreign(['kandang_id'], 'ph_complaints_ibfk_5')->references(['kandang_id'])->on('kandang')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ph_complaints', function (Blueprint $table) {
            $table->dropForeign('ph_complaints_ibfk_5');
            $table->dropColumn('kandang_id');
        });
    }
};
