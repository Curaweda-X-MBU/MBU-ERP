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
        Schema::table('project_recordings', function(Blueprint $table) {
            $table->dropColumn('unit_name');
            $table->integer('uom_id')->index('uom_id')->nullable()->after('item');
            $table->foreign(['uom_id'], 'projectrecordings_uom_uom_id')->references(['uom_id'])->on('uom')->onUpdate('no action')->onDelete('no action');
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
