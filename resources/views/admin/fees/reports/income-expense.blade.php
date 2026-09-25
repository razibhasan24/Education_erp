@extends('admin.app')
@section('page-title', 'ইনকাম-এক্সপেন্স রিপোর্ট')

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">থেকে:</label>
        <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm mr-2">
        <label class="mr-1">পর্যন্ত:</label>
        <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm mr-2">
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

<div class="row">
    <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>৳ {{ number_format($totalIncome, 2) }}</h3><p>মোট আয়</p></div><div class="icon"><i class="fas fa-arrow-up"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-danger"><div class="inner"><h3>৳ {{ number_format($totalExpense, 2) }}</h3><p>মোট ব্যয়</p></div><div class="icon"><i class="fas fa-arrow-down"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>৳ {{ number_format($totalIncome - $totalExpense, 2) }}</h3><p>নিট মুনাফা</p></div><div class="icon"><i class="fas fa-balance-scale"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-warning"><div class="inner"><h3>৳ {{ number_format($totalDiscount, 2) }}</h3><p>ডিসকাউন্ট</p></div><div class="icon"><i class="fas fa-percent"></i></div></div></div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">মাসিক আয়</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>মাস</th><th class="text-right">আয়</th></tr></thead>
                    <tbody>
                        @foreach($monthlyIncome as $m)
                        <tr><td>{{ $m->year }}-{{ str_pad($m->month,2,'0',STR_PAD_LEFT) }}</td><td class="text-right">৳ {{ number_format($m->total, 2) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-danger">
            <div class="card-header"><h3 class="card-title">মাসিক ব্যয়</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>মাস</th><th class="text-right">ব্যয়</th></tr></thead>
                    <tbody>
                        @foreach($monthlyExpense as $m)
                        <tr><td>{{ $m->year }}-{{ str_pad($m->month,2,'0',STR_PAD_LEFT) }}</td><td class="text-right">৳ {{ number_format($m->total, 2) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
