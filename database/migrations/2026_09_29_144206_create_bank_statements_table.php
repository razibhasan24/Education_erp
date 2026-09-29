<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->date('transaction_date');
            $table->string('description');
            $table->string('reference')->nullable();       // Bank ref/cheque no
            $table->string('transaction_id')->nullable();  // bKash/Nagad TrxID
            $table->decimal('debit', 15, 2)->default(0);   // Withdrawal
            $table->decimal('credit', 15, 2)->default(0);  // Deposit
            $table->decimal('balance', 15, 2)->default(0); // Running balance
            $table->enum('status', ['unmatched', 'matched', 'ignored'])->default('unmatched');
            $table->foreignId('matched_journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('matched_payment_id')->nullable()->constrained('fee_payments')->nullOnDelete();
            $table->text('note')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['bank_account_id', 'transaction_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statements');
    }
};