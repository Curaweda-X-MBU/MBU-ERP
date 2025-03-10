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
        Schema::table('projects', function(Blueprint $table) {
            $table->integer('fcr_id')->index('fcr_id')->nullable()->after('pic');
            $table->foreign(['fcr_id'], 'project_fcr_id')->references(['fcr_id'])->on('fcr')->onUpdate('no action')->onDelete('no action');
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
