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
              // Quizzes Table
              Schema::create('quizzes', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('quizable_type'); // Polymorphic relation to blogs, webinars, courses
                $table->unsignedBigInteger('quizable_id');
                $table->timestamps();
            });
    
            // Questions Table
            Schema::create('questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
                $table->text('question_text');
                $table->timestamps();
            });
    
            // Options Table
            Schema::create('options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('question_id')->constrained()->onDelete('cascade');
                $table->string('option_text');
                $table->boolean('is_correct')->default(false);
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
    }
};
