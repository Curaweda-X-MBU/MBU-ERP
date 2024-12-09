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
        Schema::table('feed_histories', function(Blueprint $table) {
            $table->dropForeign('feed_histories_ibfk_1');
        });

        Schema::table('mortality_histories', function(Blueprint $table) {
            $table->dropForeign('mortality_histories_ibfk_1');
        });

        Schema::table('ovk_histories', function(Blueprint $table) {
            $table->dropForeign('ovk_histories_ibfk_1');
        });

        Schema::table('product_component_products', function(Blueprint $table) {
            $table->dropForeign('product_component_products_ibfk_1');
        });

        Schema::table('projects', function(Blueprint $table) {
            $table->dropForeign('projects_ibfk_1');
            $table->dropForeign('projects_ibfk_2');
            $table->dropForeign('projects_ibfk_3');
            $table->dropForeign('projects_ibfk_4');
            $table->dropForeign('projects_ibfk_5');
            $table->dropForeign('projects_ibfk_6');
        });

        Schema::dropIfExists('projects');

        Schema::create('projects', function(Blueprint $table) {
            $table->integer('project_id', true);
            $table->integer('product_id')->index('product_id');
            $table->integer('kandang_id')->index('kandang_id');
            $table->integer('capacity');
            $table->tinyInteger('farm_type');
            $table->integer('period');
            $table->string('pic', 100);
            $table->integer('fcr_id')->index('fcr_id');
            $table->integer('target_depletion');
            $table->tinyInteger('chickin_status');
            $table->tinyInteger('project_status');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::table('projects', function(Blueprint $table) {
            $table->foreign(['kandang_id'], 'projects_kandang_kandang_id')->references(['kandang_id'])->on('kandang')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_id'], 'projects_products_product_id')->references(['product_id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['fcr_id'], 'projects_fcr_fcr_id')->references(['fcr_id'])->on('fcr')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'projects_users_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('project_phases', function(Blueprint $table) {
            $table->integer('project_phase_id', true);
            $table->string('name', 50);
            $table->date('start_date_estimate');
            $table->date('end_date_estimate');
            $table->integer('project_id')->index('project_id');
        });

        Schema::table('project_phases', function(Blueprint $table) {
            $table->foreign(['project_id'], 'project_phases_projects_project_id')->references(['project_id'])->on('projects')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('project_budgets', function(Blueprint $table) {
            $table->integer('project_budget_id', true);
            $table->string('item', 150);
            $table->integer('qty');
            $table->bigInteger('price');
            $table->bigInteger('total');
            $table->integer('project_id')->index('project_id');
        });

        Schema::table('project_budgets', function(Blueprint $table) {
            $table->foreign(['project_id'], 'project_budgets_projects_project_id')->references(['project_id'])->on('projects')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('project_recordings', function(Blueprint $table) {
            $table->integer('project_recording_id', true);
            $table->string('unit_name', 50);
            $table->string('interval', 50);
            $table->integer('project_id')->index('project_id');
        });

        Schema::table('project_recordings', function(Blueprint $table) {
            $table->foreign(['project_id'], 'project_recordings_projects_project_id')->references(['project_id'])->on('projects')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
