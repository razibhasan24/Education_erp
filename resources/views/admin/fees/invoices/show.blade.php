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
@endsection
