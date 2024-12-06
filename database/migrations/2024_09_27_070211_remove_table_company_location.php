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
            $table->dropForeign('ph_complaints_ibfk_1');
            $table->dropForeign('ph_complaints_ibfk_2');
            $table->dropColumn('company_id');
            $table->dropColumn('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ph_complaints', function (Blueprint $table) {
            $table->integer('company_id')->index('company_id');
            $table->integer('location_id')->index('location_id');
            
            $table->foreign(['company_id'], 'ph_complaints_ibfk_1')->references(['company_id'])->on('companies')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['location_id'], 'ph_complaints_ibfk_2')->references(['location_id'])->on('locations')->onUpdate('no action')->onDelete('no action');
        });
    }
};
