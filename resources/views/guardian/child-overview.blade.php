@extends('student.app')
@section('page-title', $student->name . ' — ওভারভিউ')

@section('page-actions')
    <a href="{{ route('guardian.dashboard') }}" class="btn btn-secondary btn-sm">ফিরে যান</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>{{ $stats['present'] }}</h3><p>উপস্থিত (এই মাস)</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-danger"><div class="inner"><h3>{{ $stats['absent'] }}</h3><p>অনুপস্থিত</p></div><div class="icon"><i class="fas fa-times"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>৳{{ number_format($feeSummary['paid'], 0) }}</h3><p>পরিশোধিত</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-warning"><div class="inner"><h3>৳{{ number_format($feeSummary['due'], 0) }}</h3><p>বকেয়া</p></div><div class="icon"><i class="fas fa-exclamation"></i></div></div></div>
</div>

@if($lastResult && $lastExam)
<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">
            {{ $lastExam->name }} — GPA: <b>{{ number_format($lastResult['gpa'], 2) }}</b>
            গ্রেড: <b>{{ $lastResult['grade'] }}</b>
        </h3>
    </div>
    <div class="card-body">
        <p>ফলাফল: <span class="badge badge-{{ $lastResult['failed'] ? 'danger' : 'success' }}">{{ $lastResult['failed'] ? 'ফেল' : 'পাস' }}</span></p>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header"><h3 class="card-title">সাম্প্রতিক ইনভয়েস</h3></div>
    <div class="card-body table-responsive">
        <table class="table table-sm">
            <thead><tr><th>ইনভয়েস #</th><th>তারিখ</th><th>মোট</th><th>বকেয়া</th><th>স্ট্যাটাস</th></tr></thead>
            <tbody>
                @foreach($recentInvoices as $inv)
                <tr>
                    <td>{{ $inv->invoice_no }}</td>
                    <td>{{ $inv->invoice_date->format('d M, Y') }}</td>
                    <td>৳{{ number_format($inv->total_amount, 2) }}</td>
                    <td class="text-danger">৳{{ number_format($inv->due_amount, 2) }}</td>
                    <td>@php $st = \App\Models\FeeInvoice::statusLabels()[$inv->status]; @endphp<span class="badge badge-{{ $st['color'] }}">{{ $st['label'] }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
