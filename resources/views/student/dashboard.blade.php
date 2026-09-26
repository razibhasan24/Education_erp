@extends('student.app')
@section('page-title', 'আমার ড্যাশবোর্ড')

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile text-center">
                <img class="profile-user-img img-fluid img-circle"
                     src="{{ $student->photo ? asset('storage/'.$student->photo) : 'https://ui-avatars.com/api/?name='.urlencode($student->name).'&size=150' }}"
                     style="width:110px;height:110px;">
                <h4 class="mt-2">{{ $student->name }}</h4>
                <p class="text-muted mb-1">{{ $student->student_id }}</p>
                <p>{{ $student->schoolClass->name ?? '-' }} @if($student->section) ({{ $student->section->name }}) @endif</p>
                <p class="text-muted">রোল: {{ $student->roll_number ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="row">
            <div class="col-md-3 col-6"><div class="small-box bg-success"><div class="inner"><h3>{{ $stats['present'] }}</h3><p>উপস্থিত (এই মাস)</p></div><div class="icon"><i class="fas fa-user-check"></i></div></div></div>
            <div class="col-md-3 col-6"><div class="small-box bg-danger"><div class="inner"><h3>{{ $stats['absent'] }}</h3><p>অনুপস্থিত</p></div><div class="icon"><i class="fas fa-user-times"></i></div></div></div>
            <div class="col-md-3 col-6"><div class="small-box bg-info"><div class="inner"><h3>৳{{ number_format($feeSummary['paid'], 0) }}</h3><p>পরিশোধিত</p></div><div class="icon"><i class="fas fa-check"></i></div></div></div>
            <div class="col-md-3 col-6"><div class="small-box bg-warning"><div class="inner"><h3>৳{{ number_format($feeSummary['due'], 0) }}</h3><p>বকেয়া</p></div><div class="icon"><i class="fas fa-exclamation"></i></div></div></div>
        </div>

        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">সাম্প্রতিক ইনভয়েস</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>ইনভয়েস #</th><th>তারিখ</th><th>মোট</th><th>বকেয়া</th><th>স্ট্যাটাস</th></tr></thead>
                    <tbody>
                        @forelse($recentInvoices as $inv)
                        <tr>
                            <td>{{ $inv->invoice_no }}</td>
                            <td>{{ $inv->invoice_date->format('d M, Y') }}</td>
                            <td>৳{{ number_format($inv->total_amount, 2) }}</td>
                            <td class="text-danger">৳{{ number_format($inv->due_amount, 2) }}</td>
                            <td>@php $st = \App\Models\FeeInvoice::statusLabels()[$inv->status]; @endphp<span class="badge badge-{{ $st['color'] }}">{{ $st['label'] }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-muted text-center">কোনো ইনভয়েস নেই</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
