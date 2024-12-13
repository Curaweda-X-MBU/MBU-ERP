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
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('projects_products_product_id');
            $table->dropColumn('product_id');
            $table->integer('product_category_id')->index('product_category_id')->nullable()->after('project_id');
            $table->foreign(['product_category_id'], 'projects_prodcat_id')->references(['product_category_id'])->on('product_categories')->onUpdate('no action')->onDelete('no action');
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
