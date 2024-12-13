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
        Schema::table('companies', function(Blueprint $table) {
            $table->string('alias', 3)->nullable()->after('name');
        });

        Schema::table('suppliers', function(Blueprint $table) {
            $table->string('alias', 3)->nullable()->after('name');
        });

        Schema::create('purchases', function(Blueprint $table) {
            $table->integer('purchase_id', true);
            $table->string('pr_number');
            $table->string('po_number')->nullable();
            $table->integer('supplier_id')->index('supplier_id');
            $table->date('require_date');
            $table->bigInteger('total_before_tax')->default(0);
            $table->bigInteger('total_other_amount')->default(0);
            $table->bigInteger('total_received')->default(0);
            $table->bigInteger('total_not_received')->default(0);
            $table->bigInteger('total_retur')->default(0);
            $table->bigInteger('total_payment')->default(0);
            $table->bigInteger('total_remaining_payment')->default(0);
            $table->bigInteger('grand_total')->default(0);
            $table->string('notes')->nullable();
            $table->tinyInteger('status');
            $table->json('approval_line')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('purchases', function(Blueprint $table) {
            $table->foreign(['supplier_id'], 'purchases_suppliers_supplier_id')->references(['supplier_id'])->on('suppliers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'purchases_users_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('purchase_items', function(Blueprint $table) {
            $table->integer('purchase_item_id', true);
            $table->integer('purchase_id')->index('purchase_id');
            $table->integer('product_id')->index('product_id');
            $table->integer('warehouse_id')->index('warehouse_id');
            $table->integer('uom_id')->index('uom_id');
            $table->bigInteger('qty');
            $table->bigInteger('price');
            $table->bigInteger('total');
        });

        Schema::table('purchase_items', function(Blueprint $table) {
            $table->foreign(['purchase_id'], 'items_purchases_purchase_id')->references(['purchase_id'])->on('purchases')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_id'], 'items_products_product_id')->references(['product_id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['warehouse_id'], 'items_warehouses_warehouse_id')->references(['warehouse_id'])->on('warehouses')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['uom_id'], 'items_uom_uom_id')->references(['uom_id'])->on('uom')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('purchase_others', function(Blueprint $table) {
            $table->integer('purchase_other_id', true);
            $table->integer('purchase_id')->index('purchase_id');
            $table->string('name');
            $table->bigInteger('amount');
        });

        Schema::table('purchase_others', function(Blueprint $table) {
            $table->foreign(['purchase_id'], 'others_purchases_purchase_id')->references(['purchase_id'])->on('purchases')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('purchase_payments', function(Blueprint $table) {
            $table->integer('purchase_payment_id', true);
            $table->integer('purchase_id')->index('purchase_id');
            $table->date('payment_date');
            $table->tinyInteger('payment_method');
            $table->integer('own_bank_id')->index('own_bank_id');
            $table->string('own_account_number');
            $table->integer('recipient_bank_id')->index('recipient_bank_id');
            $table->string('recipient_account_number');
            $table->string('ref_number')->nullable();
            $table->string('transaction_number')->nullable();
            $table->bigInteger('bank_charge');
            $table->bigInteger('amount');
            $table->string('document')->nullable();
            $table->tinyInteger('status');
        });

        Schema::table('purchase_payments', function(Blueprint $table) {
            $table->foreign(['purchase_id'], 'payments_purchases_purchase_id')->references(['purchase_id'])->on('purchases')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['own_bank_id'], 'payments_bank_own_bank_id')->references(['bank_id'])->on('banks')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['recipient_bank_id'], 'payments_bank_recipient_bank_id')->references(['bank_id'])->on('banks')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
