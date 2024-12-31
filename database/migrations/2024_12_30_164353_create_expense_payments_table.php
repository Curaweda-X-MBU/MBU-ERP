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
        Schema::create('expense_payments', function(Blueprint $table) {
            $table->id('expense_payment_id');
            $table->unsignedBigInteger('expense_id');
            $table->tinyInteger('is_approved')->nullable();
            $table->integer('approver_id')->nullable();
            $table->text('approval_notes')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('payment_method', 50);
            $table->integer('bank_id');
            $table->string('payment_reference')->nullable();
            $table->string('transaction_number')->nullable();
            $table->bigInteger('payment_nominal');
            $table->bigInteger('bank_admin_fees');
            $table->dateTime('payment_at');
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('verify_status');
            $table->timestamps();

            $table->foreign('expense_id')->references('expense_id')->on('expenses')->onUpdate('no action')->onDelete('no action');
            $table->foreign('approver_id')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign('bank_id')->references('bank_id')->on('banks')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('expenses', function(Blueprint $table) {
            $table->string('id_expense')->nullable()->after('expense_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_payments');
        if (Schema::hasColumn('expenses', 'id_expense')) {
            Schema::table('expenses', function(Blueprint $table) {
                $table->dropColumn('id_expense');
            });
        }
    }
};
