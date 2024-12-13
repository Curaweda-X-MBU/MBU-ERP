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
        Schema::create('ph_complaints', function(Blueprint $table) {
            $table->integer('ph_complaint_id', true);
            $table->string('product', 50);
            $table->tinyInteger('type');
            $table->integer('population');
            $table->date('investigation_date');
            $table->text('description');
            $table->json('symptoms')->nullable();
            $table->integer('total_deaths');
            $table->integer('total_culling');
            $table->integer('company_id')->index('company_id');
            $table->integer('location_id')->index('location_id');
            $table->integer('supplier_id')->index('supplier_id');
            $table->integer('created_by')->nullable()->index('created_by');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
            $table->softDeletes();
        });

        Schema::table('ph_complaints', function(Blueprint $table) {
            $table->foreign(['company_id'], 'ph_complaints_ibfk_1')->references(['company_id'])->on('companies')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['location_id'], 'ph_complaints_ibfk_2')->references(['location_id'])->on('locations')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['supplier_id'], 'ph_complaints_ibfk_3')->references(['supplier_id'])->on('suppliers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['created_by'], 'ph_complaints_ibfk_4')->references(['user_id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('ph_chick_in', function(Blueprint $table) {
            $table->integer('ph_chick_in_id', true);
            $table->integer('ph_complaint_id')->index('ph_complaint_id');
            $table->date('date');
            $table->string('travel_letter_number');
            $table->time('delivery_time');
            $table->time('reception_time');
            $table->time('duration');
            $table->string('hatchery', 50);
            $table->string('grade', 10);
            $table->integer('total_box');
            $table->integer('total_heads');
            $table->softDeletes();
        });

        Schema::table('ph_chick_in', function(Blueprint $table) {
            $table->foreign(['ph_complaint_id'], 'ph_chick_in_ibfk_1')->references(['ph_complaint_id'])->on('ph_complaints')->onUpdate('no action')->onDelete('no action');
        });

        Schema::create('ph_mortalities', function(Blueprint $table) {
            $table->integer('ph_mortality_id', true);
            $table->integer('ph_complaint_id')->index('ph_complaint_id');
            $table->integer('day');
            $table->integer('death');
            $table->integer('culling');
            $table->softDeletes();
        });

        Schema::table('ph_mortalities', function(Blueprint $table) {
            $table->foreign(['ph_complaint_id'], 'ph_mortalities_ibfk_1')->references(['ph_complaint_id'])->on('ph_complaints')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ph_complaints', function(Blueprint $table) {
            $table->dropForeign('ph_complaints_ibfk_1');
            $table->dropForeign('ph_complaints_ibfk_2');
            $table->dropForeign('ph_complaints_ibfk_3');
            $table->dropForeign('ph_complaints_ibfk_4');
        });
        Schema::table('ph_chick_in', function(Blueprint $table) {
            $table->dropForeign('ph_chick_in_ibfk_1');
        });
        Schema::table('ph_mortalities', function(Blueprint $table) {
            $table->dropForeign('ph_mortalities_ibfk_1');
        });
        Schema::dropIfExists('ph_complaints');
        Schema::dropIfExists('ph_chick_in');
        Schema::dropIfExists('ph_mortalities');
    }
};
