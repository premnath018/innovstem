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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->text('blog_slug');
            $table->text('blog_title');
            $table->text('blog_description');
            $table->text('blog_content');
            $table->text('blog_banner')->nullable();
            $table->text('blog_thumbnail')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->text('created_by');
            $table->text('blog_meta_title')->nullable();
            $table->text('blog_meta_keyword')->nullable();
            $table->text('blog_meta_description')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
