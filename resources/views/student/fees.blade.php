@extends('student.app')
@section('page-title', 'আমার ফি')

@section('content')
<div class="row">
    <div class="col-md-4"><div class="small-box bg-info"><div class="inner"><h3>৳{{ number_format($summary['total'], 0) }}</h3><p>মোট বিল</p></div><div class="icon"><i class="fas fa-file-invoice"></i></div></div></div>
    <div class="col-md-4"><div class="small-box bg-success"><div class="inner"><h3>৳{{ number_format($summary['paid'], 0) }}</h3><p>পরিশোধিত</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
    <div class="col-md-4"><div class="small-box bg-danger"><div class="inner"><h3>৳{{ number_format($summary['due'], 0) }}</h3><p>বকেয়া</p></div><div class="icon"><i class="fas fa-exclamation"></i></div></div></div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ইনভয়েস #</th>
                    <th>তারিখ</th>
                    <th>মোট</th>
                    <th>পরিশোধ</th>
                    <th>বকেয়া</th>
                    <th>স্ট্যাটাস</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td><b>{{ $inv->invoice_no }}</b></td>
                    <td>{{ $inv->invoice_date->format('d M, Y') }}</td>
                    <td>৳{{ number_format($inv->total_amount, 2) }}</td>
                    <td class="text-success">৳{{ number_format($inv->paid_amount, 2) }}</td>
                    <td class="text-danger"><b>৳{{ number_format($inv->due_amount, 2) }}</b></td>
                    <td>@php $st = \App\Models\FeeInvoice::statusLabels()[$inv->status]; @endphp<span class="badge badge-{{ $st['color'] }}">{{ $st['label'] }}</span></td>
                    <td>
                        @if($inv->due_amount > 0)
                            <form action="{{ route('payment.initiate', $inv) }}" method="POST" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success"><i class="fas fa-credit-card"></i> অনলাইন পেমেন্ট</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @forelse($inv->payments as $p)
                <tr class="bg-light">
                    <td><small>রিসিট: {{ $p->receipt_no }}</small></td>
                    <td colspan="2"><small>{{ $p->payment_date->format('d M, Y') }} — {{ $p->payment_method }}</small></td>
                    <td colspan="2"><small>৳{{ number_format($p->amount, 2) }}</small></td>
                    <td colspan="2">
                        <a href="{{ route('admin.fees.payments.receipt', $p) }}" target="_blank" class="btn btn-xs btn-info"><i class="fas fa-print"></i></a>
                    </td>
                </tr>
                @empty
                @endforelse
                @empty
                <tr><td colspan="7" class="text-center text-muted">কোনো ইনভয়েস নেই</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
