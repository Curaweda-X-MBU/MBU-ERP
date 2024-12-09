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
        Schema::table('audits', function(Blueprint $table) {
            $table->text('description')->nullable()->change();
            $table->string('document', 100)->nullable()->after('description');
            $table->tinyInteger('category')->after('title');
            $table->tinyInteger('priority')->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function(Blueprint $table) {
            $table->text('description')->nullable(false)->change();
            $table->dropColumn('document');
            $table->dropColumn('category');
            $table->dropColumn('priority');
        });
    }
};
