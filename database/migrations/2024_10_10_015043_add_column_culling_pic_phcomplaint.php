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
            $table->integer('culling_pic')->nullable()->after('total_culling');
        });
        Schema::table('ph_complaints', function(Blueprint $table) {
            $table->foreign(['culling_pic'], 'ph_complaints_ibfk_7')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ph_complaints', function(Blueprint $table) {
            $table->dropForeign('ph_complaints_ibfk_7');
            $table->dropColumn('culling_pic');
        });
    }
};
