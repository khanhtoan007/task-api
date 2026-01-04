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

            $table->uuid('created_by');
            $table->uuid('assigned_to')->nullable();

            $table->uuid('project_id')->nullable();
            $table->uuid('parent_id')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // foreign keys
            $table->foreign('created_by')
                ->references('id')->on('users');

            $table->foreign('assigned_to')
                ->references('id')->on('users');

            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->cascadeOnDelete();

            $table->foreign('parent_id')
                ->references('id')->on('tasks')
                ->cascadeOnDelete();
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
