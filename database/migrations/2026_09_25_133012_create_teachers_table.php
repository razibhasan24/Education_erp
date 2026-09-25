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
    Schema::create('teachers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('institution_id')->constrained()->onDelete('cascade');
        $table->string('employee_id')->unique();
        $table->string('name_bn');
        $table->string('name_en')->nullable();
        $table->string('designation'); // Headmaster, Assistant Teacher, etc.
        $table->string('department')->nullable(); // Science, Arts, etc.
        $table->string('qualification');
        $table->date('joining_date');
        $table->decimal('salary', 10, 2)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};