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
        Schema::create('product_supplier', function(Blueprint $table) {
            $table->id('product_supplier_id');
            $table->integer('product_id');
            $table->integer('supplier_id');
            $table->bigInteger('product_price')->nullable();
            $table->bigInteger('selling_price')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->on('products')->references('product_id')->onDelete('cascade');
            $table->foreign('supplier_id')->on('suppliers')->references('supplier_id')->onDelete('cascade');
        });

        Schema::create('nonstock_supplier', function(Blueprint $table) {
            $table->id('nonstock_supplier_id');
            $table->integer('nonstock_id');
            $table->integer('supplier_id');
            $table->timestamps();

            $table->foreign('nonstock_id')->on('nonstocks')->references('nonstock_id')->onDelete('cascade');
            $table->foreign('supplier_id')->on('suppliers')->references('supplier_id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
        Schema::dropIfExists('nonstock_supplier');
    }
};
