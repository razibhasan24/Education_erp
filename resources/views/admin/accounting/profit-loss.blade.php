@extends('admin.app')
@section('page-title', 'Profit & Loss Statement')

@section('page-actions')
    <button onclick="window.print()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> প্রিন্ট</button>
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">থেকে:</label>
        <input type="date" name="from" value="{{ $pl['from'] }}" class="form-control form-control-sm mr-2">
        <label class="mr-1">পর্যন্ত:</label>
        <input type="date" name="to" value="{{ $pl['to'] }}" class="form-control form-control-sm mr-2">
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

<div class="row">
    <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>৳{{ number_format($pl['total_income'], 0) }}</h3><p>মোট আয়</p></div><div class="icon"><i class="fas fa-arrow-up"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-danger"><div class="inner"><h3>৳{{ number_format($pl['total_expense'], 0) }}</h3><p>মোট ব্যয়</p></div><div class="icon"><i class="fas fa-arrow-down"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-{{ $pl['net_profit'] >= 0 ? 'info' : 'warning' }}"><div class="inner"><h3>৳{{ number_format($pl['net_profit'], 0) }}</h3><p>{{ $pl['net_profit'] >= 0 ? 'নিট মুনাফা' : 'নিট ক্ষতি' }}</p></div><div class="icon"><i class="fas fa-balance-scale"></i></div></div></div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-success">
            <div class="card-header"><h3 class="card-title">আয় (Income)</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm">
                    @foreach($pl['incomes'] as $i)
                    <tr>
                        <td>{{ $i['account']->code }} - {{ $i['account']->name }}</td>
                        <td class="text-right">৳{{ number_format($i['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-light"><th>মোট আয়</th><th class="text-right">৳{{ number_format($pl['total_income'], 2) }}</th></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-danger">
            <div class="card-header"><h3 class="card-title">ব্যয় (Expense)</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm">
                    @foreach($pl['expenses'] as $e)
                    <tr>
                        <td>{{ $e['account']->code }} - {{ $e['account']->name }}</td>
                        <td class="text-right">৳{{ number_format($e['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-light"><th>মোট ব্যয়</th><th class="text-right">৳{{ number_format($pl['total_expense'], 2) }}</th></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="callout callout-{{ $pl['net_profit'] >= 0 ? 'success' : 'danger' }}">
    <h4><b>{{ $pl['net_profit'] >= 0 ? 'নিট মুনাফা' : 'নিট ক্ষতি' }}:</b> ৳{{ number_format(abs($pl['net_profit']), 2) }}</h4>
</div>
@endsection
