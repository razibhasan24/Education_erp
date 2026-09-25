<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tuition Fee, Exam Fee, Library Fee
            $table->string('name_bn')->nullable();
            $table->enum('fee_type', ['monthly', 'one_time', 'yearly', 'session'])->default('monthly');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_categories');
    }
};
