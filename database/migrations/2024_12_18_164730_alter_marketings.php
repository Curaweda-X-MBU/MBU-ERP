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
        Schema::table('marketings', function(Blueprint $table) {
            $table->tinyInteger('is_returned')->after('marketing_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketings', function(Blueprint $table) {
            $table->dropColumn('is_returned');
        });
    }
};
