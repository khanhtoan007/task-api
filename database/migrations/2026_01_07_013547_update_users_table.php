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
    Schema::table('users', function (Blueprint $table) {
        $table->string('phone')->nullable();
        $table->string('sex')->nullable();
        $table->uuid('contest')->nullable()->references('id')->on('contests');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['contest']);
            $table->dropColumn(['phone', 'sex', 'contest']);
        });
    }
};
