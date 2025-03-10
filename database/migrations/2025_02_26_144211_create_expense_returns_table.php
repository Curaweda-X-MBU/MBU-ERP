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
        Schema::create('expense_returns', function(Blueprint $table) {
            $table->id('expense_return_id');
            $table->foreignId('expense_id')->constrained('expenses', 'expense_id')->cascadeOnDelete();
            $table->string('payment_method', 50);
            $table->integer('bank_id')->nullable();
            $table->integer('bank_recipient_id')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('transaction_number')->nullable();
            $table->bigInteger('payment_nominal');
            $table->bigInteger('bank_admin_fees')->nullable()->default(0);
            $table->dateTime('payment_at');
            $table->string('return_docs')->nullable();
            $table->text('notes')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('bank_id')->references('bank_id')->on('banks')->onDelete('set null');
            $table->foreign('bank_recipient_id')->references('bank_id')->on('banks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_returns');
    }
};
