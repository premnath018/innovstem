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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->text('course_slug');
            $table->text('course_title');
            $table->text('content_short_description')->nullable();
            $table->text('content_long_description')->nullable();
            $table->text('course_content');
            $table->json('learning_materials')->nullable();
            $table->text('course_banner')->nullable();
            $table->text('course_thumbnail')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->text('created_by');
            $table->text('course_meta_title')->nullable();
            $table->text('course_meta_keyword')->nullable();
            $table->text('course_meta_description')->nullable();
            $table->foreignId('class_level_id')->nullable()->change()->constrained('class_levels')->nullOnDelete();
            $table->integer('view_count')->default(0);
            $table->integer('enrolment_count')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
