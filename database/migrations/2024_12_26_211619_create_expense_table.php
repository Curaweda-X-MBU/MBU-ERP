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
        Schema::create('expenses', function(Blueprint $table) {
            $table->id('expense_id');
            $table->tinyInteger('is_approved')->nullable();
            $table->integer('approver_id')->nullable();
            $table->text('approval_notes')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->integer('location_id');
            $table->tinyInteger('category');
            $table->bigInteger('grand_total');
            $table->tinyInteger('expense_status');
            $table->integer('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('approver_id')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign('location_id')->references('location_id')->on('locations')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('expense_kandang', function(Blueprint $table) {
            $table->id('expense_kandang_id');
            $table->unsignedBigInteger('expense_id');
            $table->integer('kandang_id');

            $table->foreign('expense_id')->references('expense_id')->on('expenses')->onUpdate('no action')->onDelete('no action');
            $table->foreign('kandang_id')->references('kandang_id')->on('kandang')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('expense_items', function(Blueprint $table) {
            $table->id('expense_item_id');
            $table->unsignedBigInteger('expense_id');
            $table->string('sub_category');
            $table->string('price_nominal');

            $table->foreign('expense_id')->references('expense_id')->on('expenses')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('expense_sub_categories', function(Blueprint $table) {
            $table->id('expense_sub_category_id');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_kandang');
        Schema::dropIfExists('expense_items');
        Schema::dropIfExists('expense_sub_categories');
        Schema::dropIfExists('expenses');
    }
};
