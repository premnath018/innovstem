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
        Schema::table('blogs', function (Blueprint $table) {
            $table->boolean('active')->default(true)->after('view_count');
        });

        Schema::table('webinars', function (Blueprint $table) {
            $table->integer('attendance_count')->default(0);
            $table->boolean('active')->default(true);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->boolean('active')->default(true); 
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->boolean('active')->default(true); 
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('active')->default(true); 
        });
    }

    public function down(): void
    {
        Schema::table('blogs', fn (Blueprint $table) => $table->dropColumn('active'));
        Schema::table('webinars', fn (Blueprint $table) => $table->dropColumn('active'));
        Schema::table('students', fn (Blueprint $table) => $table->dropColumn('active'));
        Schema::table('resources', fn (Blueprint $table) => $table->dropColumn('active'));
        Schema::table('categories', fn (Blueprint $table) => $table->dropColumn('active'));
    }
};
