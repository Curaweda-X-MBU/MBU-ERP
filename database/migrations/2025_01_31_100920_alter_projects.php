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
            $table->dateTime('closing_date')->nullable()->after('first_day_old_chick');
            $table->integer('closing_by')->nullable()->index('closing_by')->after('closing_date');

            $table->foreign(['closing_by'], 'projects_users_closing_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
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
