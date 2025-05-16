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
        Schema::create('counseling_packages', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50);
            $table->string('package_name', 100);
            $table->decimal('price_inr', 10, 2);
            $table->string('duration', 50)->nullable();
            $table->text('includes')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counseling_packages');
    }
};
