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
            $table->json('images')->after('total_culling');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ph_complaints', function(Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
