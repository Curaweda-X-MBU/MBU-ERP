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
        Schema::create('banks', function(Blueprint $table) {
            $table->integer('bank_id', true);
            $table->string('code', 5);
            $table->string('short_name', 5)->nullable();
            $table->string('name', 50);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('banks', function(Blueprint $table) {
            $table->foreign(['created_by'], 'banks_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banks', function(Blueprint $table) {
            $table->dropForeign('banks_ibfk_1');
        });

        Schema::dropIfExists('banks');
    }
};
