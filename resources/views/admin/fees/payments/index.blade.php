@extends('admin.app')
@section('page-title', 'পেমেন্ট তালিকা')

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">থেকে:</label>
        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm mr-2">
        <label class="mr-1">পর্যন্ত:</label>
        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm mr-2">
        <select name="method" class="form-control form-control-sm mr-2">
            <option value="">সব মেথড</option>
            @foreach(['cash'=>'ক্যাশ','bkash'=>'বিকাশ','nagad'=>'নগদ','rocket'=>'রকেট','bank'=>'ব্যাংক','card'=>'কার্ড','cheque'=>'চেক'] as $k=>$v)
                <option value="{{ $k }}" @selected(request('method')==$k)>{{ $v }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> ফিল্টার</button>
        <a href="{{ route('admin.fees.payments.index') }}" class="btn btn-sm btn-secondary ml-1">রিসেট</a>
    </form>
</div>

<div class="row">
    <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>৳ {{ number_format($summary['total'], 2) }}</h3><p>মোট কালেকশন</p></div><div class="icon"><i class="fas fa-money-bill"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>{{ $summary['count'] }}</h3><p>পেমেন্ট সংখ্যা</p></div><div class="icon"><i class="fas fa-receipt"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-warning"><div class="inner"><h3>৳ {{ number_format($summary['discount'], 2) }}</h3><p>মোট ডিসকাউন্ট</p></div><div class="icon"><i class="fas fa-percent"></i></div></div></div>
    <div class="col-md-3"><div class="small-box bg-danger"><div class="inner"><h3>৳ {{ number_format($summary['fine'], 2) }}</h3><p>মোট জরিমানা</p></div><div class="icon"><i class="fas fa-gavel"></i></div></div></div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover datatable">
            <thead>
                <tr>
                    <th>রিসিট #</th>
                    <th>তারিখ</th>
                    <th>শিক্ষার্থী</th>
                    <th>ইনভয়েস #</th>
                    <th class="text-right">পরিমাণ</th>
                    <th>মেথড</th>
                    <th>অ্যাকশন</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                <tr>
                    <td><b>{{ $p->receipt_no }}</b></td>
                    <td>{{ $p->payment_date->format('d M, Y') }}</td>
                    <td>{{ $p->student->name ?? '-' }}</td>
                    <td>{{ $p->invoice->invoice_no ?? '-' }}</td>
                    <td class="text-right"><b>৳ {{ number_format($p->amount, 2) }}</b></td>
                    <td><span class="badge badge-info">{{ $p->payment_method }}</span></td>
                    <td>
                        <a href="{{ route('admin.fees.payments.receipt', $p) }}" target="_blank" class="btn btn-xs btn-info"><i class="fas fa-print"></i></a>
                        <form action="{{ route('admin.fees.payments.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('নিশ্চিত?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
