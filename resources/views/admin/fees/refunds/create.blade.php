@extends('admin.app')
@section('page-title', 'নতুন Refund')

@section('page-actions')
    <a href="{{ route('admin.fees.refunds.index') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
@endsection

@section('content')
<form action="{{ route('admin.fees.refunds.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header"><h3 class="card-title">Refund তথ্য</h3></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>শিক্ষার্থী *</label>
                            <select name="student_id" id="student_id" class="form-control select2" required onchange="loadPayments()">
                                <option value="">-- নির্বাচন --</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}" @selected($selectedStudent?->id == $s->id)>
                                        {{ $s->name }} ({{ $s->student_id }}) — {{ $s->schoolClass->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>সংশ্লিষ্ট পেমেন্ট</label>
                            <select name="fee_payment_id" id="payment_id" class="form-control select2">
                                <option value="">-- ঐচ্ছিক --</option>
                                @foreach($payments as $p)
                                    <option value="{{ $p->id }}" data-amount="{{ $p->amount }}">
                                        {{ $p->receipt_no }} — ৳{{ number_format($p->amount, 2) }} ({{ $p->payment_date->format('d M, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">নির্দিষ্ট পেমেন্ট নির্বাচন করলে তার বিপরীতে refund হবে।</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>পরিমাণ (৳) *</label>
                            <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Refund তারিখ *</label>
                            <input type="date" name="refund_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Refund মেথড *</label>
                            <select name="refund_method" class="form-control" required>
                                <option value="cash">ক্যাশ</option>
                                <option value="bkash">বিকাশ</option>
                                <option value="nagad">নগদ</option>
                                <option value="bank">ব্যাংক</option>
                                <option value="cheque">চেক</option>
                                <option value="adjustment">Adjustment (পরের ফি তে সমন্বয়)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Transaction ID</label>
                            <input type="text" name="transaction_id" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                            <label>কারণ *</label>
                            <textarea name="reason" class="form-control" rows="3" required placeholder="যেমন: ভর্তি বাতিল, ডাবল পেমেন্ট, অপ্রয়োজনীয় ফি"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header"><h3 class="card-title">নির্দেশনা</h3></div>
                <div class="card-body">
                    <div class="alert alert-info small mb-0">
                        <ol class="pl-3 mb-0">
                            <li>শিক্ষার্থী নির্বাচন করুন</li>
                            <li>সংশ্লিষ্ট পেমেন্ট থাকলে নির্বাচন করুন (ঐচ্ছিক)</li>
                            <li>Refund পরিমাণ লিখুন</li>
                            <li>কারণ স্পষ্টভাবে লিখুন</li>
                            <li>সংরক্ষণের পর <b>Approve</b> → তারপর <b>Mark as Paid</b> করুন</li>
                        </ol>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary btn-block btn-lg"><i class="fas fa-save"></i> Refund জমা দিন</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
function loadPayments() {
    const sid = document.getElementById('student_id').value;
    const sel = document.getElementById('payment_id');
    sel.innerHTML = '<option value="">-- ঐচ্ছিক --</option>';
    if (!sid) return;
    fetch(`{{ url('admin/api/student-payments') }}?student_id=${sid}`)
        .then(r => r.json())
        .then(data => data.forEach(p => {
            sel.innerHTML += `<option value="${p.id}" data-amount="${p.amount}">
                ${p.receipt_no} — ৳${p.amount} (${p.payment_date})
            </option>`;
        }));
}

document.getElementById('payment_id')?.addEventListener('change', function () {
    const amount = this.options[this.selectedIndex].dataset.amount;
    if (amount) document.getElementById('amount').value = amount;
});
</script>
@endpush