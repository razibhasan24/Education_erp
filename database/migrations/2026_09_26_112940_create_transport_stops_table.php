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
        Schema::create('transport_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_route_id')->constrained('transport_routes')->onDelete('cascade');
            $table->string('name');
            $table->time('pickup_time')->nullable();
            $table->time('drop_time')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->integer('sequence')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_stops');
    }
};
