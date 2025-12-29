<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->uuid('created_by')->nullable()->after('status');
            $table->uuid('assigned_to')->nullable()->after('created_by');
            $table->uuid('project_id')->nullable()->after('assigned_to');
            $table->uuid('parent_id')->nullable()->after('project_id');
        });

        DB::table('tasks')->update([
            'created_by' => DB::raw('user_id'),
            'assigned_to' => DB::raw('user_id'),
        ]);

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('assigned_to')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('tasks')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['parent_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('status');
        });

        DB::table('tasks')->update([
            'user_id' => DB::raw('created_by'),
        ]);

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'assigned_to', 'project_id', 'parent_id']);
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
