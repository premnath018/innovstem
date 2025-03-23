<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->date('deadline_date')->nullable()->after('status');
            $table->text('remarks')->nullable()->after('deadline_date');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium')->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['deadline_date', 'remarks', 'priority']);
        });
    }
};