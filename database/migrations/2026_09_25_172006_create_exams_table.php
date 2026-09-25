<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // যেমন: প্রথম সাময়িক, বার্ষিক, মডেল টেস্ট
            $table->string('name_bn')->nullable();
            $table->enum('exam_type', ['class_test', 'monthly', 'half_yearly', 'annual', 'model_test', 'admission'])->default('monthly');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false); // রেজাল্ট প্রকাশ কিনা
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
