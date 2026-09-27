@extends('admin.app')
@section('page-title', 'Balance Sheet')

@section('page-actions')
    <button onclick="window.print()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> প্রিন্ট</button>
@endsection

@section('content')
<div class="card card-primary">
    <form method="GET" class="card-body form-inline">
        <label class="mr-1">As of:</label>
        <input type="date" name="as_of" value="{{ $bs['as_of'] }}" class="form-control form-control-sm mr-2">
        <button class="btn btn-sm btn-info"><i class="fas fa-search"></i> দেখান</button>
    </form>
</div>

<div class="alert alert-{{ $bs['balanced'] ? 'success' : 'warning' }}">
    <i class="fas fa-balance-scale"></i>
    <b>Assets:</b> ৳{{ number_format($bs['total_assets'], 2) }} |
    <b>Liabilities + Equity:</b> ৳{{ number_format($bs['total_liabilities'] + $bs['total_equity'], 2) }}
    @if($bs['balanced']) <span class="badge badge-success ml-2">✓ ব্যালেন্সড</span> @endif
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Assets (সম্পদ)</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm">
                    @foreach($bs['assets'] as $a)
                    <tr>
                        <td>{{ $a['account']->code }} - {{ $a['account']->name }}</td>
                        <td class="text-right">৳{{ number_format($a['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-light"><th>মোট Assets</th><th class="text-right">৳{{ number_format($bs['total_assets'], 2) }}</th></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-danger">
            <div class="card-header"><h3 class="card-title">Liabilities (দায়)</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm">
                    @foreach($bs['liabilities'] as $l)
                    <tr>
                        <td>{{ $l['account']->code }} - {{ $l['account']->name }}</td>
                        <td class="text-right">৳{{ number_format($l['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="bg-light"><th>মোট Liabilities</th><th class="text-right">৳{{ number_format($bs['total_liabilities'], 2) }}</th></tr>
                </table>
            </div>
        </div>

        <div class="card card-info">
            <div class="card-header"><h3 class="card-title">Equity (মালিকানা)</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm">
                    @foreach($bs['equity'] as $e)
                    <tr>
                        <td>{{ $e['account']->code }} - {{ $e['account']->name }}</td>
                        <td class="text-right">৳{{ number_format($e['amount'], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td><b>Retained Earnings (Net Profit)</b></td>
                        <td class="text-right">৳{{ number_format($bs['net_profit'], 2) }}</td>
                    </tr>
                    <tr class="bg-light"><th>মোট Equity</th><th class="text-right">৳{{ number_format($bs['total_equity'], 2) }}</th></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
