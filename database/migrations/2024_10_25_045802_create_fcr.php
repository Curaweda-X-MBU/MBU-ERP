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
        Schema::create('fcr', function (Blueprint $table) {
            $table->integer('fcr_id', true);
            $table->string('name', 50);
            $table->integer('product_id')->index('product_id');
            $table->integer('uom_id')->index('uom_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('fcr', function (Blueprint $table) {
            $table->foreign(['product_id'], 'fcr_products_product_id')->references(['product_id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['uom_id'], 'fcr_uom_uom_id')->references(['uom_id'])->on('uom')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'fcr_users_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fcr');
    }
};
