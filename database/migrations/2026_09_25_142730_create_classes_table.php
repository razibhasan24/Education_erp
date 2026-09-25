<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // যেমন: Six, Seven, দাখিল প্রথম
            $table->string('name_bn')->nullable();
            $table->string('code')->nullable();
            $table->integer('numeric_value')->default(1); // ক্রম নির্ধারণের জন্য
            $table->enum('education_level', ['primary', 'secondary', 'higher_secondary', 'madrasha', 'other'])->default('secondary');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
