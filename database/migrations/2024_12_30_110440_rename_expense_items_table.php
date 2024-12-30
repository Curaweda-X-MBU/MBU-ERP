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
        Schema::table('expense_items', function(Blueprint $table) {
            if (Schema::hasColumn('expense_items', 'price')) {
                $table->dropColumn('price');
            }
            $table->tinyInteger('payment_status');
            $table->text('notes')->nullable()->after('total_price');
        });

        Schema::rename('expense_items', 'expense_main_prices');

        Schema::create('expense_addit_prices', function(Blueprint $table) {
            $table->id('expense_addit_price_id');
            $table->unsignedBigInteger('expense_id');
            $table->string('name');
            $table->bigInteger('price');
            $table->text('notes')->nullable();

            $table->foreign('expense_id')->references('expense_id')->on('expenses')
                ->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_main_prices', function(Blueprint $table) {
            if (Schema::hasColumn('expense_main_prices', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            if (Schema::hasColumn('expense_main_prices', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('expense_main_prices', 'price')) {
                $table->dropColumn('price');
            }
        });

        Schema::rename('expense_main_prices', 'expense_items');

        Schema::dropIfExists('expense_addit_prices');
    }
};
