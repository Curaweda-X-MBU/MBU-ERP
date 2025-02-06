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
        Schema::create('active_projects_log', function(Blueprint $table) {
            $table->id('active_projects_log_id');
            $table->bigInteger('period');
            $table->integer('project_id');
            $table->timestamps();

            $table->foreign('project_id')->references('project_id')->on('projects')->onUpdate('cascade')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_projects_log');
    }
};
