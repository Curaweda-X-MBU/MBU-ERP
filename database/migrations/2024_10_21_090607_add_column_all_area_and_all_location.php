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
        Schema::table('roles', function(Blueprint $table) {
            $table->boolean('all_area')->default(false)->after('company_id');
            $table->boolean('all_location')->default(false)->after('all_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function(Blueprint $table) {
            //
        });
    }
};
