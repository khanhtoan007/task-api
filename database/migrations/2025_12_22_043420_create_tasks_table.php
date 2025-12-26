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
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('description');
            $table->string('status');
            $table->foreignUuid('created_by')->references('id')->on('users');
            $table->foreignUuid('assigned_to')->references('id')->on('users');
            $table->foreignUuid('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->references('id')->on('tasks')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
