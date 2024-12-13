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
        Schema::table('recording_nonstocks', function(Blueprint $table) {
            $table->integer('recording_id')->index('recording_id')->nullable()->after('recording_nonstock_id');
            $table->foreign(['recording_id'], 'recnonstock_recording_recording_id')->references(['recording_id'])->on('recordings')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
