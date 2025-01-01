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
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->text('resource_slug');
            $table->text('resource_title');
            $table->text('resource_url');
            $table->text('resource_description');
            $table->text('resource_content');
            $table->text('resource_banner')->nullable();
            $table->text('resource_thumbnail')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->text('created_by');
            $table->text('resource_meta_title')->nullable();
            $table->text('resource_meta_keyword')->nullable();
            $table->text('resource_meta_description')->nullable();
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
