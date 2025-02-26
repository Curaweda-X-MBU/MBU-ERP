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
        Schema::table('expenses', function(Blueprint $table) {
            $table->integer('supplier_id')->nullable()->after('location_id');

            $table->foreign(['supplier_id'])->references('supplier_id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function(Blueprint $table) {
            $table->dropForeign(['supplier_id']);

            $table->dropColumn('supplier_id');
        });
    }
};
