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
        Schema::table('expenses', function(Blueprint $table) {
            $table->json('approval_line')->nullable()->after('is_approved');

            $table->dropForeign(['approver_id']);
            $table->dropColumn(['approver_id', 'approval_notes', 'approved_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function(Blueprint $table) {
            $table->dropColumn(['approval_line']);

            $table->integer('approver_id')->nullable();
            $table->text('approval_notes')->nullable();
            $table->dateTime('approved_at')->nullable();

            $table->foreign('approver_id')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }
};
