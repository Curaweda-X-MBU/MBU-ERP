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
        Schema::create('purchase_item_receptions', function (Blueprint $table) {
            $table->integer('purchase_item_reception_id', true);
            $table->integer('purchase_item_id')->index('purchase_item_id');
            $table->dateTime('received_date');
            $table->string('travel_number');
            $table->string('travel_number_document')->nullable();
            $table->string('vehicle_number');
            $table->bigInteger('total_received')->default(0);
            $table->bigInteger('total_retur')->default(0);
        });

        Schema::table('purchase_item_receptions', function (Blueprint $table) {
            $table->foreign(['purchase_item_id'], 'items_receptions_purchase_item_id')->references(['purchase_item_id'])->on('purchase_items')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('purchase_items', function (Blueprint $table) {
            $table->bigInteger('total_not_received')->default(0)->after('total');
            $table->bigInteger('amount_not_received')->default(0)->after('total_not_received');
            $table->bigInteger('total_received')->default(0)->after('amount_not_received');
            $table->bigInteger('amount_received')->default(0)->after('total_received');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->bigInteger('total_amount_received')->default(0)->after('approval_line');
            $table->bigInteger('total_amount_not_received')->default(0)->after('total_amount_received');
            $table->bigInteger('total_amount_retur')->default(0)->after('total_amount_not_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_item_recepients');
    }
};
