<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('marketings', function(Blueprint $table) {
            $table->id('marketing_id');
            $table->string('id_marketing')->nullable();
            $table->tinyInteger('is_approved')->nullable();
            $table->integer('approver_id')->nullable();
            $table->text('approval_notes')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->integer('company_id');
            $table->integer('customer_id');
            $table->dateTime('sold_at');
            $table->dateTime('realized_at')->nullable();
            $table->string('doc_reference')->nullable();
            $table->text('notes')->nullable();
            $table->integer('sales_id');
            $table->integer('tax')->nullable();
            $table->integer('discount')->nullable();
            $table->bigInteger('sub_total');
            $table->bigInteger('grand_total');
            $table->tinyInteger('payment_status');
            $table->tinyInteger('marketing_status');
            $table->integer('created_by');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('approver_id')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign('company_id')->references('company_id')->on('companies')->onUpdate('no action')->onDelete('no action');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onUpdate('no action')->onDelete('no action');
            $table->foreign('sales_id')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign('created_by')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('marketing_addit_prices', function(Blueprint $table) {
            $table->id('marketing_addit_price_id');
            $table->unsignedBigInteger('marketing_id');
            $table->string('item', 100);
            $table->bigInteger('price');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('marketing_id')->references('marketing_id')->on('marketings')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('marketing_payments', function(Blueprint $table) {
            $table->bigIncrements('marketing_payment_id');
            $table->unsignedBigInteger('marketing_id');
            $table->tinyInteger('is_approved')->nullable();
            $table->integer('approver_id')->nullable();
            $table->text('approval_notes')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->string('payment_method', 50);
            $table->integer('bank_id');
            $table->string('payment_reference')->nullable();
            $table->string('transaction_number')->nullable();
            $table->bigInteger('payment_nominal');
            $table->datetime('payment_at');
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('approver_id')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign('marketing_id')->references('marketing_id')->on('marketings')->onUpdate('no action')->onDelete('no action');
            $table->foreign('bank_id')->references('bank_id')->on('banks')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('marketing_products', function(Blueprint $table) {
            $table->id('marketing_product_id');
            $table->unsignedBigInteger('marketing_id');
            $table->integer('kandang_id');
            $table->integer('product_id');
            $table->bigInteger('price');
            $table->integer('weight_avg');
            $table->integer('uom_id');
            $table->integer('qty');
            $table->integer('weight_total');
            $table->bigInteger('total_price');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('marketing_id')->references('marketing_id')->on('marketings')->onUpdate('no action')->onDelete('no action');
            $table->foreign('kandang_id')->references('kandang_id')->on('kandang')->onUpdate('no action')->onDelete('no action');
            $table->foreign('product_id')->references('product_id')->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign('uom_id')->references('uom_id')->on('uom')->onUpdate('no action')->onDelete('no action');

        });

        Schema::create('marketing_delivery_vehicles', function(Blueprint $table) {
            $table->id('marketing_delivery_vehicle_id');
            $table->unsignedBigInteger('marketing_id');
            $table->string('plat_number');
            $table->integer('qty');
            $table->integer('uom_id');
            $table->dateTime('exit_at');
            $table->integer('sender_id');
            $table->string('driver_name');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('marketing_id')->references('marketing_id')->on('marketings')->onUpdate('no action')->onDelete('no action');
            $table->foreign('uom_id')->references('uom_id')->on('uom')->onUpdate('no action')->onDelete('no action');
            $table->foreign('sender_id')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketings');
        Schema::dropIfExists('marketing_addit_prices');
        Schema::dropIfExists('marketing_payments');
        Schema::dropIfExists('marketing_products');
        Schema::dropIfExists('marketing_delivery_vehicles');
    }
};
