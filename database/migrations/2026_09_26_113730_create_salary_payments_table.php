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
       Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('month'); // 01-12
            $table->integer('year');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('allowance', 10, 2)->default(0);
            $table->decimal('deduction', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->date('payment_date');
            $table->string('payment_method')->default('cash');
            $table->string('remarks')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['teacher_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};