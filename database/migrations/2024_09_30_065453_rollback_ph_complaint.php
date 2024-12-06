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
        Schema::table('ph_complaints', function(Blueprint $table) {
            $table->json('symptoms')->nullable()->after('description');
            $table->dropForeign('ph_complaints_ibfk_6');
            $table->dropColumn('ph_symptom_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ph_complaints', function(Blueprint $table) {
            $table->integer('ph_symptom_id')->after('description');
            $table->dropColumn('symptoms');
        });

        Schema::table('ph_complaints', function(Blueprint $table) {
            $table->foreign(['ph_symptom_id'], 'ph_complaints_ibfk_6')->references(['ph_symptom_id'])->on('ph_symptoms')->onUpdate('no action')->onDelete('no action');
        });
    }
};
