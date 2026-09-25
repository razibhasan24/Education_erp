<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('exam_subject_id')->constrained('exam_subjects')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->decimal('written_marks', 6, 2)->default(0);
            $table->decimal('mcq_marks', 6, 2)->default(0);
            $table->decimal('practical_marks', 6, 2)->default(0);
            $table->decimal('total_marks', 6, 2)->default(0);
            $table->string('grade', 5)->nullable();
            $table->decimal('gpa', 4, 2)->nullable();
            $table->boolean('is_absent')->default(false);
            $table->string('remarks')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['exam_subject_id', 'student_id'], 'unique_mark_entry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
