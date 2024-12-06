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
        Schema::create('ph_performances', function (Blueprint $table) {
            $table->integer('ph_performance_id', true);
            $table->integer('kandang_id')->index('kandang_id');
            $table->date('chick_in_date');
            $table->integer('population');
            $table->integer('supplier_id')->index('supplier_id');
            $table->string('hatchery', 50);
            $table->integer('death');
            $table->integer('culling');
            $table->integer('depletion');
            $table->integer('percentage_depletion');
            $table->integer('bw');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('ph_performances', function (Blueprint $table) {
            $table->foreign(['kandang_id'], 'ph_performances_ibfk_1')->references(['kandang_id'])->on('kandang')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['supplier_id'], 'ph_performances_ibfk_2')->references(['supplier_id'])->on('suppliers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'ph_performances_ibfk_3')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ph_performances');
    }
};
