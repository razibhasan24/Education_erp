<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ===== ASSETS =====
            ['code' => '1000', 'name' => 'Cash in Hand',              'name_bn' => 'হাতে নগদ',              'type' => 'asset',     'sub_type' => 'cash',            'is_system' => true],
            ['code' => '1010', 'name' => 'Bank Account',              'name_bn' => 'ব্যাংক অ্যাকাউন্ট',      'type' => 'asset',     'sub_type' => 'bank',            'is_system' => true],
            ['code' => '1020', 'name' => 'bKash Account',             'name_bn' => 'বিকাশ অ্যাকাউন্ট',      'type' => 'asset',     'sub_type' => 'bank',            'is_system' => true],
            ['code' => '1030', 'name' => 'Nagad Account',             'name_bn' => 'নগদ অ্যাকাউন্ট',        'type' => 'asset',     'sub_type' => 'bank',            'is_system' => true],
            ['code' => '1100', 'name' => 'Accounts Receivable',       'name_bn' => 'প্রাপ্য হিসাব',          'type' => 'asset',     'sub_type' => 'receivable',      'is_system' => true],
            ['code' => '1200', 'name' => 'Furniture & Fixtures',      'name_bn' => 'আসবাবপত্র',             'type' => 'asset',     'sub_type' => 'fixed_asset'],
            ['code' => '1210', 'name' => 'Equipment',                 'name_bn' => 'যন্ত্রপাতি',             'type' => 'asset',     'sub_type' => 'fixed_asset'],
            ['code' => '1220', 'name' => 'Library Books',             'name_bn' => 'লাইব্রেরি বই',           'type' => 'asset',     'sub_type' => 'fixed_asset'],
            ['code' => '1230', 'name' => 'Vehicles',                  'name_bn' => 'যানবাহন',                'type' => 'asset',     'sub_type' => 'fixed_asset'],
            ['code' => '1240', 'name' => 'Building',                  'name_bn' => 'ভবন',                   'type' => 'asset',     'sub_type' => 'fixed_asset'],
            ['code' => '1250', 'name' => 'Land',                      'name_bn' => 'জমি',                   'type' => 'asset',     'sub_type' => 'fixed_asset'],

            // ===== LIABILITIES =====
            ['code' => '2000', 'name' => 'Accounts Payable',          'name_bn' => 'প্রদেয় হিসাব',          'type' => 'liability', 'sub_type' => 'payable',         'is_system' => true],
            ['code' => '2010', 'name' => 'Salary Payable',            'name_bn' => 'প্রদেয় বেতন',           'type' => 'liability', 'sub_type' => 'payable',         'is_system' => true],
            ['code' => '2020', 'name' => 'Tax Payable',               'name_bn' => 'প্রদেয় কর',             'type' => 'liability', 'sub_type' => 'current_liability'],
            ['code' => '2030', 'name' => 'Provident Fund Payable',    'name_bn' => 'প্রভিডেন্ট ফান্ড',       'type' => 'liability', 'sub_type' => 'long_term_liability'],
            ['code' => '2040', 'name' => 'Loan Payable',              'name_bn' => 'প্রদেয় ঋণ',             'type' => 'liability', 'sub_type' => 'long_term_liability'],
            ['code' => '2050', 'name' => 'Advance Fee Received',      'name_bn' => 'অগ্রিম ফি প্রাপ্ত',      'type' => 'liability', 'sub_type' => 'current_liability'],

            // ===== EQUITY =====
            ['code' => '3000', 'name' => 'Capital',                   'name_bn' => 'মূলধন',                 'type' => 'equity',    'sub_type' => 'capital',         'is_system' => true],
            ['code' => '3010', 'name' => 'Retained Earnings',         'name_bn' => 'সঞ্চিত আয়',             'type' => 'equity',    'sub_type' => 'retained_earnings', 'is_system' => true],
            ['code' => '3020', 'name' => 'Donations',                 'name_bn' => 'অনুদান',                'type' => 'equity',    'sub_type' => 'capital'],

            // ===== INCOME =====
            ['code' => '4000', 'name' => 'Tuition Fee Income',        'name_bn' => 'বেতন ফি আয়',            'type' => 'income',    'sub_type' => 'direct_income',   'is_system' => true],
            ['code' => '4010', 'name' => 'Admission Fee Income',      'name_bn' => 'ভর্তি ফি আয়',           'type' => 'income',    'sub_type' => 'direct_income',   'is_system' => true],
            ['code' => '4020', 'name' => 'Exam Fee Income',           'name_bn' => 'পরীক্ষার ফি আয়',        'type' => 'income',    'sub_type' => 'direct_income',   'is_system' => true],
            ['code' => '4030', 'name' => 'Transport Fee Income',      'name_bn' => 'ট্রান্সপোর্ট ফি আয়',    'type' => 'income',    'sub_type' => 'direct_income'],
            ['code' => '4040', 'name' => 'Hostel Fee Income',         'name_bn' => 'হোস্টেল ফি আয়',         'type' => 'income',    'sub_type' => 'direct_income'],
            ['code' => '4050', 'name' => 'Library Fee Income',        'name_bn' => 'লাইব্রেরি ফি আয়',       'type' => 'income',    'sub_type' => 'direct_income'],
            ['code' => '4060', 'name' => 'Form Fee Income',           'name_bn' => 'ফর্ম ফি আয়',            'type' => 'income',    'sub_type' => 'direct_income'],
            ['code' => '4070', 'name' => 'Certificate Fee Income',    'name_bn' => 'সার্টিফিকেট ফি আয়',     'type' => 'income',    'sub_type' => 'direct_income'],
            ['code' => '4900', 'name' => 'Other Income',              'name_bn' => 'অন্যান্য আয়',           'type' => 'income',    'sub_type' => 'indirect_income', 'is_system' => true],
            ['code' => '4910', 'name' => 'Donation Income',           'name_bn' => 'অনুদান আয়',             'type' => 'income',    'sub_type' => 'indirect_income'],
            ['code' => '4920', 'name' => 'Bank Interest Income',      'name_bn' => 'ব্যাংক সুদ আয়',          'type' => 'income',    'sub_type' => 'indirect_income'],

            // ===== EXPENSES =====
            ['code' => '5000', 'name' => 'Salary Expense',            'name_bn' => 'বেতন ব্যয়',             'type' => 'expense',   'sub_type' => 'direct_expense',   'is_system' => true],
            ['code' => '5010', 'name' => 'Rent Expense',              'name_bn' => 'ভাড়া ব্যয়',            'type' => 'expense',   'sub_type' => 'direct_expense'],
            ['code' => '5020', 'name' => 'Utility Expense',           'name_bn' => 'ইউটিলিটি ব্যয়',          'type' => 'expense',   'sub_type' => 'direct_expense'],
            ['code' => '5030', 'name' => 'Electricity Bill',          'name_bn' => 'বিদ্যুৎ বিল',            'type' => 'expense',   'sub_type' => 'direct_expense'],
            ['code' => '5040', 'name' => 'Water Bill',                'name_bn' => 'পানি বিল',               'type' => 'expense',   'sub_type' => 'direct_expense'],
            ['code' => '5050', 'name' => 'Internet & Phone Bill',     'name_bn' => 'ইন্টারনেট ও ফোন বিল',    'type' => 'expense',   'sub_type' => 'direct_expense'],
            ['code' => '5060', 'name' => 'Stationery',                'name_bn' => 'স্টেশনারি',              'type' => 'expense',   'sub_type' => 'direct_expense'],
            ['code' => '5070', 'name' => 'Maintenance & Repairs',     'name_bn' => 'রক্ষণাবেক্ষণ',           'type' => 'expense',   'sub_type' => 'direct_expense'],
            ['code' => '5080', 'name' => 'Transport Expense',         'name_bn' => 'পরিবহন ব্যয়',            'type' => 'expense',   'sub_type' => 'direct_expense'],
            ['code' => '5090', 'name' => 'Marketing Expense',         'name_bn' => 'মার্কেটিং ব্যয়',         'type' => 'expense',   'sub_type' => 'indirect_expense'],
            ['code' => '5100', 'name' => 'Bank Charge',               'name_bn' => 'ব্যাংক চার্জ',           'type' => 'expense',   'sub_type' => 'indirect_expense'],
            ['code' => '5110', 'name' => 'Depreciation Expense',      'name_bn' => 'অবচয় ব্যয়',             'type' => 'expense',   'sub_type' => 'indirect_expense'],
            ['code' => '5120', 'name' => 'Fine & Penalty',            'name_bn' => 'জরিমানা',                'type' => 'expense',   'sub_type' => 'indirect_expense'],
            ['code' => '5900', 'name' => 'Miscellaneous Expense',     'name_bn' => 'বিবিধ ব্যয়',            'type' => 'expense',   'sub_type' => 'indirect_expense', 'is_system' => true],
        ];

        foreach ($accounts as $a) {
            Account::firstOrCreate(['code' => $a['code']], $a);
        }

        $this->command->info('✅ Chart of Accounts তৈরি হয়েছে — ' . count($accounts) . 'টি অ্যাকাউন্ট।');
    }
}
