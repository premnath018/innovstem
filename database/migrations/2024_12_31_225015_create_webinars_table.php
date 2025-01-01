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
        Schema::create('webinars', function (Blueprint $table) {
            $table->id();
            $table->text('webinar_slug');
            $table->text('webinar_title');
            $table->text('webinar_description');
            $table->text('webinar_content');
            $table->text('webinar_banner')->nullable();
            $table->text('webinar_thumbnail')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->text('created_by');
            $table->text('webinar_meta_title')->nullable();
            $table->text('webinar_meta_keyword')->nullable();
            $table->text('webinar_meta_description')->nullable();
            $table->timestamp('webinar_date_time');
            $table->integer('view_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webinars');
    }
};
