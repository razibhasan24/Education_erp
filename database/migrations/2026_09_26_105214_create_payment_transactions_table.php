<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // আমাদের সিস্টেমের ID
            $table->string('gateway_transaction_id')->nullable(); // SSLCommerz এর val_id
            $table->foreignId('fee_invoice_id')->constrained('fee_invoices')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('gateway')->default('sslcommerz'); // sslcommerz, bkash
            $table->string('payment_method')->nullable(); // bKash, Card, Nagad (gateway থেকে)
            $table->enum('status', ['initiated', 'pending', 'success', 'failed', 'cancelled'])->default('initiated');
            $table->text('gateway_response')->nullable();
            $table->string('ip_address')->nullable();
            $table->foreignId('fee_payment_id')->nullable()->constrained('fee_payments')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
