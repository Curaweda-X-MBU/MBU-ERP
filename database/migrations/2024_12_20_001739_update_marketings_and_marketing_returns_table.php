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
        Schema::table('marketing_returns', function(Blueprint $table) {
            $table->dropForeign(['marketing_id']);
            $table->foreign('marketing_id')->references('marketing_id')->on('marketings')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::table('marketings', function(Blueprint $table) {
            $table->dropForeign(['marketing_return_id']);
            $table->foreign('marketing_return_id')->references('marketing_return_id')->on('marketing_returns')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_returns', function(Blueprint $table) {
            $table->dropForeign(['marketing_id']);
            $table->foreign('marketing_id')->references('marketing_id')->on('marketings')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('marketings', function(Blueprint $table) {
            $table->dropForeign(['marketing_return_id']);
            $table->foreign('marketing_return_id')->references('marketing_return_id')->on('marketing_returns')->onUpdate('cascade')->onDelete('cascade');
        });
    }
};
