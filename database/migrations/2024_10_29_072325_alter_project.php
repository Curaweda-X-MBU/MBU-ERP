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
        Schema::table('project_recordings', function (Blueprint $table) {
            $table->string('item', 50)->after('project_recording_id');
        }); 

        Schema::table('projects', function (Blueprint $table) {
            $table->integer('total_budget')->after('target_depletion');
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
