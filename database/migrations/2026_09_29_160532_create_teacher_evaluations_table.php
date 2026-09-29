<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->integer('teaching_quality');    // 1-5
            $table->integer('communication');       // 1-5
            $table->integer('behavior');            // 1-5
            $table->integer('punctuality');         // 1-5
            $table->integer('knowledge');           // 1-5
            $table->decimal('average_rating', 3, 2);
            $table->text('comments')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();

            $table->unique(['teacher_id', 'student_id', 'class_id', 'subject_id'], 'unique_evaluation');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_evaluations');
    }
};