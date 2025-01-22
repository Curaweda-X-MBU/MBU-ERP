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
        Schema::table('project_budgets', function(Blueprint $table) {
            $table->dropColumn('item');
            $table->integer('product_id')->index('product_id')->nullable()->after('project_budget_id');
            $table->integer('nonstock_id')->index('nonstock_id')->nullable()->after('product_id');

            $table->foreign(['product_id'], 'projectbudget_product_id')->references(['product_id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['nonstock_id'], 'projectbudget_nonstock_id')->references(['nonstock_id'])->on('nonstocks')->onUpdate('no action')->onDelete('no action');

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
