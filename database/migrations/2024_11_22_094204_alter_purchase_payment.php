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
        Schema::table('purchase_payments', function (Blueprint $table) {
            $table->integer('approved_by')->index('approved_by')->nullable()->after('status');
            $table->string('reason')->nullable()->after('approved_by');
            $table->foreign(['approved_by'], 'payments_users_approved_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
