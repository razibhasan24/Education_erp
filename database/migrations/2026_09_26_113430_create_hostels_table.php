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
       Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['boys', 'girls'])->default('boys');
            $table->string('warden_name')->nullable();
            $table->string('warden_phone')->nullable();
            $table->string('address')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hostels');
    }
};
