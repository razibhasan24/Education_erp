<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('institution_id')->constrained()->onDelete('cascade');
        $table->string('student_id')->unique(); // Auto-generated ID
        $table->string('name_bn'); // Name in Bengali
        $table->string('name_en')->nullable();
        $table->date('dob');
        $table->enum('gender', ['male', 'female', 'other']);
        $table->string('religion')->nullable();
        $table->string('father_name');
        $table->string('mother_name');
        $table->string('guardian_phone');
        $table->text('present_address');
        $table->text('permanent_address')->nullable();
        $table->foreignId('current_class_id')->nullable()->constrained('classes');
        $table->foreignId('current_section_id')->nullable()->constrained('sections');
        $table->date('admission_date');
        $table->string('blood_group')->nullable();
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