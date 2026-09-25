@extends('admin.app')
@section('page-title', 'পেমেন্ট নিন — ' . $invoice->invoice_no)

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">ইনভয়েস সারসংক্ষেপ</h3></div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>শিক্ষার্থী</th><td>{{ $invoice->student->name }}</td></tr>
                    <tr><th>স্টুডেন্ট আইডি</th><td>{{ $invoice->student->student_id }}</td></tr>
                    <tr><th>শ্রেণি</th><td>{{ $invoice->schoolClass->name ?? '-' }} {{ $invoice->section->name ?? '' }}</td></tr>
                    <tr><th>ইনভয়েস #</th><td>{{ $invoice->invoice_no }}</td></tr>
                    <tr><th>মোট</th><td>৳ {{ number_format($invoice->total_amount, 2) }}</td></tr>
                    <tr><th>পরিশোধিত</th><td>৳ {{ number_format($invoice->paid_amount, 2) }}</td></tr>
                    <tr class="bg-light"><th>বকেয়া</th><th class="text-danger">৳ {{ number_format($invoice->due_amount, 2) }}</th></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">পেমেন্ট তথ্য</h3></div>
            <form action="{{ route('admin.fees.payments.store', $invoice) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>পেমেন্ট তারিখ *</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>পরিমাণ (৳) *</label>
                        <input type="number" step="0.01" min="0.01" max="{{ $invoice->due_amount }}"
                               name="amount" value="{{ $invoice->due_amount }}" class="form-control" required>
                        <small class="text-muted">সর্বোচ্চ: ৳ {{ number_format($invoice->due_amount, 2) }}</small>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group"><label>ডিসকাউন্ট</label><input type="number" step="0.01" name="discount" value="0" class="form-control"></div>
                        <div class="col-6 form-group"><label>জরিমানা</label><input type="number" step="0.01" name="fine" value="0" class="form-control"></div>
                    </div>
                    <div class="form-group">
                        <label>পেমেন্ট মেথড *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash">ক্যাশ</option>
                            <option value="bkash">বিকাশ</option>
                            <option value="nagad">নগদ</option>
                            <option value="rocket">রকেট</option>
                            <option value="bank">ব্যাংক</option>
                            <option value="card">কার্ড</option>
                            <option value="cheque">চেক</option>
                        </select>
                    </div>
                    <div class="form-group"><label>ট্রানজেকশন আইডি</label><input type="text" name="transaction_id" class="form-control"></div>
                    <div class="form-group"><label>মন্তব্য</label><textarea name="remarks" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success btn-lg btn-block"><i class="fas fa-hand-holding-usd"></i> পেমেন্ট সংরক্ষণ ও রিসিট</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
