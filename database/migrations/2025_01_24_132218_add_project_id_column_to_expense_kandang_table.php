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
        Schema::table('expense_kandang', function(Blueprint $table) {
            $table->integer('project_id')->after('kandang_id')->nullable();

            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_kandang', function(Blueprint $table) {
            $table->dropForeign(['project_id']);

            $table->dropColumn('project_id');
        });
    }
};
