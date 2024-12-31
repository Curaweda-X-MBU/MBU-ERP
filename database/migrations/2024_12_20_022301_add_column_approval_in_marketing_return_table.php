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
        Schema::table('marketing_returns', function(Blueprint $table) {
            $table->tinyInteger('is_approved')->nullable()->after('marketing_id');
            $table->integer('approver_id')->nullable()->after('is_approved');
            $table->text('approval_notes')->nullable()->after('approver_id');
            $table->dateTime('approved_at')->nullable()->after('approval_notes');

            $table->foreign('approver_id')->references('user_id')->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_returns', function(Blueprint $table) {
            $table->dropForeign(['approver_id']);

            $table->dropColumn('approver_id');
            $table->dropColumn('is_approved');
            $table->dropColumn('approval_notes');
            $table->dropColumn('approved_at');
        });
    }
};
