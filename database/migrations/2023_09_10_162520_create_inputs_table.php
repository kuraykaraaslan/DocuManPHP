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
        Schema::create('inputs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('template_id');
            $table->string('name');
            $table->string('type')->default('string');
            $table->string('default')->nullable();
            $table->boolean('required')->default(0);
            $table->json('options')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('description')->nullable();
            $table->string('validation')->nullable();
            $table->string('validation_message')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inputs');
    }
};
