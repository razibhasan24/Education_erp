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
       Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2);           // 50 = 50% or 500 = 500 Taka
            $table->enum('applicable_to', ['tuition', 'all_fees', 'specific_category'])->default('tuition');
            $table->foreignId('fee_category_id')->nullable()->constrained('fee_categories')->nullOnDelete();
            $table->enum('criteria_type', ['merit', 'need_based', 'sibling', 'staff_ward', 'freedom_fighter', 'special'])->default('merit');
            $table->decimal('min_gpa', 4, 2)->nullable();
            $table->decimal('max_income', 10, 2)->nullable();
            $table->integer('max_recipients')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};