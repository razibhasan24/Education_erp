<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            $table->integer('full_marks')->default(100);
            $table->integer('pass_marks')->default(33);
            $table->integer('written_marks')->default(0); // ঐচ্ছিক ব্রেকডাউন
            $table->integer('mcq_marks')->default(0);
            $table->integer('practical_marks')->default(0);
            $table->date('exam_date')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'class_id', 'subject_id', 'group_id'], 'unique_exam_subject');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_subjects');
    }
};
