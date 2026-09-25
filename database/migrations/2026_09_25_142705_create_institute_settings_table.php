<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institute_settings', function (Blueprint $table) {
            $table->id();
            $table->string('institute_name');
            $table->string('institute_name_bn')->nullable();
            $table->enum('institute_type', ['school', 'college', 'madrasha', 'coaching', 'training'])->default('school');
            $table->string('eiin')->nullable(); // বাংলাদেশে EIIN নম্বর
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('session_year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institute_settings');
    }
};
