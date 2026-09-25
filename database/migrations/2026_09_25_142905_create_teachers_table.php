<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('teacher_id')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('designation')->nullable(); // Head Teacher, Assistant Teacher
            $table->string('department')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('religion')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('nid')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('present_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('photo')->nullable();
            $table->string('qualification')->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->enum('employment_type', ['permanent', 'contractual', 'part_time'])->default('permanent');
            $table->enum('status', ['active', 'inactive', 'resigned', 'retired'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
