<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->text('content');
            $table->text('content_bn')->nullable();
            $table->enum('audience', ['all', 'students', 'teachers', 'guardians', 'staff'])->default('all');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->string('attachment')->nullable();
            $table->date('publish_date');
            $table->date('expire_date')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('send_sms')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
