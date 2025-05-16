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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('mobile_number', 15);
            $table->string('email', 100);
            $table->string('class', 20)->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('ambition', 255)->nullable();
            $table->enum('user_type', ['Student', 'Parent', 'Teacher']);
            $table->foreignId('package_id')->constrained('counseling_packages');
            $table->foreignId('slot_id')->constrained('slots');
            $table->string('transaction_id', 100)->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->enum('payment_status', ['Pending', 'Paid', 'Failed'])->default('Pending');
            $table->text('note')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->index('user_type', 'idx_user_type');
            $table->index('transaction_id', 'idx_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
