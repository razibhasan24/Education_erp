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
    Schema::create('academic_years', function (Blueprint $table) {
        $table->id();
        $table->foreignId('institution_id')->constrained()->onDelete('cascade');
        $table->string('year'); // e.g., "2025" or "2025-2026"
        $table->date('start_date');
        $table->date('end_date');
        $table->boolean('is_current')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};