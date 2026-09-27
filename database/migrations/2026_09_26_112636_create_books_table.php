<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('isbn')->nullable();
            $table->foreignId('book_category_id')->nullable()->constrained('book_categories')->nullOnDelete();
            $table->string('edition')->nullable();
            $table->year('published_year')->nullable();
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->string('shelf_no')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
