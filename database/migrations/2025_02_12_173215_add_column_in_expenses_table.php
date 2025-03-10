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
            $table->string('po_number')->after('expense_id')->nullable();
            $table->dateTime('transaction_date')->after('po_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function(Blueprint $table) {
            $table->dropColumn('po_number');
            $table->dropColumn('transaction_date');
        });
    }
};
