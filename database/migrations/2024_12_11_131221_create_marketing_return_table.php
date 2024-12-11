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
        Schema::create('marketing_returns', function (Blueprint $table) {
            $table->id('marketing_return_id');
            $table->unsignedBigInteger('marketing_id')->unique();
            $table->string('invoice_number');
            $table->tinyInteger('payment_return_status');
            $table->tinyInteger('return_status');
            $table->bigInteger('total_return');
            $table->dateTime('return_at');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('marketing_id')->references('marketing_id')->on('marketings')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('marketings', function(Blueprint $table) {
            $table->unsignedBigInteger('marketing_return_id')->nullable()->unique();
            $table->foreign('marketing_return_id')->references('marketing_return_id')->on('marketing_returns')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_returns');
    }
};
