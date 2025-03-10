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
        Schema::create('notifications', function(Blueprint $table) {
            $table->id('notification_id');
            $table->integer('role_id');
            $table->string('message');
            $table->string('url');
            $table->string('module');
            $table->integer('foreign_id');
            $table->tinyInteger('is_done')->default(0);
            $table->integer('location_id');
            $table->timestamps();

            $table->foreign('role_id')->references('role_id')->on('roles')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
