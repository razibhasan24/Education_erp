@extends('admin.app')
@section('page-title', 'ইনভয়েস ' . $invoice->invoice_no)

@section('page-actions')
    @if($invoice->due_amount > 0)
        <a href="{{ route('admin.fees.payments.create', $invoice) }}" class="btn btn-success btn-sm">
            <i class="fas fa-hand-holding-usd"></i> পেমেন্ট নিন
        </a>
    @endif
    <a href="{{ route('admin.fees.invoices.index') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">ইনভয়েস বিস্তারিত</h3></div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <b>শিক্ষার্থী:</b> {{ $invoice->student->name ?? '-' }}<br>
                        <b>স্টুডেন্ট আইডি:</b> {{ $invoice->student->student_id ?? '-' }}<br>
                        <b>শ্রেণি:</b> {{ $invoice->schoolClass->name ?? '-' }} {{ $invoice->section->name ?? '' }}
                    </div>
                    <div class="col-md-6 text-right">
                        <b>ইনভয়েস #:</b> {{ $invoice->invoice_no }}<br>
                        <b>তারিখ:</b> {{ $invoice->invoice_date->format('d M, Y') }}<br>
                        @if($invoice->due_date)<b>ডিউ:</b> {{ $invoice->due_date->format('d M, Y') }}@endif
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead><tr><th>আইটেম</th><th class="text-right">পরিমাণ</th></tr></thead>
                    <tbody>
                        @foreach($invoice->items as $it)
                            <tr>
                                <td>{{ $it->category->name ?? '-' }}</td>
                                <td class="text-right">৳ {{ number_format($it->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr><th class="text-right">সাব-টোটাল</th><th class="text-right">৳ {{ number_format($invoice->subtotal, 2) }}</th></tr>
                        @if($invoice->discount > 0)<tr><th class="text-right">ডিসকাউন্ট</th><th class="text-right">- ৳ {{ number_format($invoice->discount, 2) }}</th></tr>@endif
                        @if($invoice->fine > 0)<tr><th class="text-right">জরিমানা</th><th class="text-right">+ ৳ {{ number_format($invoice->fine, 2) }}</th></tr>@endif
                        <tr class="bg-light"><th class="text-right">মোট</th><th class="text-right">৳ {{ number_format($invoice->total_amount, 2) }}</th></tr>
                        <tr><th class="text-right text-success">পরিশোধিত</th><th class="text-right text-success">৳ {{ number_format($invoice->paid_amount, 2) }}</th></tr>
                        <tr><th class="text-right text-danger">বকেয়া</th><th class="text-right text-danger">৳ {{ number_format($invoice->due_amount, 2) }}</th></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($invoice->payments->count())
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">পেমেন্ট ইতিহাস</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>রিসিট #</th><th>তারিখ</th><th>পরিমাণ</th><th>মেথড</th><th></th></tr></thead>
                    <tbody>
                        @foreach($invoice->payments as $p)
                        <tr>
                            <td>{{ $p->receipt_no }}</td>
                            <td>{{ $p->payment_date->format('d M, Y') }}</td>
                            <td class="text-right">৳ {{ number_format($p->amount, 2) }}</td>
                            <td><span class="badge badge-info">{{ $p->payment_method }}</span></td>
                            <td>
                                <a href="{{ route('admin.fees.payments.receipt', $p) }}" class="btn btn-xs btn-info" target="_blank"><i class="fas fa-print"></i> রিসিট</a>
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
{{-- ============ কিস্তি প্ল্যান ============ --}}
@if($invoice->installments->count())
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-alt"></i> কিস্তি প্ল্যান ({{ $invoice->installments->count() }}টি)</h3>
        <div class="card-tools">
            <form action="{{ route('admin.fees.installments.update-late-fees') }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-xs btn-warning"><i class="fas fa-sync"></i> Late Fee আপডেট</button>
            </form>
            <a href="{{ route('admin.fees.installments.create', $invoice) }}" class="btn btn-xs btn-light">
                <i class="fas fa-edit"></i> প্ল্যান পরিবর্তন
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th class="text-right">পরিমাণ</th>
                    <th>Due তারিখ</th>
                    <th class="text-right">পরিশোধিত</th>
                    <th class="text-right">Late Fee</th>
                    <th class="text-right">বাকি</th>
                    <th>স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->installments as $inst)
                <tr>
                    <td><b>{{ $inst->installment_no }}</b></td>
                    <td class="text-right">৳{{ number_format($inst->amount, 2) }}</td>
                    <td>
                        {{ $inst->due_date->format('d M, Y') }}
                        @if($inst->isOverdue())
                            <br><small class="text-danger">Overdue</small>
                        @endif
                    </td>
                    <td class="text-right text-success">৳{{ number_format($inst->paid_amount, 2) }}</td>
                    <td class="text-right text-danger">৳{{ number_format($inst->late_fee, 2) }}</td>
                    <td class="text-right"><b>৳{{ number_format($inst->remaining_amount, 2) }}</b></td>
                    <td>
                        @if($inst->status === 'paid') <span class="badge badge-success">পরিশোধিত</span>
                        @elseif($inst->status === 'overdue') <span class="badge badge-danger">Overdue</span>
                        @elseif($inst->status === 'partial') <span class="badge badge-warning">আংশিক</span>
                        @else <span class="badge badge-secondary">অপেক্ষমাণ</span> @endif
                    </td>
                    <td>
                        @if($inst->status !== 'paid')
                            <button class="btn btn-xs btn-success" data-toggle="modal" data-target="#payInst{{ $inst->id }}">
                                <i class="fas fa-money-bill"></i> পরিশোধ
                            </button>
                        @endif
                    </td>
                </tr>

                {{-- Pay Modal --}}
                @if($inst->status !== 'paid')
                <div class="modal fade" id="payInst{{ $inst->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.fees.installments.pay', $inst) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">কিস্তি #{{ $inst->installment_no }} পরিশোধ</h5>
                                    <button class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p><b>বাকি:</b> ৳{{ number_format($inst->remaining_amount, 2) }}</p>
                                    <div class="form-group">
                                        <label>পরিমাণ (৳) *</label>
                                        <input type="number" step="0.01" min="0.01" max="{{ $inst->remaining_amount }}"
                                               name="amount" value="{{ $inst->remaining_amount }}" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>তারিখ *</label>
                                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>মেথড *</label>
                                        <select name="payment_method" class="form-control" required>
                                            <option value="cash">ক্যাশ</option>
                                            <option value="bkash">বিকাশ</option>
                                            <option value="nagad">নগদ</option>
                                            <option value="bank">ব্যাংক</option>
                                            <option value="cheque">চেক</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Transaction ID</label>
                                        <input type="text" name="transaction_id" class="form-control">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-success"><i class="fas fa-save"></i> পরিশোধ করুন</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> এই ইনভয়েসে কোনো কিস্তি প্ল্যান নেই।
    <a href="{{ route('admin.fees.installments.create', $invoice) }}" class="btn btn-sm btn-primary float-right">
        <i class="fas fa-plus"></i> কিস্তি প্ল্যান তৈরি করুন
    </a>
</div>
@endif
@endsection
