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
       Schema::create('hostel_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('hostel_id')->constrained('hostels')->onDelete('cascade');
            $table->foreignId('hostel_room_id')->constrained('hostel_rooms')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'left'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostel_allocations');
    }
};
