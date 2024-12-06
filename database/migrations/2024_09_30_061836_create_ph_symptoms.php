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
        Schema::create('ph_symptoms', function (Blueprint $table) {
            $table->integer('ph_symptom_id', true);
            $table->string('name', 50);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('ph_symptoms', function (Blueprint $table) {
            $table->foreign(['created_by'], 'ph_symptoms_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ph_symptoms', function (Blueprint $table) {
            $table->dropForeign('ph_symptoms_ibfk_1');
        });

        Schema::dropIfExists('ph_symptoms');
    }
};
