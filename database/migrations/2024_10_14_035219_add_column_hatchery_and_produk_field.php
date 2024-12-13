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
        Schema::table('ph_complaints', function (Blueprint $table) {
            $table->dropColumn('product');
            $table->integer('product_id')->nullable()->after('ph_complaint_id');
        });
        Schema::table('ph_complaints', function (Blueprint $table) {
            $table->foreign(['product_id'], 'ph_complaints_ibfk_8')->references(['product_id'])->on('products')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ph_complaints', function (Blueprint $table) {
            $table->dropForeign('ph_complaints_ibfk_8');
            $table->string('product', 50)->after('ph_complaint_id');
            $table->dropColumn('product_id');
        });
    }
};
