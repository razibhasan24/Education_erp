<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_routines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->foreignId('exam_subject_id')->nullable()->constrained('exam_subjects')->nullOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_no')->nullable();
            $table->string('invigilator')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['exam_id', 'class_id', 'exam_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_routines');
    }
};