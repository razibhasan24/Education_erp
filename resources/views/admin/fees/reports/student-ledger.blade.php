@extends('admin.app')
@section('page-title', $student->name . ' — ফি লেজার')

@section('content')
<div class="row">
    <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>৳ {{ number_format($totalBilled, 2) }}</h3><p>মোট বিল</p></div><div class="icon"><i class="fas fa-file-invoice"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>৳ {{ number_format($totalPaid, 2) }}</h3><p>মোট পরিশোধ</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-danger"><div class="inner"><h3>৳ {{ number_format($totalDue, 2) }}</h3><p>মোট বকেয়া</p></div><div class="icon"><i class="fas fa-times"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-primary"><div class="inner"><h3>{{ $invoices->count() }}</h3><p>মোট ইনভয়েস</p></div><div class="icon"><i class="fas fa-list"></i></div></div></div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead><tr><th>ইনভয়েস #</th><th>তারিখ</th><th>মাস/বছর</th><th class="text-right">মোট</th><th class="text-right">পরিশোধ</th><th class="text-right">বকেয়া</th><th>স্ট্যাটাস</th><th></th></tr></thead>
            <tbody>
                @foreach($invoices as $inv)
                <tr>
                    <td>{{ $inv->invoice_no }}</td>
                    <td>{{ $inv->invoice_date->format('d M, Y') }}</td>
                    <td>{{ $inv->month ? str_pad($inv->month,2,'0',STR_PAD_LEFT).'/'.$inv->year : '-' }}</td>
                    <td class="text-right">৳ {{ number_format($inv->total_amount, 2) }}</td>
                    <td class="text-right text-success">৳ {{ number_format($inv->paid_amount, 2) }}</td>
                    <td class="text-right text-danger">৳ {{ number_format($inv->due_amount, 2) }}</td>
                    <td>@php $st = \App\Models\FeeInvoice::statusLabels()[$inv->status]; @endphp<span class="badge badge-{{ $st['color'] }}">{{ $st['label'] }}</span></td>
                    <td><a href="{{ route('admin.fees.invoices.show', $inv) }}" class="btn btn-xs btn-info"><i class="fas fa-eye"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
