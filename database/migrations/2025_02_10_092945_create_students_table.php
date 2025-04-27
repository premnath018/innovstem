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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link to user
            $table->string('name');
            $table->string('mobile')->unique();
            $table->string('standard'); 
            $table->string('ambition')->nullable();
            $table->string('parent_no')->nullable();
            $table->integer('age');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('district');
            $table->string('city');
            $table->string('pincode');
            $table->text('address');
            $table->string('state');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
