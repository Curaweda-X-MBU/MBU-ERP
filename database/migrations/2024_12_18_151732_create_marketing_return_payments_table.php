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
        Schema::create('marketing_return_payments', function(Blueprint $table) {
            $table->id('marketing_return_payment_id');
            $table->unsignedBigInteger('marketing_return_id');
            $table->tinyInteger('is_approved')->nullable();
            $table->integer('approver_id')->nullable();
            $table->text('approval_notes')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('payment_method', 50);
            $table->integer('bank_id');
            $table->integer('recipient_bank_id');
            $table->string('payment_reference')->nullable();
            $table->string('transaction_number')->nullable();
            $table->bigInteger('payment_nominal');
            $table->bigInteger('bank_admin_fees');
            $table->dateTime('payment_at');
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->tinyInteger('verify_status');
            $table->timestamps();

            $table->foreign('marketing_return_id')->references('marketing_return_id')->on('marketing_returns')->onUpdate('no action')->onDelete('no action');
            $table->foreign('approver_id')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign('bank_id')->references('bank_id')->on('banks')->onUpdate('no action')->onDelete('no action');
            $table->foreign('recipient_bank_id')->references('bank_id')->on('banks')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_return_payments');
    }
};
