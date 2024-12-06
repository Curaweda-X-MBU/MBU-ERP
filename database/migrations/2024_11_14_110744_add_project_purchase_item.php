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
        Schema::table('purchase_items', function(Blueprint $table) {
            $table->integer('project_id')->index('project_id')->nullable()->after('warehouse_id');
            $table->foreign(['project_id'], 'items_projects_project_id')->references(['project_id'])->on('projects')->onUpdate('no action')->onDelete('no action');
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
