@extends('admin.app')
@section('page-title', 'কিস্তি প্ল্যান — ' . $invoice->invoice_no)

@section('page-actions')
    <a href="{{ route('admin.fees.invoices.show', $invoice) }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> ফিরে যান
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">ইনভয়েস তথ্য</h3></div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>ইনভয়েস #</th><td><b>{{ $invoice->invoice_no }}</b></td></tr>
                    <tr><th>শিক্ষার্থী</th><td>{{ $invoice->student->name ?? '-' }}</td></tr>
                    <tr><th>শ্রেণি</th><td>{{ $invoice->schoolClass->name ?? '-' }} {{ $invoice->section->name ?? '' }}</td></tr>
                    <tr><th>তারিখ</th><td>{{ $invoice->invoice_date->format('d M, Y') }}</td></tr>
                    <tr><th>মোট বিল</th><td><b>৳{{ number_format($invoice->total_amount, 2) }}</b></td></tr>
                    <tr><th>পরিশোধিত</th><td class="text-success">৳{{ number_format($invoice->paid_amount, 2) }}</td></tr>
                    <tr><th>বকেয়া</th><td class="text-danger"><b>৳{{ number_format($invoice->due_amount, 2) }}</b></td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <form action="{{ route('admin.fees.installments.store', $invoice) }}" method="POST">
            @csrf
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt"></i> কিস্তি সেটআপ</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-light" onclick="addInstallment()">
                            <i class="fas fa-plus"></i> কিস্তি যোগ
                        </button>
                        <button type="button" class="btn btn-sm btn-light" onclick="splitEqually()">
                            <i class="fas fa-balance-scale"></i> সমান ভাগ
                        </button>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th width="60">#</th>
                                <th>পরিমাণ (৳)</th>
                                <th>তারিখ (Due)</th>
                                <th width="60"></th>
                            </tr>
                        </thead>
                        <tbody id="installmentsBody"></tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th class="text-right">মোট</th>
                                <th><span id="totalAmount">0.00</span></th>
                                <th colspan="2">
                                    <span id="statusBadge" class="badge badge-secondary">চেক হচ্ছে...</span>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                        <i class="fas fa-save"></i> কিস্তি প্ল্যান সংরক্ষণ
                    </button>
                    @if($invoice->installments->count())
                        <button type="button" class="btn btn-danger btn-lg" onclick="deletePlan()">
                            <i class="fas fa-trash"></i> পুরোনো প্ল্যান মুছুন
                        </button>
                    @endif
                </div>
            </div>
        </form>

        @if($invoice->installments->count())
        <div class="card card-warning">
            <div class="card-header"><h3 class="card-title">বর্তমান কিস্তি প্ল্যান</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm mb-0">
                    <thead class="thead-light">
                        <tr><th>#</th><th>পরিমাণ</th><th>Due</th><th>পরিশোধিত</th><th>Late Fee</th><th>স্ট্যাটাস</th></tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->installments as $inst)
                        <tr>
                            <td>{{ $inst->installment_no }}</td>
                            <td>৳{{ number_format($inst->amount, 2) }}</td>
                            <td>{{ $inst->due_date->format('d M, Y') }}</td>
                            <td class="text-success">৳{{ number_format($inst->paid_amount, 2) }}</td>
                            <td class="text-danger">৳{{ number_format($inst->late_fee, 2) }}</td>
                            <td>
                                @if($inst->status === 'paid') <span class="badge badge-success">পরিশোধিত</span>
                                @elseif($inst->status === 'overdue') <span class="badge badge-danger">Overdue</span>
                                @elseif($inst->status === 'partial') <span class="badge badge-warning">আংশিক</span>
                                @else <span class="badge badge-secondary">অপেক্ষমাণ</span> @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Delete Form --}}
<form id="deleteForm" action="{{ route('admin.fees.installments.destroy', $invoice) }}" method="POST" class="d-none">
    @csrf @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
const TOTAL = {{ $invoice->total_amount }};
let idx = 0;

function addInstallment() {
    const i = idx++;
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('installmentsBody').insertAdjacentHTML('beforeend', `
        <tr id="row_${i}">
            <td class="text-center"><b class="seq"></b></td>
            <td><input type="number" step="0.01" min="0" name="installments[${i}][amount]" value="0"
                       class="form-control form-control-sm amount-input" oninput="recalc()" required></td>
            <td><input type="date" name="installments[${i}][due_date]" value="${today}" class="form-control form-control-sm" required></td>
            <td class="text-center"><button type="button" class="btn btn-xs btn-danger" onclick="removeRow(${i})"><i class="fas fa-times"></i></button></td>
        </tr>
    `);
    renumber();
    recalc();
}

function removeRow(i) {
    document.getElementById('row_' + i)?.remove();
    renumber();
    recalc();
}

function renumber() {
    document.querySelectorAll('#installmentsBody tr').forEach((tr, i) => {
        tr.querySelector('.seq').innerText = i + 1;
    });
}

function recalc() {
    let total = 0;
    document.querySelectorAll('.amount-input').forEach(i => total += parseFloat(i.value || 0));
    document.getElementById('totalAmount').innerText = total.toFixed(2);

    const badge = document.getElementById('statusBadge');
    const btn = document.getElementById('submitBtn');
    const diff = TOTAL - total;

    if (Math.abs(diff) < 0.01) {
        badge.className = 'badge badge-success';
        badge.innerText = '✓ মোট ঠিক আছে';
        btn.disabled = false;
    } else if (diff > 0) {
        badge.className = 'badge badge-warning';
        badge.innerText = `বাকি: ৳${diff.toFixed(2)}`;
        btn.disabled = true;
    } else {
        badge.className = 'badge badge-danger';
        badge.innerText = `বেশি: ৳${Math.abs(diff).toFixed(2)}`;
        btn.disabled = true;
    }
}

function splitEqually() {
    const count = document.querySelectorAll('#installmentsBody tr').length;
    if (count < 1) { alert('আগে কিস্তি যোগ করুন।'); return; }
    const per = (TOTAL / count).toFixed(2);
    document.querySelectorAll('.amount-input').forEach(input => {
        input.value = per;
    });
    recalc();
}

function deletePlan() {
    if (confirm('পুরোনো কিস্তি প্ল্যান মুছে ফেলবেন?')) {
        document.getElementById('deleteForm').submit();
    }
}

// শুরুতে ৩টি কিস্তি
addInstallment();
addInstallment();
addInstallment();
</script>
@endpush