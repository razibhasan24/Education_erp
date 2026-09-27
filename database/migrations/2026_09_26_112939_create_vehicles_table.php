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
       Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_no')->unique();
            $table->integer('capacity')->default(40);
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('helper_name')->nullable();
            $table->string('helper_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
