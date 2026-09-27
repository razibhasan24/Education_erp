<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no')->unique();     // JE-2025-0001
            $table->date('entry_date');
            $table->string('reference')->nullable();     // Invoice/Payment reference
            $table->string('narration');                 // বিবরণ
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->enum('type', [
                'manual', 'fee_payment', 'expense', 'salary',
                'opening', 'adjustment', 'refund', 'scholarship', 'other'
            ])->default('manual');
            $table->string('source_type')->nullable();   // FeePayment, Expense etc.
            $table->unsignedBigInteger('source_id')->nullable();
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('posted');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['entry_date', 'status']);
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
