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
        Schema::create('project_chickin', function (Blueprint $table) {
            $table->integer('project_chickin_id', true);
            $table->integer('project_id')->index('project_id');
            $table->string('travel_letter_number');
            $table->string('travel_letter_document')->nullable();
            $table->date('chickin_date');
            $table->integer('supplier_id')->index('supplier_id');
            $table->string('hatchery');
            $table->bigInteger('total_chickin');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_chickin');
    }
};
