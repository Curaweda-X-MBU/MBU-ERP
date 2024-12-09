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
        Schema::create('areas', function(Blueprint $table) {
            $table->integer('area_id', true);
            $table->string('name', 50);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('companies', function(Blueprint $table) {
            $table->integer('company_id', true);
            $table->string('name', 50);
            $table->string('address', 100)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('customers', function(Blueprint $table) {
            $table->integer('customer_id', true);
            $table->string('name', 50);
            $table->integer('assign_to')->nullable()->index('fk_assign_to');
            $table->tinyInteger('type');
            $table->text('address');
            $table->string('phone', 20)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('tax_num', 50)->nullable();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->dateTime('updated_at')->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('fk_created_by');
            $table->softDeletes();
        });

        Schema::create('departments', function(Blueprint $table) {
            $table->integer('department_id', true);
            $table->string('name', 50);
            $table->integer('company_id')->index('company_id');
            $table->integer('location_id')->index('location_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('feed_histories', function(Blueprint $table) {
            $table->integer('feed_history_id', true);
            $table->integer('project_id')->index('project_id');
            $table->integer('qty');
            $table->integer('pic')->index('pic');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('kandang', function(Blueprint $table) {
            $table->integer('kandang_id', true);
            $table->string('name', 50);
            $table->integer('capacity');
            $table->tinyInteger('type');
            $table->integer('pic')->index('pic');
            $table->integer('location_id')->index('location_id');
            $table->integer('company_id')->index('company_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('locations', function(Blueprint $table) {
            $table->integer('location_id', true);
            $table->string('name', 50);
            $table->text('address');
            $table->integer('area_id')->index('area_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('mortality_histories', function(Blueprint $table) {
            $table->integer('mortality_history_id', true);
            $table->integer('project_id')->index('project_id');
            $table->tinyInteger('type');
            $table->integer('qty');
            $table->integer('pic')->index('pic');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('ovk_histories', function(Blueprint $table) {
            $table->integer('ovk_history_id', true);
            $table->integer('project_id')->index('project_id');
            $table->integer('qty');
            $table->integer('pic')->index('pic');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('preparation_answers', function(Blueprint $table) {
            $table->integer('preparation_answer_id', true);
            $table->integer('question_id')->index('question_id');
            $table->integer('preparation_id')->index('preparation_id');
            $table->date('start_date');
            $table->date('finish_date');
            $table->text('remarks')->nullable();
            $table->tinyInteger('status');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('preparations', function(Blueprint $table) {
            $table->integer('preparation_id', true);
            $table->tinyInteger('question_type');
            $table->integer('pic')->index('pic');
            $table->float('latitude', null, 0)->nullable();
            $table->float('longitude', null, 0)->nullable();
            $table->dateTime('time_in');
            $table->string('image', 100);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('product_categories', function(Blueprint $table) {
            $table->integer('product_category_id', true);
            $table->string('name', 50);
            $table->string('category_code', 10)->unique('category_code');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('product_component_products', function(Blueprint $table) {
            $table->integer('product_component_products_id', true);
            $table->integer('project_id')->index('project_id');
            $table->integer('product_component_id')->index('product_component_id');
            $table->integer('qty');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('product_components', function(Blueprint $table) {
            $table->integer('product_component_id', true);
            $table->integer('supplier_id')->nullable()->index('supplier_id');
            $table->string('name', 50);
            $table->string('brand', 50);
            $table->integer('uom_id')->index('uom_id');
            $table->integer('price');
            $table->integer('tax')->nullable();
            $table->integer('expiry_period')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('product_sub_categories', function(Blueprint $table) {
            $table->integer('product_sub_category_id', true);
            $table->string('name', 50);
            $table->integer('product_category_id')->index('product_category_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('products', function(Blueprint $table) {
            $table->integer('product_id', true);
            $table->string('name', 50);
            $table->string('brand', 50);
            $table->string('sku', 50)->nullable();
            $table->integer('company_id')->index('company_id');
            $table->integer('uom_id')->index('uom_id');
            $table->integer('product_category_id')->index('product_category_id');
            $table->integer('product_sub_category_id')->nullable()->index('product_sub_category_id');
            $table->tinyInteger('can_be_sold');
            $table->tinyInteger('can_be_purchased');
            $table->bigInteger('product_price');
            $table->bigInteger('selling_price')->nullable();
            $table->integer('tax')->nullable();
            $table->integer('expiry_period')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('projects', function(Blueprint $table) {
            $table->integer('project_id', true);
            $table->integer('kandang_id')->index('kandang_id');
            $table->integer('product_id')->index('product_id');
            $table->integer('pic')->nullable()->index('pic');
            $table->integer('qty')->default(0);
            $table->date('start_date');
            $table->date('chick_in_date')->nullable();
            $table->integer('purchase_id');
            $table->integer('total_feed')->nullable();
            $table->integer('remaining_feed')->nullable();
            $table->decimal('fcr_standard', 10)->default(0);
            $table->integer('mortality_standard')->default(0);
            $table->integer('remaining_qty')->nullable();
            $table->integer('culling_standard')->default(0);
            $table->integer('ovk_standard')->nullable();
            $table->integer('total_ovk')->nullable();
            $table->integer('remaining_ovk')->nullable();
            $table->integer('preparation_id')->nullable()->index('preparation_id');
            $table->boolean('is_active')->default(false);
            $table->boolean('status')->default(false);
            $table->integer('approved_by')->nullable()->index('approved_by');
            $table->date('approved_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('questions', function(Blueprint $table) {
            $table->integer('question_id', true);
            $table->text('question');
            $table->tinyInteger('question_type');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('roles', function(Blueprint $table) {
            $table->integer('role_id', true);
            $table->string('name', 50);
            $table->integer('company_id')->index('company_id');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('suppliers', function(Blueprint $table) {
            $table->integer('supplier_id', true);
            $table->string('name', 50);
            $table->string('pic_name', 50);
            $table->tinyInteger('type');
            $table->text('address');
            $table->string('phone', 20);
            $table->string('email', 50);
            $table->string('tax_num', 50)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('uom', function(Blueprint $table) {
            $table->integer('uom_id', true);
            $table->string('name', 50);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index('created_by');
            $table->softDeletes();
        });

        Schema::create('users', function(Blueprint $table) {
            $table->integer('user_id', true);
            $table->string('npk', 50);
            $table->string('name', 50);
            $table->string('email', 50);
            $table->string('password');
            $table->string('phone', 20);
            $table->integer('department_id')->index('department_id');
            $table->integer('role_id')->index('role_id');
            $table->tinyInteger('is_active');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });

        Schema::table('areas', function(Blueprint $table) {
            $table->foreign(['created_by'], 'areas_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('companies', function(Blueprint $table) {
            $table->foreign(['created_by'], 'companies_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('customers', function(Blueprint $table) {
            $table->foreign(['assign_to'], 'fk_assign_to')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'fk_created_by')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('departments', function(Blueprint $table) {
            $table->foreign(['company_id'], 'departments_ibfk_1')->references(['company_id'])->on('companies')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['location_id'], 'departments_ibfk_2')->references(['location_id'])->on('locations')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'departments_ibfk_3')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('feed_histories', function(Blueprint $table) {
            $table->foreign(['project_id'], 'feed_histories_ibfk_1')->references(['project_id'])->on('projects')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['pic'], 'feed_histories_ibfk_2')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'feed_histories_ibfk_3')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('kandang', function(Blueprint $table) {
            $table->foreign(['pic'], 'kandang_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['location_id'], 'kandang_ibfk_2')->references(['location_id'])->on('locations')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['company_id'], 'kandang_ibfk_3')->references(['company_id'])->on('companies')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'kandang_ibfk_4')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('locations', function(Blueprint $table) {
            $table->foreign(['area_id'], 'locations_ibfk_1')->references(['area_id'])->on('areas')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'locations_ibfk_2')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('mortality_histories', function(Blueprint $table) {
            $table->foreign(['project_id'], 'mortality_histories_ibfk_1')->references(['project_id'])->on('projects')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['pic'], 'mortality_histories_ibfk_2')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'mortality_histories_ibfk_3')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('ovk_histories', function(Blueprint $table) {
            $table->foreign(['project_id'], 'ovk_histories_ibfk_1')->references(['project_id'])->on('projects')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['pic'], 'ovk_histories_ibfk_2')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'ovk_histories_ibfk_3')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('preparation_answers', function(Blueprint $table) {
            $table->foreign(['question_id'], 'preparation_answers_ibfk_1')->references(['question_id'])->on('questions')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['preparation_id'], 'preparation_answers_ibfk_2')->references(['preparation_id'])->on('preparations')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'preparation_answers_ibfk_3')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('preparations', function(Blueprint $table) {
            $table->foreign(['pic'], 'preparations_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'preparations_ibfk_2')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('product_categories', function(Blueprint $table) {
            $table->foreign(['created_by'], 'product_categories_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('product_component_products', function(Blueprint $table) {
            $table->foreign(['project_id'], 'product_component_products_ibfk_1')->references(['project_id'])->on('projects')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_component_id'], 'product_component_products_ibfk_2')->references(['product_component_id'])->on('product_components')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'product_component_products_ibfk_3')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('product_components', function(Blueprint $table) {
            $table->foreign(['supplier_id'], 'product_components_ibfk_2')->references(['supplier_id'])->on('suppliers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['uom_id'], 'product_components_ibfk_3')->references(['uom_id'])->on('uom')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'product_components_ibfk_4')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('product_sub_categories', function(Blueprint $table) {
            $table->foreign(['product_category_id'], 'product_sub_categories_ibfk_1')->references(['product_category_id'])->on('product_categories')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'product_sub_categories_ibfk_2')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('products', function(Blueprint $table) {
            $table->foreign(['company_id'], 'products_ibfk_1')->references(['company_id'])->on('companies')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['uom_id'], 'products_ibfk_2')->references(['uom_id'])->on('uom')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_category_id'], 'products_ibfk_3')->references(['product_category_id'])->on('product_categories')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_sub_category_id'], 'products_ibfk_4')->references(['product_sub_category_id'])->on('product_sub_categories')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'products_ibfk_5')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('projects', function(Blueprint $table) {
            $table->foreign(['kandang_id'], 'projects_ibfk_1')->references(['kandang_id'])->on('kandang')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['product_id'], 'projects_ibfk_2')->references(['product_id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['pic'], 'projects_ibfk_3')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['preparation_id'], 'projects_ibfk_4')->references(['preparation_id'])->on('preparations')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['approved_by'], 'projects_ibfk_5')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'projects_ibfk_6')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('questions', function(Blueprint $table) {
            $table->foreign(['created_by'], 'questions_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('roles', function(Blueprint $table) {
            $table->foreign(['company_id'], 'roles_ibfk_1')->references(['company_id'])->on('companies')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'roles_ibfk_2')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('suppliers', function(Blueprint $table) {
            $table->foreign(['created_by'], 'suppliers_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('uom', function(Blueprint $table) {
            $table->foreign(['created_by'], 'uom_ibfk_1')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::table('users', function(Blueprint $table) {
            $table->foreign(['department_id'], 'users_ibfk_2')->references(['department_id'])->on('departments')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['role_id'], 'users_ibfk_3')->references(['role_id'])->on('roles')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropForeign('users_ibfk_2');
            $table->dropForeign('users_ibfk_3');
        });

        Schema::table('uom', function(Blueprint $table) {
            $table->dropForeign('uom_ibfk_1');
        });

        Schema::table('suppliers', function(Blueprint $table) {
            $table->dropForeign('suppliers_ibfk_1');
        });

        Schema::table('roles', function(Blueprint $table) {
            $table->dropForeign('roles_ibfk_1');
            $table->dropForeign('roles_ibfk_2');
        });

        Schema::table('questions', function(Blueprint $table) {
            $table->dropForeign('questions_ibfk_1');
        });

        Schema::table('projects', function(Blueprint $table) {
            $table->dropForeign('projects_ibfk_1');
            $table->dropForeign('projects_ibfk_2');
            $table->dropForeign('projects_ibfk_3');
            $table->dropForeign('projects_ibfk_4');
            $table->dropForeign('projects_ibfk_5');
            $table->dropForeign('projects_ibfk_6');
        });

        Schema::table('products', function(Blueprint $table) {
            $table->dropForeign('products_ibfk_1');
            $table->dropForeign('products_ibfk_2');
            $table->dropForeign('products_ibfk_3');
            $table->dropForeign('products_ibfk_4');
            $table->dropForeign('products_ibfk_5');
        });

        Schema::table('product_sub_categories', function(Blueprint $table) {
            $table->dropForeign('product_sub_categories_ibfk_1');
            $table->dropForeign('product_sub_categories_ibfk_2');
        });

        Schema::table('product_components', function(Blueprint $table) {
            $table->dropForeign('product_components_ibfk_2');
            $table->dropForeign('product_components_ibfk_3');
            $table->dropForeign('product_components_ibfk_4');
        });

        Schema::table('product_component_products', function(Blueprint $table) {
            $table->dropForeign('product_component_products_ibfk_1');
            $table->dropForeign('product_component_products_ibfk_2');
            $table->dropForeign('product_component_products_ibfk_3');
        });

        Schema::table('product_categories', function(Blueprint $table) {
            $table->dropForeign('product_categories_ibfk_1');
        });

        Schema::table('preparations', function(Blueprint $table) {
            $table->dropForeign('preparations_ibfk_1');
            $table->dropForeign('preparations_ibfk_2');
        });

        Schema::table('preparation_answers', function(Blueprint $table) {
            $table->dropForeign('preparation_answers_ibfk_1');
            $table->dropForeign('preparation_answers_ibfk_2');
            $table->dropForeign('preparation_answers_ibfk_3');
        });

        Schema::table('ovk_histories', function(Blueprint $table) {
            $table->dropForeign('ovk_histories_ibfk_1');
            $table->dropForeign('ovk_histories_ibfk_2');
            $table->dropForeign('ovk_histories_ibfk_3');
        });

        Schema::table('mortality_histories', function(Blueprint $table) {
            $table->dropForeign('mortality_histories_ibfk_1');
            $table->dropForeign('mortality_histories_ibfk_2');
            $table->dropForeign('mortality_histories_ibfk_3');
        });

        Schema::table('locations', function(Blueprint $table) {
            $table->dropForeign('locations_ibfk_1');
            $table->dropForeign('locations_ibfk_2');
        });

        Schema::table('kandang', function(Blueprint $table) {
            $table->dropForeign('kandang_ibfk_1');
            $table->dropForeign('kandang_ibfk_2');
            $table->dropForeign('kandang_ibfk_3');
            $table->dropForeign('kandang_ibfk_4');
        });

        Schema::table('feed_histories', function(Blueprint $table) {
            $table->dropForeign('feed_histories_ibfk_1');
            $table->dropForeign('feed_histories_ibfk_2');
            $table->dropForeign('feed_histories_ibfk_3');
        });

        Schema::table('departments', function(Blueprint $table) {
            $table->dropForeign('departments_ibfk_1');
            $table->dropForeign('departments_ibfk_2');
            $table->dropForeign('departments_ibfk_3');
        });

        Schema::table('customers', function(Blueprint $table) {
            $table->dropForeign('fk_assign_to');
            $table->dropForeign('fk_created_by');
        });

        Schema::table('companies', function(Blueprint $table) {
            $table->dropForeign('companies_ibfk_1');
        });

        Schema::table('areas', function(Blueprint $table) {
            $table->dropForeign('areas_ibfk_1');
        });

        Schema::dropIfExists('users');

        Schema::dropIfExists('uom');

        Schema::dropIfExists('suppliers');

        Schema::dropIfExists('roles');

        Schema::dropIfExists('questions');

        Schema::dropIfExists('projects');

        Schema::dropIfExists('products');

        Schema::dropIfExists('product_sub_categories');

        Schema::dropIfExists('product_components');

        Schema::dropIfExists('product_component_products');

        Schema::dropIfExists('product_categories');

        Schema::dropIfExists('preparations');

        Schema::dropIfExists('preparation_answers');

        Schema::dropIfExists('ovk_histories');

        Schema::dropIfExists('mortality_histories');

        Schema::dropIfExists('locations');

        Schema::dropIfExists('kandang');

        Schema::dropIfExists('feed_histories');

        Schema::dropIfExists('departments');

        Schema::dropIfExists('customers');

        Schema::dropIfExists('companies');

        Schema::dropIfExists('areas');
    }
};
