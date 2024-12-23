<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::beginTransaction();
        try {
            Schema::table('marketings', function(Blueprint $table) {
                $table->double('tax')->nullable()->change();
            });
            Schema::table('marketing_products', function(Blueprint $table) {
                $table->double('weight_avg')->change();
                $table->double('weight_total')->change();
            });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::beginTransaction();
        try {
            Schema::table('marketings', function(Blueprint $table) {
                $table->integer('tax')->nullable()->change();
            });
            Schema::table('marketing_products', function(Blueprint $table) {
                $table->integer('weight_avg')->change();
                $table->integer('weight_total')->change();
            });
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
};
