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
        try {
            Schema::table('expenses', function(Blueprint $table) {
                $table->string('bill_docs')->after('category')->nullable();
                $table->foreignId('parent_expense_id')->nullable()->after('expense_id')->constrained('expenses', 'expense_id')->nullOnDelete();
                $table->string('realization_docs')->after('bill_docs')->nullable();
            });

            if (Schema::hasTable('expense_payments')) {
                Schema::table('expense_payments', function(Blueprint $table) {
                    $table->dropForeign(['approver_id']);
                    $table->dropForeign(['bank_id']);
                    $table->dropForeign(['expense_id']);
                });
            }
            Schema::dropIfExists('expense_payments');

            Schema::create('expense_disburses', function(Blueprint $table) {
                $table->id('expense_disburse_id');
                $table->foreignId('expense_id')->constrained('expenses', 'expense_id')->cascadeOnDelete();
                $table->tinyInteger('is_approved')->nullable();
                $table->integer('approver_id')->nullable();
                $table->text('approval_notes')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->string('payment_method', 50);
                $table->integer('bank_id')->nullable();
                $table->string('payment_reference')->nullable();
                $table->string('transaction_number')->nullable();
                $table->bigInteger('payment_nominal');
                $table->dateTime('payment_at');
                $table->string('disburse_docs')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('approver_id')->references('user_id')->on('users')->onDelete('set null');
                $table->foreign('bank_id')->references('bank_id')->on('banks')->onDelete('set null');
            });

            Schema::create('expense_realizations', function(Blueprint $table) {
                $table->id('expense_realization_id');
                $table->foreignId('expense_id')->constrained('expenses', 'expense_id')->cascadeOnDelete();
                $table->foreignId('expense_item_id')->nullable()->constrained('expense_main_prices', 'expense_item_id')->cascadeOnDelete();
                $table->foreignId('expense_addit_price_id')->nullable()->constrained('expense_addit_prices', 'expense_addit_price_id')->cascadeOnDelete();
                $table->double('qty')->nullable();
                $table->bigInteger('price');
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('expenses', function(Blueprint $table) {
                $table->dropColumn('bill_docs');
                $table->dropColumn('realization_docs');
                $table->dropForeign(['parent_expense_id']);
                $table->dropColumn('parent_expense_id');
            });

            Schema::dropIfExists('expense_disburses');

            Schema::table('expense_realizations', function(Blueprint $table) {
                $table->dropForeign(['expense_id']);
                $table->dropForeign(['expense_item_id']);
                $table->dropForeign(['expense_addit_price_id']);
            });

            Schema::dropIfExists('expense_realizations');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
};
