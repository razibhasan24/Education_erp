<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();       // যেমন: 1000, 4100
            $table->string('name');                     // Cash in Hand
            $table->string('name_bn')->nullable();
            $table->enum('type', ['asset', 'liability', 'equity', 'income', 'expense']);
            $table->enum('sub_type', [
                'current_asset', 'fixed_asset', 'bank', 'cash', 'receivable',
                'current_liability', 'long_term_liability', 'payable',
                'capital', 'retained_earnings',
                'direct_income', 'indirect_income',
                'direct_expense', 'indirect_expense',
            ])->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->boolean('is_system')->default(false);  // সিস্টেম-জেনারেটেড (ডিলিট করা যাবে না)
            $table->boolean('is_active')->default(true);
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->enum('opening_type', ['debit', 'credit'])->default('debit');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
