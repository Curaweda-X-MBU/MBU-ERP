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
        Schema::create('audits', function (Blueprint $table) {
            $table->integer('audit_id', true);
            $table->string('title', 100);
            $table->text('description');
            $table->integer('department_id')->index('department_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });

        Schema::table('audits', function (Blueprint $table) {
            $table->foreign(['department_id'], 'audits_ibfk_1')->references(['department_id'])->on('departments')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropForeign('audits_ibfk_1');
        });
        Schema::dropIfExists('audits');
    }
};
