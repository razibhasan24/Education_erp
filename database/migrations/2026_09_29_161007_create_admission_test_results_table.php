<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_test_results', function (Blueprint $table) {
            $table->id();
            $table->string('roll_no')->unique();
            $table->string('student_name');
            $table->string('father_name');
            $table->string('mother_name')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();
            $table->decimal('bangla', 6, 2)->default(0);
            $table->decimal('english', 6, 2)->default(0);
            $table->decimal('math', 6, 2)->default(0);
            $table->decimal('general_knowledge', 6, 2)->default(0);
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->integer('merit_position')->nullable();
            $table->enum('status', ['pending', 'passed', 'failed', 'waiting'])->default('pending');
            $table->text('remarks')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_test_results');
    }
};