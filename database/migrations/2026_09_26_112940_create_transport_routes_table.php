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
       Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('start_point')->nullable();
            $table->string('end_point')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_routes');
    }
};
