<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('bulksmsbd'); // bulksmsbd, alphasms, custom
            $table->string('api_key')->nullable();
            $table->string('sender_id')->nullable();
            $table->string('api_url')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('notify_attendance')->default(false);
            $table->boolean('notify_result')->default(false);
            $table->boolean('notify_due')->default(false);
            $table->string('attendance_template')->nullable();
            $table->string('result_template')->nullable();
            $table->string('due_template')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};